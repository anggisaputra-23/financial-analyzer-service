<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Financial Analyzer Service') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-a: #f4fbff;
            --bg-b: #fff7ec;
            --surface: #ffffff;
            --surface-soft: #f8fcff;
            --line: #d6e8f3;
            --text: #163247;
            --muted: #5d7587;
            --primary: #0f766e;
            --primary-strong: #0b5f59;
            --accent: #f97316;
            --ok: #15803d;
            --info: #0c4a6e;
            --error: #b91c1c;
            --shadow: 0 18px 45px rgba(12, 74, 110, 0.11);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: var(--text);
            font-family: "Sora", "Segoe UI", sans-serif;
            background:
                radial-gradient(900px 360px at 12% -8%, rgba(15, 118, 110, 0.13), transparent 60%),
                radial-gradient(700px 300px at 92% 5%, rgba(249, 115, 22, 0.16), transparent 68%),
                linear-gradient(145deg, var(--bg-a), var(--bg-b));
            min-height: 100vh;
        }

        .wrap {
            width: min(1140px, calc(100% - 2rem));
            margin: 1.5rem auto 2.6rem;
        }

        .hero {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 22px;
            background: linear-gradient(130deg, rgba(255, 255, 255, 0.96), rgba(255, 255, 255, 0.9));
            box-shadow: var(--shadow);
            padding: 1.4rem 1.4rem 1.3rem;
            animation: rise 420ms ease-out both;
        }

        .hero::after {
            content: "";
            position: absolute;
            right: -60px;
            top: -64px;
            width: 220px;
            height: 220px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.25), rgba(249, 115, 22, 0));
            pointer-events: none;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border: 1px solid #b8e0d6;
            background: #ecfdf5;
            color: var(--primary-strong);
            border-radius: 999px;
            font-size: 0.77rem;
            font-weight: 700;
            padding: 0.33rem 0.72rem;
            letter-spacing: 0.02em;
        }

        .hero h1 {
            margin: 0.72rem 0 0;
            font-size: clamp(1.45rem, 2.8vw, 2.22rem);
            line-height: 1.25;
            max-width: 24ch;
        }

        .hero p {
            margin: 0.7rem 0 0;
            color: var(--muted);
            max-width: 72ch;
            font-size: 0.95rem;
            line-height: 1.55;
        }

        .hero-meta {
            margin-top: 0.9rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
        }

        .hero-meta span {
            border: 1px solid #d6e8f3;
            background: #f8fcff;
            border-radius: 12px;
            color: #25506d;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.36rem 0.55rem;
        }

        .grid {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: 1.08fr 0.92fr;
            gap: 0.95rem;
        }

        .panel {
            border: 1px solid var(--line);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: var(--shadow);
            padding: 1rem;
            animation: rise 520ms ease-out both;
        }

        .panel:nth-child(2) {
            animation-delay: 80ms;
        }

        .panel h2 {
            margin: 0;
            font-size: 1.02rem;
        }

        .panel p {
            margin: 0.42rem 0 0;
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.52;
        }

        .stack {
            margin-top: 0.9rem;
            display: grid;
            gap: 0.64rem;
        }

        .field label {
            display: block;
            margin-bottom: 0.32rem;
            color: #4d6477;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .field input {
            width: 100%;
            border: 1px solid #cadfee;
            border-radius: 12px;
            padding: 0.62rem 0.72rem;
            background: #ffffff;
            color: var(--text);
            font: 600 0.88rem "JetBrains Mono", ui-monospace, monospace;
        }

        .field input:focus {
            outline: 2px solid rgba(15, 118, 110, 0.2);
            border-color: #86cdbf;
        }

        .hint {
            margin: 0;
            border: 1px dashed #cbe3f0;
            border-radius: 12px;
            background: #f7fcff;
            color: #3f5f76;
            font-size: 0.82rem;
            line-height: 1.5;
            padding: 0.62rem 0.67rem;
        }

        .actions {
            margin-top: 0.75rem;
            display: flex;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .actions.compact {
            margin-top: 0.45rem;
        }

        .btn {
            border: 0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.86rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            padding: 0.62rem 0.85rem;
            cursor: pointer;
            transition: transform 130ms ease, box-shadow 130ms ease, opacity 130ms ease;
        }

        .btn:disabled {
            opacity: 0.64;
            cursor: not-allowed;
        }

        .btn-primary {
            background: linear-gradient(130deg, var(--primary), var(--primary-strong));
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(11, 95, 89, 0.26);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-1px);
        }

        .btn-soft {
            background: #edf6ff;
            border: 1px solid #cde0f2;
            color: #2f5677;
        }

        .status {
            margin-top: 0.74rem;
            border-radius: 12px;
            border: 1px solid transparent;
            padding: 0.62rem 0.7rem;
            font-size: 0.84rem;
            font-weight: 600;
        }

        .status.info {
            color: var(--info);
            background: #e8f4fb;
            border-color: #b8dbef;
        }

        .status.ok {
            color: var(--ok);
            background: #ecfdf3;
            border-color: #bce8cb;
        }

        .status.error {
            color: var(--error);
            background: #fff0f0;
            border-color: #f5caca;
        }

        .steps {
            margin: 0.78rem 0 0;
            display: grid;
            gap: 0.46rem;
        }

        .step {
            border: 1px solid #d8e8f4;
            border-radius: 12px;
            background: var(--surface-soft);
            padding: 0.55rem 0.64rem;
            font-size: 0.82rem;
            color: #44627a;
            line-height: 1.5;
        }

        .step strong {
            color: #19445f;
        }

        .kv {
            margin-top: 0.86rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.52rem;
        }

        .kcard {
            border: 1px solid #d7e7f3;
            border-radius: 12px;
            background: #fbfeff;
            padding: 0.55rem;
            min-height: 68px;
        }

        .kcard small {
            display: block;
            color: #6d8091;
            font-size: 0.72rem;
            margin-bottom: 0.24rem;
        }

        .kcard strong {
            font-size: 0.98rem;
            color: #1c4661;
            word-break: break-word;
            line-height: 1.4;
        }

        .micro-note {
            margin: 0;
            color: #5d768a;
            font-size: 0.79rem;
            line-height: 1.5;
        }

        .section-title {
            margin: 0.88rem 0 0.48rem;
            font-size: 0.86rem;
            font-weight: 800;
            color: #274c66;
            letter-spacing: 0.01em;
        }

        .breakdown {
            display: grid;
            gap: 0.44rem;
        }

        .bar-item {
            border: 1px solid #d5e6f2;
            border-radius: 11px;
            background: #fbfdff;
            padding: 0.45rem;
        }

        .bar-head {
            display: flex;
            justify-content: space-between;
            font-size: 0.78rem;
            color: #4f6980;
            margin-bottom: 0.3rem;
            gap: 0.34rem;
        }

        .bar-track {
            height: 8px;
            border-radius: 999px;
            background: #dceaf5;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .insight {
            border: 1px solid #f6d8b4;
            background: #fff8ef;
            color: #7f5222;
            border-radius: 12px;
            padding: 0.66rem 0.72rem;
            min-height: 70px;
            font-size: 0.85rem;
            line-height: 1.5;
        }

        .json-box {
            margin-top: 0.55rem;
            border: 1px solid #152636;
            border-radius: 12px;
            background: linear-gradient(180deg, #132739, #0f1f2d);
            overflow: auto;
            max-height: 270px;
        }

        pre {
            margin: 0;
            color: #daf0ff;
            font-size: 0.79rem;
            line-height: 1.52;
            padding: 0.72rem;
            white-space: pre-wrap;
            word-break: break-word;
            font-family: "JetBrains Mono", ui-monospace, monospace;
        }

        @keyframes rise {
            from {
                transform: translateY(8px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 980px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .wrap {
                width: calc(100% - 1rem);
                margin: 1rem auto 1.4rem;
            }

            .hero {
                border-radius: 17px;
                padding: 1rem;
            }

            .panel {
                border-radius: 17px;
                padding: 0.86rem;
            }

            .actions .btn {
                width: 100%;
            }

            .kv {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="wrap">
    <header class="hero">
        <span class="badge">AUTO MODE ONLY</span>
        <h1>Financial Analyzer AI - Sinkronisasi Otomatis Tanpa Input Manual</h1>
        <p>
            Halaman ini hanya untuk jalur otomatis. Data transaksi akan diambil dari feed Service 1,
            diproses analisis AI, lalu hasilnya langsung tampil di dashboard ini.
        </p>
        <div class="hero-meta">
            <span>Endpoint: POST /api/analyze/auto/run</span>
            <span>Service C Pull: GET /api/analyze/auto/latest</span>
            <span>Mode: API key only</span>
            <span>No manual transaction form</span>
        </div>
    </header>

    <main class="grid">
        <section class="panel">
            <h2>Jalankan Otomatis</h2>
            <p>Tekan tombol sekali. Sistem akan pakai default user + since tersimpan secara otomatis.</p>

            <div class="stack">
                <div class="field">
                    <label for="apiKey">API Key</label>
                    <input id="apiKey" type="text" value="{{ (string) config('services.analyzer.api_key', '') }}" placeholder="Contoh: fintrack1">
                </div>

                <p class="hint">
                    Tidak ada opsi manual di UI ini. Jika Service 1 belum aktif atau endpoint feed belum tersedia,
                    hasil akan langsung menampilkan error upstream secara jelas.
                </p>
            </div>

            <div class="actions">
                <button type="button" class="btn btn-primary" id="runBtn">Run Automatic Analysis</button>
                <button type="button" class="btn btn-soft" id="clearBtn">Clear Output</button>
            </div>

            <div id="status" class="status info">Siap menjalankan sinkronisasi otomatis.</div>

            <div class="steps">
                <div class="step"><strong>Step 1:</strong> Service 1 harus aktif (feed endpoint ready).</div>
                <div class="step"><strong>Step 2:</strong> Klik Run Automatic Analysis.</div>
                <div class="step"><strong>Step 3:</strong> Dashboard menampilkan source sync, metrik, dan insight AI.</div>
            </div>
        </section>

        <section class="panel">
            <h2>Live Analysis Output</h2>
            <p>Menampilkan hasil run terbaru dari mode otomatis.</p>

            <div class="section-title">Source Sync</div>
            <div class="kv">
                <div class="kcard">
                    <small>User ID</small>
                    <strong id="sUser">-</strong>
                </div>
                <div class="kcard">
                    <small>Fetched Transactions</small>
                    <strong id="sFetched">-</strong>
                </div>
                <div class="kcard">
                    <small>Since Source</small>
                    <strong id="sSinceSource">-</strong>
                </div>
                <div class="kcard">
                    <small>Next Since</small>
                    <strong id="sNextSince">-</strong>
                </div>
                <div class="kcard">
                    <small>Executed At</small>
                    <strong id="sExecutedAt">-</strong>
                </div>
            </div>

            <div class="section-title">Metrics</div>
            <div class="kv">
                <div class="kcard">
                    <small>Total Income</small>
                    <strong id="mIncome">-</strong>
                </div>
                <div class="kcard">
                    <small>Total Expense</small>
                    <strong id="mExpense">-</strong>
                </div>
                <div class="kcard">
                    <small>Transaction Count</small>
                    <strong id="mCount">-</strong>
                </div>
                <div class="kcard">
                    <small>Top Category</small>
                    <strong id="mTop">-</strong>
                </div>
            </div>

            <div class="section-title">Category Breakdown</div>
            <div id="breakdown" class="breakdown">
                <div class="bar-item">
                    <div class="bar-head"><span>Belum ada data</span><span>0%</span></div>
                    <div class="bar-track"><div class="bar-fill" style="width: 0%"></div></div>
                </div>
            </div>

            <div class="section-title">AI Insight</div>
            <div id="insight" class="insight">Belum ada insight.</div>

            <div class="section-title">Raw JSON</div>
            <div class="json-box">
                <pre id="jsonOutput">Belum ada response.</pre>
            </div>

            <div class="section-title">Payload Siap Ambil untuk Service C</div>
            <p class="micro-note">Payload ini menormalisasi hasil run terbaru. Service C dapat pull via endpoint GET /api/analyze/auto/latest?user_id=...</p>
            <div class="actions compact">
                <button type="button" class="btn btn-soft" id="copyServiceCPayloadBtn">Copy Payload Service C</button>
            </div>
            <div class="json-box">
                <pre id="serviceCPayloadOutput">Belum ada payload Service C.</pre>
            </div>
        </section>
    </main>
</div>

<script>
    const autoRunEndpoint = @json(url('/api/analyze/auto/run'));

    const apiKeyInput = document.getElementById('apiKey');
    const runBtn = document.getElementById('runBtn');
    const clearBtn = document.getElementById('clearBtn');
    const copyServiceCPayloadBtn = document.getElementById('copyServiceCPayloadBtn');
    const statusEl = document.getElementById('status');
    const jsonOutput = document.getElementById('jsonOutput');
    const serviceCPayloadOutput = document.getElementById('serviceCPayloadOutput');

    const sUser = document.getElementById('sUser');
    const sFetched = document.getElementById('sFetched');
    const sSinceSource = document.getElementById('sSinceSource');
    const sNextSince = document.getElementById('sNextSince');
    const sExecutedAt = document.getElementById('sExecutedAt');

    const mIncome = document.getElementById('mIncome');
    const mExpense = document.getElementById('mExpense');
    const mCount = document.getElementById('mCount');
    const mTop = document.getElementById('mTop');
    const insightEl = document.getElementById('insight');
    const breakdownEl = document.getElementById('breakdown');
    let latestServiceCPayload = null;

    function setStatus(message, type) {
        statusEl.className = 'status ' + type;
        statusEl.textContent = message;
    }

    function setLoading(state) {
        runBtn.disabled = state;
        runBtn.textContent = state ? 'Running...' : 'Run Automatic Analysis';
    }

    function formatMoney(value) {
        const amount = Number(value);

        if (Number.isNaN(amount)) {
            return '-';
        }

        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(amount);
    }

    function renderBreakdown(breakdown) {
        const entries = Object.entries(breakdown || {});

        if (!entries.length) {
            breakdownEl.innerHTML = '<div class="bar-item"><div class="bar-head"><span>Belum ada data</span><span>0%</span></div><div class="bar-track"><div class="bar-fill" style="width:0%"></div></div></div>';
            return;
        }

        breakdownEl.innerHTML = entries
            .sort((a, b) => Number(b[1]) - Number(a[1]))
            .map(([category, percent]) => {
                const value = Number(percent) || 0;
                const width = Math.max(0, Math.min(100, value));

                return '' +
                    '<div class="bar-item">' +
                        '<div class="bar-head"><span>' + String(category) + '</span><span>' + value.toFixed(2) + '%</span></div>' +
                        '<div class="bar-track"><div class="bar-fill" style="width:' + width.toFixed(2) + '%"></div></div>' +
                    '</div>';
            })
            .join('');
    }

    function renderSource(source, executedAt) {
        sUser.textContent = source && source.user_id != null ? String(source.user_id) : '-';
        sFetched.textContent = source && source.fetched_transactions != null ? String(source.fetched_transactions) : '-';
        sSinceSource.textContent = source && source.since_source ? String(source.since_source) : '-';
        sNextSince.textContent = source && source.next_since ? String(source.next_since) : '-';
        sExecutedAt.textContent = executedAt || '-';
    }

    function buildServiceCPayload(data, executedAt) {
        const source = data && data.source ? data.source : {};
        const analysis = data && data.analysis ? data.analysis : null;

        if (!analysis) {
            return {
                schema_version: 'service-c.v1',
                status: 'no_new_transactions',
                executed_at: executedAt,
                message: data && data.message ? String(data.message) : 'Tidak ada transaksi baru.',
                source_sync: {
                    user_id: source && source.user_id != null ? Number(source.user_id) : null,
                    fetched_transactions: source && source.fetched_transactions != null ? Number(source.fetched_transactions) : 0,
                    since_source: source && source.since_source ? String(source.since_source) : null,
                    next_since: source && source.next_since ? String(source.next_since) : null,
                },
                metrics: null,
                ai_insight: null,
                category_breakdown: [],
            };
        }

        return {
            schema_version: 'service-c.v1',
            status: 'ready',
            executed_at: executedAt,
            source_sync: {
                user_id: source && source.user_id != null ? Number(source.user_id) : null,
                fetched_transactions: source && source.fetched_transactions != null ? Number(source.fetched_transactions) : Number(analysis.transaction_count || 0),
                since_source: source && source.since_source ? String(source.since_source) : null,
                next_since: source && source.next_since ? String(source.next_since) : null,
            },
            metrics: {
                total_income: Number(analysis.total_income || 0),
                total_expense: Number(analysis.total_expense || 0),
                transaction_count: Number(analysis.transaction_count || 0),
                top_category: analysis.top_category ? String(analysis.top_category) : null,
            },
            ai_insight: analysis.insight ? String(analysis.insight) : null,
            category_breakdown: Object.entries(analysis.category_breakdown || {})
                .map(([category, percentage]) => ({
                    category: String(category),
                    percentage: Number(percentage || 0),
                })),
        };
    }

    function renderServiceCPayload(data, executedAt) {
        latestServiceCPayload = buildServiceCPayload(data, executedAt);
        serviceCPayloadOutput.textContent = JSON.stringify(latestServiceCPayload, null, 2);
    }

    function renderAnalysis(analysis) {
        mIncome.textContent = formatMoney(analysis.total_income);
        mExpense.textContent = formatMoney(analysis.total_expense);
        mCount.textContent = analysis.transaction_count != null ? String(analysis.transaction_count) : '-';
        mTop.textContent = analysis.top_category ? String(analysis.top_category) : '-';
        insightEl.textContent = analysis.insight ? String(analysis.insight) : 'Insight tidak tersedia.';
        renderBreakdown(analysis.category_breakdown || {});
    }

    function clearOutput() {
        renderSource(null, null);
        mIncome.textContent = '-';
        mExpense.textContent = '-';
        mCount.textContent = '-';
        mTop.textContent = '-';
        insightEl.textContent = 'Belum ada insight.';
        renderBreakdown({});
        jsonOutput.textContent = 'Belum ada response.';
        latestServiceCPayload = null;
        serviceCPayloadOutput.textContent = 'Belum ada payload Service C.';
        setStatus('Output dibersihkan.', 'info');
    }

    async function runAutoAnalysis() {
        const apiKey = String(apiKeyInput.value || '').trim();

        if (!apiKey) {
            setStatus('API key wajib diisi.', 'error');
            return;
        }

        setLoading(true);
        setStatus('Menjalankan analisis otomatis...', 'info');

        try {
            const response = await fetch(autoRunEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'x-api-key': apiKey,
                },
                body: JSON.stringify({}),
            });

            const data = await response.json().catch(() => ({
                message: 'Response bukan JSON valid.'
            }));

            jsonOutput.textContent = JSON.stringify(data, null, 2);

            if (!response.ok) {
                throw new Error(String(data.message || ('Request gagal dengan status ' + response.status)));
            }

            const executedAt = new Date().toISOString();

            renderSource(data.source || null, executedAt);
            renderServiceCPayload(data, executedAt);

            if (data.analysis) {
                renderAnalysis(data.analysis);
                setStatus('Auto analysis berhasil. ' + String((data.source && data.source.fetched_transactions) || 0) + ' transaksi diproses.', 'ok');
            } else {
                mIncome.textContent = '-';
                mExpense.textContent = '-';
                mCount.textContent = '-';
                mTop.textContent = '-';
                insightEl.textContent = 'Tidak ada insight baru.';
                renderBreakdown({});
                setStatus(String(data.message || 'Tidak ada transaksi baru.'), 'info');
            }
        } catch (error) {
            setStatus(String(error.message || 'Terjadi kesalahan saat auto analysis.'), 'error');
        } finally {
            setLoading(false);
        }
    }

    runBtn.addEventListener('click', runAutoAnalysis);
    clearBtn.addEventListener('click', clearOutput);
    copyServiceCPayloadBtn.addEventListener('click', async () => {
        if (!latestServiceCPayload) {
            setStatus('Jalankan analisis otomatis dulu agar payload Service C tersedia.', 'error');
            return;
        }

        try {
            await navigator.clipboard.writeText(JSON.stringify(latestServiceCPayload, null, 2));
            setStatus('Payload Service C berhasil di-copy.', 'ok');
        } catch (error) {
            setStatus('Gagal copy ke clipboard. Silakan copy manual dari panel payload.', 'error');
        }
    });
</script>
</body>
</html>
