<?php

namespace App\Services;

use App\Models\AnalysisReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialAnalysisService
{
    private const TYPE_INCOME = 'income';
    private const TYPE_EXPENSE = 'expense';
    private const INSIGHT_PROVIDER = 'groq';

    public function __construct(private readonly GroqInsightService $groqInsightService)
    {
    }

    /**
     * @param array<int, array{amount: mixed, category: mixed, type: mixed}> $transactions
     * @return array{total_income: float, total_expense: float, transaction_count: int, top_category: ?string, category_breakdown: array<string, float>, insight: string}
     */
    public function analyze(int $userId, array $transactions): array
    {
        $normalizedTransactions = $this->normalizeTransactions($transactions);
        $metrics = $this->calculateMetrics($normalizedTransactions);

        $analysisData = [
            'user_id' => $userId,
            'total_income' => $metrics['total_income'],
            'total_expense' => $metrics['total_expense'],
            'transaction_count' => $metrics['transaction_count'],
            'top_category' => $metrics['top_category'],
            'category_breakdown' => $metrics['category_breakdown'],
        ];

        return DB::transaction(function () use ($analysisData, $metrics, $normalizedTransactions, $userId): array {
            $analysisReport = $this->persistAnalysisReport($userId, $normalizedTransactions, $metrics);

            $this->persistCategoryBreakdowns(
                $analysisReport,
                $metrics['expense_by_category'],
                $metrics['category_breakdown']
            );

            $insightResult = $this->groqInsightService->generateInsight($analysisData);

            $analysisReport->aiInsight()->create([
                'provider' => self::INSIGHT_PROVIDER,
                'model' => $insightResult['model'],
                'prompt' => $insightResult['prompt'],
                'insight' => $insightResult['insight'],
            ]);

            return $this->buildResponse($metrics, $insightResult['insight']);
        });
    }

    /**
     * @param array<int, array{amount: mixed, category: mixed, type: mixed}> $transactions
     * @return Collection<int, array{amount: float, category: string, type: string}>
     */
    private function normalizeTransactions(array $transactions): Collection
    {
        return collect($transactions)
            ->map(function (array $transaction): array {
                return [
                    'amount' => (float) $transaction['amount'],
                    'category' => strtolower(trim((string) $transaction['category'])),
                    'type' => strtolower(trim((string) $transaction['type'])),
                ];
            })
            ->values();
    }

    /**
     * @param Collection<int, array{amount: float, category: string, type: string}> $transactions
     * @return array{total_income: float, total_expense: float, transaction_count: int, top_category: ?string, category_breakdown: array<string, float>, expense_by_category: array<string, float>}
     */
    private function calculateMetrics(Collection $transactions): array
    {
        $totalIncome = round((float) $transactions
            ->where('type', self::TYPE_INCOME)
            ->sum('amount'), 2);

        $totalExpense = round((float) $transactions
            ->where('type', self::TYPE_EXPENSE)
            ->sum('amount'), 2);

        $transactionCount = $transactions->count();

        /** @var array<string, float> $expenseByCategory */
        $expenseByCategory = $this->expenseByCategory($transactions)
            ->mapWithKeys(fn (float $amount, string $category): array => [(string) $category => round($amount, 2)])
            ->all();

        $topCategory = array_key_first($expenseByCategory);

        /** @var array<string, float> $categoryBreakdown */
        $categoryBreakdown = collect($expenseByCategory)
            ->map(fn (float $amount): float => $totalExpense > 0
                ? round(($amount / $totalExpense) * 100, 2)
                : 0.0)
            ->all();

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'transaction_count' => $transactionCount,
            'top_category' => $topCategory,
            'category_breakdown' => $categoryBreakdown,
            'expense_by_category' => $expenseByCategory,
        ];
    }

    /**
     * @param Collection<int, array{amount: float, category: string, type: string}> $normalizedTransactions
     * @param array{total_income: float, total_expense: float, transaction_count: int, top_category: ?string, category_breakdown: array<string, float>, expense_by_category: array<string, float>} $metrics
     */
    private function persistAnalysisReport(int $userId, Collection $normalizedTransactions, array $metrics): AnalysisReport
    {
        return AnalysisReport::create([
            'user_id' => $userId,
            'total_income' => $metrics['total_income'],
            'total_expense' => $metrics['total_expense'],
            'transaction_count' => $metrics['transaction_count'],
            'top_category' => $metrics['top_category'],
            'raw_transactions' => $normalizedTransactions->all(),
        ]);
    }

    /**
     * @param array<string, float> $expenseByCategory
     * @param array<string, float> $categoryBreakdown
     */
    private function persistCategoryBreakdowns(
        AnalysisReport $analysisReport,
        array $expenseByCategory,
        array $categoryBreakdown
    ): void {
        foreach ($expenseByCategory as $category => $amount) {
            $analysisReport->categoryBreakdowns()->create([
                'category' => $category,
                'amount' => $amount,
                'percentage' => (float) ($categoryBreakdown[$category] ?? 0),
            ]);
        }
    }

    /**
     * @param array{total_income: float, total_expense: float, transaction_count: int, top_category: ?string, category_breakdown: array<string, float>, expense_by_category: array<string, float>} $metrics
     * @return array{total_income: float, total_expense: float, transaction_count: int, top_category: ?string, category_breakdown: array<string, float>, insight: string}
     */
    private function buildResponse(array $metrics, string $insight): array
    {
        return [
            'total_income' => $metrics['total_income'],
            'total_expense' => $metrics['total_expense'],
            'transaction_count' => $metrics['transaction_count'],
            'top_category' => $metrics['top_category'],
            'category_breakdown' => $metrics['category_breakdown'],
            'insight' => $insight,
        ];
    }

    /**
     * @param Collection<int, array{amount: float, category: string, type: string}> $transactions
     * @return Collection<string, float>
     */
    private function expenseByCategory(Collection $transactions): Collection
    {
        return $transactions
            ->where('type', self::TYPE_EXPENSE)
            ->groupBy('category')
            ->map(fn (Collection $group): float => (float) $group->sum('amount'))
            ->sortDesc();
    }
}
