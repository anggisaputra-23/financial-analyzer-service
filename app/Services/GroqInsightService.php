<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GroqInsightService
{
    /**
     * @param array<string, mixed> $analysisData
     * @return array{insight: string, model: ?string, prompt: string}
     */
    public function generateInsight(array $analysisData): array
    {
        $prompt = 'Berdasarkan data keuangan berikut, berikan insight singkat dan saran: '
            .json_encode($analysisData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $apiKey = trim((string) config('services.groq.api_key'));
        $model = trim((string) config('services.groq.model'));

        if ($apiKey === '') {
            return [
                'insight' => 'GROQ_API_KEY belum dikonfigurasi. Tambahkan API key untuk mendapatkan insight AI dari Groq.',
                'model' => null,
                'prompt' => $prompt,
            ];
        }

        $baseUrl = rtrim((string) config('services.groq.base_url', 'https://api.groq.com/openai/v1'), '/');
        $timeout = (int) config('services.groq.timeout', 15);

        try {
            $response = Http::timeout($timeout)
                ->acceptJson()
                ->withToken($apiKey)
                ->post($baseUrl.'/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Anda adalah analis keuangan yang memberi insight singkat, jelas, dan actionable.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.4,
                    'max_tokens' => 220,
                ]);

            if ($response->failed()) {
                Log::warning('Groq request failed.', [
                    'status' => $response->status(),
                    'response_body' => mb_substr($response->body(), 0, 600),
                ]);

                return $this->fallbackResponse($analysisData, $model, $prompt);
            }

            $content = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

            return [
                'insight' => $content !== '' ? $content : $this->fallbackInsight($analysisData),
                'model' => $model !== '' ? $model : null,
                'prompt' => $prompt,
            ];
        } catch (Throwable $exception) {
            Log::error('Groq request exception.', [
                'message' => $exception->getMessage(),
            ]);

            return $this->fallbackResponse($analysisData, $model, $prompt);
        }
    }

    /**
     * @param array<string, mixed> $analysisData
     * @return array{insight: string, model: ?string, prompt: string}
     */
    private function fallbackResponse(array $analysisData, string $model, string $prompt): array
    {
        return [
            'insight' => $this->fallbackInsight($analysisData),
            'model' => $model !== '' ? $model : null,
            'prompt' => $prompt,
        ];
    }

    /**
     * @param array<string, mixed> $analysisData
     */
    private function fallbackInsight(array $analysisData): string
    {
        $totalExpense = (float) ($analysisData['total_expense'] ?? 0);
        $topCategory = (string) ($analysisData['top_category'] ?? '');
        $categoryBreakdown = (array) ($analysisData['category_breakdown'] ?? []);

        if ($totalExpense <= 0) {
            return 'Belum ada pengeluaran tercatat. Fokus pertahankan arus kas positif sambil menyiapkan rencana tabungan bulanan.';
        }

        if ($topCategory !== '' && isset($categoryBreakdown[$topCategory])) {
            $portion = (float) $categoryBreakdown[$topCategory];

            return sprintf(
                'Pengeluaran terbesar ada di kategori %s (%.2f%% dari total pengeluaran). Pertimbangkan menetapkan limit mingguan agar pengeluaran lebih terkontrol.',
                $topCategory,
                $portion
            );
        }

        return 'Pantau rasio pemasukan dan pengeluaran setiap minggu, lalu tentukan target pengurangan biaya untuk kategori yang paling sering muncul.';
    }
}
