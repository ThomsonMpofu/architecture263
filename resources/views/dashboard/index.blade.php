@extends('layouts.app')

<link rel="stylesheet" href="{{ asset('css/employeedashboard.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>

<style>
    /* Professional Oversight Theme */
    :root {
        --acz-blue: #1e3a8a;
        --acz-emerald: #059669;
        --acz-bg: #f8fafc;
        --acz-muted: #6b7280;
    }

    .hp-font { font-family: "Inter", "Segoe UI", sans-serif !important; }

    .dashboard .card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    .kpi-label { font-size: .80rem; color: var(--acz-muted); margin-bottom: 4px; }
    .kpi-value { font-size: 1.6rem; font-weight: 800; margin-bottom: 2px; }

    .trend-up   { color: #10b981; font-size: 0.75rem; font-weight: 700; }
    .trend-down { color: #ef4444; font-size: 0.75rem; font-weight: 700; }
    .trend-flat { color: #64748b; font-size: 0.75rem; font-weight: 700; }

    .stats-icon {
        width: 44px; height: 44px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 12px;
        background: #eef2ff;
        color: var(--acz-blue);
    }

    .insight-card {
        background: linear-gradient(to right, #1e3a8a, #3b82f6);
        color: white;
    }

    .mini-muted { font-size: .78rem; color: var(--acz-muted); }
    .progress-sm { height: 6px !important; border-radius: 10px; }

    .table-registry thead th {
        background: #f1f5f9;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        font-size: 11px;
        color: #475569;
        border-bottom: 1px solid #e5e7eb;
    }

    .badge-soft {
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        color: #111827;
    }

    .pill {
        border: 1px solid #e5e7eb;
        background: #ffffff;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: .78rem;
        color: #111827;
    }

    #submissionTrendChart { max-height: 260px; }
    #registryCompChart { max-height: 210px; }
    #pipelineStageChart { max-height: 260px; }
</style>

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | HARD-CODED DEMO DATA (Swap later with DB queries)
    |--------------------------------------------------------------------------
    */

    // === Registry (Current) ===
    $registeredArchitects = 1248;
    $candidates          = 316;
    $registeredFirms     = 184;

    // === Registry (Previous Month) ===
    $registeredArchitectsPrev = 1198;
    $candidatesPrev           = 308;
    $registeredFirmsPrev      = 176;

    // === Pipeline (This Month) ===
    $submissionsThisMonth = 95;
    $approvalsThisMonth   = 70;

    // === Pipeline (Previous Month) ===
    $submissionsPrevMonth = 98;
    $approvalsPrevMonth   = 76;

    // === Compliance (Current) ===
    $cpdCompliancePercent = 78;
    $cpdCompliancePrev    = 74;
    $cpdAtRisk            = 165;

    // === Fees/Revenue (Internal) ===
    $monthlyRevenue       = 24500;  // ZWG (demo numeric)
    $monthlyRevenuePrev   = 21800;  // ZWG prev month

    // === Stage Backlog (Counts + Avg days in stage) ===
    $pipelineStages = [
        ['stage' => 'Submission Received',   'count' => 38, 'avg_days' => 3,  'color' => '#60a5fa'],
        ['stage' => 'ACZ Review',            'count' => 22, 'avg_days' => 11, 'color' => '#a78bfa'],
        ['stage' => 'Local Authority Review','count' => 85, 'avg_days' => 29, 'color' => '#f59e0b'],
        ['stage' => 'Approved',              'count' => 70, 'avg_days' => 5,  'color' => '#34d399'],
        ['stage' => 'Returned / Queries',    'count' => 14, 'avg_days' => 8,  'color' => '#fb7185'],
    ];

    // === LA Performance (Queue + Avg approval days + Efficiency) ===
    $las = [
        ['name' => 'Harare City Council',     'pending' => 31, 'avg_days' => 45, 'val' => 45, 'status' => 'Slow',    'color' => 'danger',  'trend' => -6],
        ['name' => 'Bulawayo Municipality',   'pending' => 9,  'avg_days' => 14, 'val' => 88, 'status' => 'Optimal', 'color' => 'success', 'trend' =>  4],
        ['name' => 'Mutare City',             'pending' => 18, 'avg_days' => 28, 'val' => 62, 'status' => 'Average', 'color' => 'warning', 'trend' =>  1],
        ['name' => 'Gweru Council',           'pending' => 7,  'avg_days' => 12, 'val' => 92, 'status' => 'Optimal', 'color' => 'success', 'trend' =>  3],
    ];

    // === Renewals (Demo) ===
    $renewals = [
        ['name' => 'John Mapondera',   'category' => 'Professional', 'expiry' => 'In 2 Days',   'priority' => 'danger'],
        ['name' => 'Sarah Zhou',       'category' => 'Candidate',    'expiry' => '14 Mar 2026', 'priority' => 'warning'],
        ['name' => 'Urban Edge Studio','category' => 'Practice',     'expiry' => '20 Mar 2026', 'priority' => 'secondary'],
    ];

    // === Pending Applications (Demo) ===
    $pendingApps = [
        ['name' => 'Tawanda Ncube', 'category' => 'Professional Architect', 'submitted' => '05 Dec 2025', 'status' => 'Awaiting Council',    'age' => '23d'],
        ['name' => 'Nomsa Dube',    'category' => 'Architect-in-Training',  'submitted' => '03 Dec 2025', 'status' => 'Awaiting Documents',  'age' => '25d'],
        ['name' => 'Urban Edge Studio', 'category' => 'Practice Registration','submitted'=>'28 Nov 2025', 'status' => 'Committee Review',   'age' => '30d'],
    ];

    // === Throughput Trend (Demo) ===
    $months = ['Sept','Oct','Nov','Dec','Jan','Feb'];
    $submissionsTrend = [65, 59, 80, 81, 56, 95];
    $approvalsTrend   = [40, 48, 62, 55, 42, 70];

    // ====== Helpers / Trends ======
    $pct = function($current, $prev) {
        if ($prev == 0) return 0;
        return round((($current - $prev) / $prev) * 100, 1);
    };

    $archTrend = $pct($registeredArchitects, $registeredArchitectsPrev);
    $candTrend = $pct($candidates, $candidatesPrev);
    $firmTrend = $pct($registeredFirms, $registeredFirmsPrev);

    $subTrend = $pct($submissionsThisMonth, $submissionsPrevMonth);
    $appTrend = $pct($approvalsThisMonth, $approvalsPrevMonth);

    $revTrend = $pct($monthlyRevenue, $monthlyRevenuePrev);
    $cpdTrend = $pct($cpdCompliancePercent, $cpdCompliancePrev);

    // Backlog signal (this month)
    $backlogThisMonth = max($submissionsThisMonth - $approvalsThisMonth, 0);
    $backlogPrevMonth = max($submissionsPrevMonth - $approvalsPrevMonth, 0);
    $backlogTrend     = $pct($backlogThisMonth, max($backlogPrevMonth,1));

    // Bottleneck spotlight: pick stage with highest avg_days (excluding Approved)
    $bottleneck = collect($pipelineStages)
        ->reject(fn($s) => $s['stage'] === 'Approved')
        ->sortByDesc('avg_days')
        ->first();

    // Worst LA: max avg_days
    $worstLA = collect($las)->sortByDesc('avg_days')->first();

    // Insight logic: approvals lagging behind submissions by threshold
    $isLagging = $approvalsThisMonth < ($submissionsThisMonth * 0.75); // demo rule (tune later)
@endphp

<div class="pagetitle hp-font mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">Council Oversight Portal</h3>
            <p class="text-muted mb-0 small">Architecture263 Management System • Regulatory Dashboard</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-light border shadow-sm btn-sm">
                <i class="bx bx-download"></i> Monthly Report
            </button>
            <button class="btn btn-primary btn-sm px-3 shadow-sm">
                <i class="bx bx-plus"></i> New Gazette
            </button>
        </div>
    </div>
</div>

<section class="section dashboard hp-font">

    {{-- TOP KPI ROW --}}
    <div class="row">

        {{-- Registered Architects --}}
        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="card h-100 p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="kpi-label mb-1">Registered Architects</p>
                        <h3 class="kpi-value">{{ number_format($registeredArchitects) }}</h3>

                        @php $cls = $archTrend > 0 ? 'trend-up' : ($archTrend < 0 ? 'trend-down' : 'trend-flat'); @endphp
                        <span class="{{ $cls }}">
                            <i class="bx {{ $archTrend >= 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                            {{ abs($archTrend) }}%
                            <span class="text-muted fw-normal">vs last month</span>
                        </span>

                        {{--
                        LIVE DATA (later):
                        $registeredArchitects = DB::table('registry_members')->where('type','Professional')->where('status','Active')->count();
                        $registeredArchitectsPrev = ... // last month snapshot
                        --}}
                    </div>
                    <div class="stats-icon" style="background:#eef2ff;color:#1e3a8a;">
                        <i class="bx bx-certification fs-4"></i>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <span class="pill"><i class="bx bxs-circle text-warning me-1"></i> Candidates: <strong>{{ number_format($candidates) }}</strong></span>
                    <span class="pill"><i class="bx bxs-circle text-info me-1"></i> Firms: <strong>{{ number_format($registeredFirms) }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Pipeline Backlog --}}
        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="card h-100 p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="kpi-label mb-1">Pipeline Backlog (Month)</p>
                        <h3 class="kpi-value">{{ number_format($backlogThisMonth) }}</h3>

                        @php $cls = $backlogTrend > 0 ? 'trend-down' : ($backlogTrend < 0 ? 'trend-up' : 'trend-flat'); @endphp
                        <span class="{{ $cls }}">
                            <i class="bx {{ $backlogTrend > 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                            {{ abs($backlogTrend) }}%
                            <span class="text-muted fw-normal">vs last month</span>
                        </span>

                        <div class="mini-muted mt-2">
                            Submissions: <strong>{{ $submissionsThisMonth }}</strong>
                            • Approvals: <strong>{{ $approvalsThisMonth }}</strong>
                        </div>

                        {{--
                        LIVE DATA (later):
                        $submissionsThisMonth = DB::table('submissions')->whereMonth('created_at', now()->month)->count();
                        $approvalsThisMonth = DB::table('submissions')->whereMonth('approved_at', now()->month)->count();
                        --}}
                    </div>
                    <div class="stats-icon" style="background:#fff7ed;color:#b45309;">
                        <i class="bx bx-task fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Internal Revenue --}}
        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="card h-100 p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="kpi-label mb-1">Collected Fees (ZWG)</p>
                        <h3 class="kpi-value">{{ number_format($monthlyRevenue) }}</h3>

                        @php $cls = $revTrend > 0 ? 'trend-up' : ($revTrend < 0 ? 'trend-down' : 'trend-flat'); @endphp
                        <span class="{{ $cls }}">
                            <i class="bx {{ $revTrend >= 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                            {{ abs($revTrend) }}%
                            <span class="text-muted fw-normal">vs last month</span>
                        </span>

                        <div class="mini-muted mt-2">
                            Registration + Subscription fees (internal)
                        </div>

                        {{--
                        LIVE DATA (later):
                        $monthlyRevenue = DB::table('payments')->whereMonth('paid_at', now()->month)->sum('amount');
                        --}}
                    </div>
                    <div class="stats-icon" style="background:#ecfdf5;color:#059669;">
                        <i class="bx bx-wallet fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Regulatory Insight --}}
        <div class="col-xxl-3 col-md-6 mb-4">
            <div class="card insight-card h-100 p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="bx bxs-zap me-2"></i>
                    <span class="fw-bold small">Regulatory Health</span>
                </div>

                <div class="small opacity-75">
                    @if($isLagging)
                        <div class="mb-2">Approvals are trailing submissions — backlog building this month.</div>
                        <div class="mb-2">
                            Bottleneck: <strong class="text-white">{{ $bottleneck['stage'] }}</strong>
                            (avg <strong>{{ $bottleneck['avg_days'] }}d</strong>)
                        </div>
                        <div>
                            Slowest LA: <strong class="text-white">{{ $worstLA['name'] }}</strong>
                            (avg <strong>{{ $worstLA['avg_days'] }}d</strong>)
                        </div>
                    @else
                        <div class="mb-2">Throughput is healthy — approvals are keeping pace with submissions.</div>
                        <div class="mb-2">Candidate pipeline supports registry growth.</div>
                        <div>Focus: keep CPD compliance above target.</div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ANALYTICS ROW (Throughput + Composition) --}}
    <div class="row">

        {{-- Workflow Throughput --}}
        <div class="col-xxl-8 col-lg-7 mb-4">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-0">Workflow Throughput</h5>
                        <p class="mini-muted mb-0">Submissions vs Approvals — the gap indicates backlog growth.</p>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active">6M</button>
                        <button class="btn btn-outline-secondary">1Y</button>
                    </div>
                </div>
                <canvas id="submissionTrendChart" height="260"></canvas>

                {{--
                LIVE DATA (later):
                labels + series from controller:
                $months, $submissionsTrend, $approvalsTrend
                --}}
            </div>
        </div>

        {{-- Registry Composition --}}
        <div class="col-xxl-4 col-lg-5 mb-4">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="fw-bold mb-1">Registry Composition</h5>
                        <p class="mini-muted mb-0">Distribution by category (growth & sustainability).</p>
                    </div>
                    @php
                        $compGrowth = $pct(($registeredArchitects + $candidates), ($registeredArchitectsPrev + $candidatesPrev));
                        $cls = $compGrowth > 0 ? 'trend-up' : ($compGrowth < 0 ? 'trend-down' : 'trend-flat');
                    @endphp
                    <span class="{{ $cls }}">
                        <i class="bx {{ $compGrowth >= 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                        {{ abs($compGrowth) }}% <span class="text-muted fw-normal">vs last month</span>
                    </span>
                </div>

                <div class="mt-3" style="position: relative; height: 210px;">
                    <canvas id="registryCompChart"></canvas>
                </div>

                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                        <span class="small text-muted"><i class="bx bxs-circle me-1" style="color:#1e3a8a;"></i> Professionals</span>
                        <span class="fw-bold small">{{ number_format($registeredArchitects) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                        <span class="small text-muted"><i class="bx bxs-circle me-1" style="color:#fbbf24;"></i> Candidates</span>
                        <span class="fw-bold small">{{ number_format($candidates) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small text-muted"><i class="bx bxs-circle me-1" style="color:#06b6d4;"></i> Firms</span>
                        <span class="fw-bold small">{{ number_format($registeredFirms) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BOTTLENECKS (Pipeline Stages) + Quick Actions --}}
    <div class="row">

        {{-- Stage Backlog Chart --}}
        <div class="col-lg-8 mb-4">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-0">Pipeline Funnel & Bottlenecks</h5>
                        <p class="mini-muted mb-0">Stage counts + avg days expose where files are stuck.</p>
                    </div>
                    <span class="badge badge-soft">Bottleneck: {{ $bottleneck['stage'] }} ({{ $bottleneck['avg_days'] }}d)</span>
                </div>

                <canvas id="pipelineStageChart" height="260"></canvas>

                <div class="row mt-3 g-3">
                    @foreach($pipelineStages as $s)
                        <div class="col-md-4">
                            <div class="p-3 rounded" style="border:1px solid #e5e7eb;">
                                <div class="d-flex justify-content-between">
                                    <div class="fw-bold" style="font-size:.85rem;">{{ $s['stage'] }}</div>
                                    <span class="badge badge-soft">{{ $s['avg_days'] }}d avg</span>
                                </div>
                                <div class="mt-2">
                                    <div class="mini-muted">In stage</div>
                                    <div class="fw-bold" style="font-size:1.15rem;">{{ number_format($s['count']) }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{--
                LIVE DATA (later):
                Each stage count from submissions table (status/stage) + avg days from timestamps.
                --}}
            </div>
        </div>

        {{-- Quick Actions + Compliance Trend --}}
        <div class="col-lg-4 mb-4">
            <div class="card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="fw-bold mb-1">Compliance Pulse</h5>
                        <p class="mini-muted mb-0">Quality control for the register.</p>
                    </div>
                    @php $cls = $cpdTrend > 0 ? 'trend-up' : ($cpdTrend < 0 ? 'trend-down' : 'trend-flat'); @endphp
                    <span class="{{ $cls }}">
                        <i class="bx {{ $cpdTrend >= 0 ? 'bx-trending-up' : 'bx-trending-down' }}"></i>
                        {{ abs($cpdTrend) }}% <span class="text-muted fw-normal">vs last month</span>
                    </span>
                </div>

                <div class="text-center my-3">
                    <h2 class="fw-bold text-primary mb-1">{{ $cpdCompliancePercent }}%</h2>
                    <p class="text-muted small mb-0">Overall CPD Compliance</p>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold mb-1">Mandatory CPD (Category A)</label>
                    <div class="progress progress-sm mb-3">
                        <div class="progress-bar bg-success" style="width: 85%"></div>
                    </div>

                    <label class="small fw-bold mb-1">Voluntary CPD (Category B)</label>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-info" style="width: 42%"></div>
                    </div>
                </div>

                <div class="alert alert-warning border-0 small mt-3">
                    <i class="bx bx-error-circle me-1"></i>
                    <strong>{{ number_format($cpdAtRisk) }}</strong> members are currently non-compliant for the 2025 cycle.
                </div>

                <div class="mt-3">
                    <div class="fw-bold mb-2" style="font-size:.85rem;">Quick Actions</div>
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="bx bx-user-plus"></i> Register New Professional</a>
                        <a href="#" class="btn btn-outline-secondary btn-sm"><i class="bx bx-check-shield"></i> Review Applications</a>
                        <a href="#" class="btn btn-outline-secondary btn-sm"><i class="bx bx-book-content"></i> Open Registry</a>
                        <a href="#" class="btn btn-outline-secondary btn-sm"><i class="bx bx-file"></i> Submissions Queue</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- LA PERFORMANCE + Renewals --}}
    <div class="row">

        {{-- LA Audit Table --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0" style="font-size: 0.95rem;">Local Authority Performance Audit</h5>
                    <div class="mini-muted">Queue size + average approval days make inefficiency undeniable.</div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-registry mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Authority</th>
                                <th>Queue Size</th>
                                <th>Avg. Approval Days</th>
                                <th>Efficiency</th>
                                <th class="text-end pe-4">Signal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($las as $la)
                            @php
                                $sigClass = $la['trend'] > 0 ? 'trend-up' : ($la['trend'] < 0 ? 'trend-down' : 'trend-flat');
                                $sigIcon  = $la['trend'] > 0 ? 'bx-trending-up' : ($la['trend'] < 0 ? 'bx-trending-down' : 'bx-minus');
                            @endphp
                            <tr>
                                <td class="ps-4"><strong>{{ $la['name'] }}</strong></td>
                                <td>{{ $la['pending'] }} <span class="mini-muted">files</span></td>
                                <td>
                                    <strong>{{ $la['avg_days'] }}</strong> <span class="mini-muted">days</span>
                                </td>
                                <td style="width: 220px;">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-{{ $la['color'] }}" style="width: {{ $la['val'] }}%"></div>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="badge bg-{{ $la['color'] }}-light text-{{ $la['color'] }}">{{ $la['status'] }}</span>
                                    <div class="{{ $sigClass }} mt-1">
                                        <i class="bx {{ $sigIcon }}"></i> {{ abs($la['trend']) }}%
                                        <span class="text-muted fw-normal">vs last month</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{--
                LIVE DATA (later):
                Pending counts per LA, avg approval days computed from submission -> approval timestamps.
                --}}
            </div>
        </div>

        {{-- Renewals --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0" style="font-size: 0.95rem;">Upcoming Renewals</h5>
                        <div class="mini-muted">Prevent lapses that affect public register integrity.</div>
                    </div>
                    <span class="badge bg-danger">Critical</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Member / Firm</th>
                                <th>Category</th>
                                <th>Expiry</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($renewals as $r)
                            <tr>
                                <td><strong>{{ $r['name'] }}</strong></td>
                                <td>{{ $r['category'] }}</td>
                                <td class="text-{{ $r['priority'] }}">{{ $r['expiry'] }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary py-0">Notify</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{--
                    LIVE DATA (later):
                    $renewals = DB::table('subscriptions')->whereBetween('expires_at',[now(), now()->addDays(30)])->orderBy('expires_at')->limit(10)->get();
                    --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Applications --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-0" style="font-size: 0.95rem;">Pending Registration Applications</h5>
                        <div class="mini-muted">Queue with age/SLA signal — focus on the oldest items first.</div>
                    </div>
                    <a href="#" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-list-ul"></i> View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Applicant</th>
                                <th>Category</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th class="text-end">Age</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingApps as $a)
                            <tr>
                                <td class="fw-semibold">{{ $a['name'] }}</td>
                                <td class="text-muted">{{ $a['category'] }}</td>
                                <td>{{ $a['submitted'] }}</td>
                                <td><span class="badge badge-soft">{{ $a['status'] }}</span></td>
                                <td class="text-end"><span class="fw-bold text-danger">{{ $a['age'] }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{--
                    LIVE DATA (later):
                    $pendingApps = DB::table('registration_applications')->whereIn('status',[...])->orderBy('submitted_at')->limit(10)->get();
                    --}}
                </div>
            </div>
        </div>
    </div>

</section>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // ---------- 1) Throughput line chart ----------
    const months = @json($months);
    const submissionsTrend = @json($submissionsTrend);
    const approvalsTrend = @json($approvalsTrend);

    const ctxTrend = document.getElementById('submissionTrendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Submissions',
                    data: submissionsTrend,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.06)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5
                },
                {
                    label: 'Approvals',
                    data: approvalsTrend,
                    borderColor: '#10b981',
                    borderDash: [6, 6],
                    fill: false,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' }, tooltip: { mode: 'index', intersect: false } },
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // ---------- 2) Registry composition doughnut ----------
    const compData = @json([$registeredArchitects, $candidates, $registeredFirms]);
    const ctxComp = document.getElementById('registryCompChart').getContext('2d');
    new Chart(ctxComp, {
        type: 'doughnut',
        data: {
            labels: ['Professionals', 'Candidates', 'Firms'],
            datasets: [{
                data: compData,
                backgroundColor: ['#1e3a8a', '#fbbf24', '#06b6d4'],
                hoverOffset: 4,
                borderWidth: 0
            }]
        },
        options: {
            cutout: '75%',
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // ---------- 3) Pipeline stage backlog (bar) ----------
    const pipelineStages = @json($pipelineStages);
    const stageLabels = pipelineStages.map(s => s.stage);
    const stageCounts = pipelineStages.map(s => s.count);
    const stageColors = pipelineStages.map(s => s.color);

    const ctxPipe = document.getElementById('pipelineStageChart').getContext('2d');
    new Chart(ctxPipe, {
        type: 'bar',
        data: {
            labels: stageLabels,
            datasets: [{
                label: 'Items in Stage',
                data: stageCounts,
                backgroundColor: stageColors,
                borderWidth: 0,
                borderRadius: 10
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

});
</script>

@endsection