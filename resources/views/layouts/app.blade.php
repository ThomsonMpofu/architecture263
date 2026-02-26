@extends('layouts.app')

<link rel="stylesheet" href="{{ asset('css/employeedashboard.css') }}">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>

<style>
/* =========================
   ACZ White Dashboard Theme
   ========================= */
.acz-dashboard{
    --acz-bg: #ffffff;
    --acz-bg-soft: #fbfcfd;
    --acz-border: #e9eef0;
    --acz-border-2: #f1f5f4;
    --acz-text: #0f172a;
    --acz-muted: #64748b;
    --acz-shadow: 0 10px 22px rgba(16, 24, 40, 0.06);
    --acz-shadow-hover: 0 14px 34px rgba(16, 24, 40, 0.10);
    --acz-radius: 16px;

    background: linear-gradient(180deg, var(--acz-bg) 0%, var(--acz-bg-soft) 100%);
    border: 1px solid var(--acz-border);
    border-radius: 18px;
    padding: 16px;
}

.acz-dashboard .row{
    --bs-gutter-x: 1rem;
    --bs-gutter-y: 1rem;
}

.acz-card{
    background: #fff !important;
    border: 1px solid var(--acz-border) !important;
    border-radius: var(--acz-radius) !important;
    box-shadow: var(--acz-shadow) !important;
    overflow: hidden;
    transition: transform .15s ease, box-shadow .15s ease;
}
.acz-card:hover{
    transform: translateY(-1px);
    box-shadow: var(--acz-shadow-hover) !important;
}

.acz-card .card-header{
    background: #fff !important;
    border-bottom: 1px solid var(--acz-border) !important;
    padding: 12px 14px;
}

.acz-card .card-body{
    padding: 14px;
}

.acz-title{
    font-size: .95rem;
    font-weight: 700;
    color: var(--acz-text);
    margin: 0;
    display: flex;
    align-items: center;
    gap: .4rem;
}

.acz-muted{ color: var(--acz-muted) !important; }
.acz-small{ font-size: .82rem; }
.acz-xs{ font-size: .75rem; }

.acz-icon-badge{
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    color: #fff;
}
.acz-icon-badge i{ font-size: 18px; }

.acz-chip{
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    border: 1px solid var(--acz-border);
    background: #fff;
    border-radius: 999px;
    padding: 2px 10px;
    font-size: .75rem;
    color: var(--acz-text);
}

.acz-soft{
    background: #fff !important;
    border: 1px solid var(--acz-border-2) !important;
    border-radius: 12px;
    padding: 10px;
}

.acz-btn{
    border-radius: 999px !important;
}
.acz-btn-outline{
    border-color: #d8dee6 !important;
}
.acz-btn-outline:hover{
    background: #f6f8fa !important;
}

.acz-avatar img{
    width: 46px;
    height: 46px;
    object-fit: cover;
}

.acz-table tbody tr:hover{
    background: #fbfcfd;
}

/* Progress stacked */
.emp-stack .progress{
    height: 10px;
    border-radius: 999px;
    overflow: hidden;
    background: #f1f5f4;
    margin-right: 6px;
}
.emp-stack .progress:last-child{ margin-right: 0; }
.emp-stack .progress-bar{ border-radius: 999px; }

/* Chart layout */
#taskStatisticsChart{ max-width: 380px; margin: 0 auto; }
.attendance-canvas{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    pointer-events: none;
}

/* Small utilities */
.acz-divider{ border-top: 1px solid var(--acz-border); }
.bg-primary-soft{ background: rgba(27, 132, 255, .12) !important; }
.text-pink{ color: #ec4899 !important; }
.bg-pink{ background: #f472b6 !important; }
</style>

@section('content')
@php
    $userId = Auth::id();

    $basicInfo = $userId
        ? DB::table('basic_informations')->where('user_id', $userId)->first()
        : null;

    // Leave counters (use once)
    $totalLeaves = $userId ? DB::table('leaves')->where('user_id', $userId)->count() : 0;
    $takenLeaves = $userId ? DB::table('leaves')->where('user_id', $userId)->where('status', 'accepted')->count() : 0;
    $rejectedLeaves = $userId ? DB::table('leaves')->where('user_id', $userId)->where('status', 'rejected')->count() : 0;
    $requestedLeaves = $userId ? DB::table('leaves')->where('user_id', $userId)->where('status', 'pending')->count() : 0;

    // Birthdays (use once)
    $birthdaysToday = DB::table('basic_informations')
        ->join('staff', 'basic_informations.user_id', '=', 'staff.user_id')
        ->join('departments', 'staff.department_id', '=', 'departments.id')
        ->join('department_positions', 'staff.position_id', '=', 'department_positions.id')
        ->select(
            'first_name',
            'surname',
            'departments.name as department',
            'department_positions.job_title as designation',
            'basic_informations.date_of_birth as birthday'
        )
        ->whereMonth('date_of_birth', now()->month)
        ->whereDay('date_of_birth', now()->day)
        ->get();

    $birthdaysTomorrow = DB::table('basic_informations')
        ->join('staff', 'basic_informations.user_id', '=', 'staff.user_id')
        ->join('departments', 'staff.department_id', '=', 'departments.id')
        ->join('department_positions', 'staff.position_id', '=', 'department_positions.id')
        ->select(
            'first_name',
            'surname',
            'departments.name as department',
            'department_positions.job_title as designation',
            'basic_informations.date_of_birth as birthday'
        )
        ->whereMonth('date_of_birth', now()->month)
        ->whereDay('date_of_birth', now()->addDay()->day)
        ->get();

    $upcomingBirthdays = DB::table('basic_informations')
        ->join('staff', 'basic_informations.user_id', '=', 'staff.user_id')
        ->join('departments', 'staff.department_id', '=', 'departments.id')
        ->join('department_positions', 'staff.position_id', '=', 'department_positions.id')
        ->select(
            'first_name',
            'surname',
            'departments.name as department',
            'department_positions.job_title as designation',
            'basic_informations.date_of_birth as birthday'
        )
        ->whereMonth('date_of_birth', now()->month)
        ->whereDay('date_of_birth', '>', now()->day)
        ->orderByRaw('DAY(birthday)')
        ->get();
@endphp

<div class="pagetitle">
    <h3 class="mb-1">{{ __('Dashboard') }}</h3>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">{{ __('Dashboard') }}</li>
        </ol>
    </nav>
</div>

<section class="section dashboard acz-dashboard">

    <!-- Welcome -->
    <div class="card acz-card">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3">
                <span class="acz-avatar">
                    <img src="{{ asset('img/default user.png') }}" alt="Profile Image" class="rounded-circle">
                </span>

                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <div class="fw-semibold" style="font-size: 1rem;">
                                Welcome back,
                                <span class="text-dark">
                                    @auth
                                        {{ $basicInfo ? ($basicInfo->first_name . ' ' . $basicInfo->surname) : Auth::user()->username }}
                                    @else
                                        <script>window.location.href = '{{ route('login') }}';</script>
                                    @endauth
                                </span>
                            </div>
                            <div class="acz-muted acz-small mt-1">
                                You have
                                <span class="fw-bold text-warning">{{ $requestedLeaves }}</span> leave request(s)
                                @if($rejectedLeaves > 0)
                                    and <span class="fw-bold text-danger">{{ $rejectedLeaves }}</span> rejected leave(s)
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary acz-btn acz-btn-outline btn-sm">
                                <i class="ri-calendar-event-line me-1"></i> Leaves
                            </a>
                            <a href="{{ route('assigned-tasks.index') }}" class="btn btn-outline-secondary acz-btn acz-btn-outline btn-sm">
                                <i class="ri-task-line me-1"></i> Tasks
                            </a>
                        </div>
                    </div>

                    <div class="acz-divider mt-3"></div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <span class="acz-chip"><i class="ri-check-line text-success"></i> Taken: <b>{{ $takenLeaves }}</b></span>
                        <span class="acz-chip"><i class="ri-time-line text-warning"></i> Pending: <b>{{ $requestedLeaves }}</b></span>
                        <span class="acz-chip"><i class="ri-close-line text-danger"></i> Rejected: <b>{{ $rejectedLeaves }}</b></span>
                        <span class="acz-chip"><i class="ri-stack-line text-primary"></i> Total: <b>{{ $totalLeaves }}</b></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Welcome -->

    @can('admin-dashboard')
    <!-- Admin Stats -->
    <div class="row">
        <div class="col-xxl-3 col-md-6 d-flex">
            <div class="card acz-card w-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="acz-icon-badge bg-dark"><i class="bx bx-group"></i></span>
                            <div>
                                <div class="acz-muted acz-xs">Total Employees</div>
                                <div class="fw-bold" style="font-size: 1.25rem;">{{ $stats->total_employees }}</div>
                            </div>
                        </div>
                        <i class="ri-arrow-right-s-line acz-muted"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6 d-flex">
            <div class="card acz-card w-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="acz-icon-badge bg-success"><i class="bx bx-user-check"></i></span>
                            <div>
                                <div class="acz-muted acz-xs">Active</div>
                                <div class="fw-bold" style="font-size: 1.25rem;">{{ $stats->active_employees }}</div>
                            </div>
                        </div>
                        <i class="ri-arrow-right-s-line acz-muted"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6 d-flex">
            <div class="card acz-card w-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="acz-icon-badge bg-danger"><i class="bx bx-user-x"></i></span>
                            <div>
                                <div class="acz-muted acz-xs">Inactive</div>
                                <div class="fw-bold" style="font-size: 1.25rem;">{{ $stats->inactive_employees }}</div>
                            </div>
                        </div>
                        <i class="ri-arrow-right-s-line acz-muted"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-3 col-md-6 d-flex">
            <div class="card acz-card w-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="acz-icon-badge bg-info"><i class="bx bx-user-plus"></i></span>
                            <div>
                                <div class="acz-muted acz-xs">Job Applicants</div>
                                <div class="fw-bold" style="font-size: 1.25rem;">{{ $stats->job_applicants }}</div>
                            </div>
                        </div>
                        <i class="ri-arrow-right-s-line acz-muted"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Admin Stats -->
    @endcan

    <!-- Row: Birthdays / Leave / Tasks -->
    <div class="row">
        <!-- Birthdays -->
        <div class="col-xxl-4 col-md-6 d-flex">
            <div class="card acz-card w-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="acz-title"><i class="ri-cake-2-line"></i> Birthdays</h6>
                    <a href="javascript:void(0);" class="btn btn-outline-secondary acz-btn acz-btn-outline btn-sm">
                        <i class="ri-list-unordered me-1"></i> List
                    </a>
                </div>
                <div class="card-body">
                    @php $totalShown = 0; @endphp

                    @if ($birthdaysToday->isNotEmpty())
                        <div class="fw-semibold acz-xs mb-2"><i class="ri-calendar-event-line me-1"></i> Today</div>
                        @foreach ($birthdaysToday->take(6) as $birthday)
                            @php $totalShown++; @endphp
                            <div class="acz-soft mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 32px;height:32px;background:#e7f1ff;">
                                            <i class="ri-user-line"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="fw-semibold acz-small text-truncate">
                                                {{ $birthday->first_name }} {{ $birthday->surname }}
                                            </div>
                                            <div class="acz-muted acz-xs text-truncate">
                                                {{ $birthday->designation ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="acz-chip"><i class="ri-cake-2-line text-warning"></i> Today</span>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if ($birthdaysTomorrow->isNotEmpty() && $totalShown < 6)
                        <div class="fw-semibold acz-xs mt-3 mb-2"><i class="ri-calendar-event-line me-1"></i> Tomorrow</div>
                        @foreach ($birthdaysTomorrow->take(6 - $totalShown) as $birthday)
                            @php $totalShown++; @endphp
                            <div class="acz-soft mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 32px;height:32px;background:#e7f1ff;">
                                            <i class="ri-user-line"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="fw-semibold acz-small text-truncate">
                                                {{ $birthday->first_name }} {{ $birthday->surname }}
                                            </div>
                                            <div class="acz-muted acz-xs text-truncate">
                                                {{ $birthday->designation ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="acz-chip"><i class="ri-time-line text-primary"></i> Tomorrow</span>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if ($upcomingBirthdays->isNotEmpty() && $totalShown < 6)
                        <div class="fw-semibold acz-xs mt-3 mb-2"><i class="ri-calendar-line me-1"></i> Upcoming</div>
                        @foreach ($upcomingBirthdays->take(6 - $totalShown) as $birthday)
                            <div class="acz-soft mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 32px;height:32px;background:#e7f1ff;">
                                            <i class="ri-user-line"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="fw-semibold acz-small text-truncate">
                                                {{ $birthday->first_name }} {{ $birthday->surname }}
                                            </div>
                                            <div class="acz-muted acz-xs text-truncate">
                                                {{ $birthday->designation ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="acz-chip"><i class="ri-notification-3-line text-info"></i> Soon</span>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if ($totalShown == 0)
                        <div class="text-center py-4">
                            <i class="ri-cake-2-line text-warning" style="font-size: 2rem;"></i>
                            <div class="fw-semibold mt-2">No birthdays this month</div>
                            <div class="acz-muted acz-small">Check back next month for more celebrations.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Leave Details -->
        <div class="col-xxl-4 col-md-6 d-flex">
            <div class="card acz-card w-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="acz-title"><i class="ri-file-list-3-line"></i> Leave Details</h6>
                    <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary acz-btn acz-btn-outline btn-sm">
                        <i class="ri-list-check me-1"></i> View All
                    </a>
                </div>

                <div class="card-body d-flex flex-column">
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold">Total Leaves</div>
                            <div class="fw-bold" style="font-size: 1.25rem;">{{ $totalLeaves }}</div>
                        </div>

                        <div class="progress-stacked emp-stack">
                            <div class="progress" role="progressbar" style="width: {{ $totalLeaves > 0 ? ($takenLeaves/$totalLeaves)*100 : 0 }}%">
                                <div class="progress-bar bg-warning"></div>
                            </div>
                            <div class="progress" role="progressbar" style="width: {{ $totalLeaves > 0 ? ($rejectedLeaves/$totalLeaves)*100 : 0 }}%">
                                <div class="progress-bar bg-secondary"></div>
                            </div>
                            <div class="progress" role="progressbar" style="width: {{ $totalLeaves > 0 ? ($requestedLeaves/$totalLeaves)*100 : 0 }}%">
                                <div class="progress-bar bg-danger"></div>
                            </div>
                            <div class="progress" role="progressbar" style="width: {{ $totalLeaves > 0 ? (($totalLeaves - ($takenLeaves + $rejectedLeaves + $requestedLeaves))/$totalLeaves)*100 : 0 }}%">
                                <div class="progress-bar bg-pink"></div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="acz-chip"><i class="ri-check-line text-warning"></i> Taken: <b>{{ $takenLeaves }}</b></span>
                            <span class="acz-chip"><i class="ri-close-line text-secondary"></i> Rejected: <b>{{ $rejectedLeaves }}</b></span>
                            <span class="acz-chip"><i class="ri-time-line text-danger"></i> Pending: <b>{{ $requestedLeaves }}</b></span>
                        </div>
                    </div>

                    <div class="row g-2 mt-auto">
                        <div class="col-6">
                            <div class="acz-soft text-center">
                                <div class="fw-bold" style="font-size: 1.2rem;">{{ $totalLeaves }}</div>
                                <div class="acz-muted acz-xs">Total</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="acz-soft text-center">
                                <div class="fw-bold" style="font-size: 1.2rem;">{{ $takenLeaves }}</div>
                                <div class="acz-muted acz-xs">Taken</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="acz-soft text-center">
                                <div class="fw-bold" style="font-size: 1.2rem;">{{ $rejectedLeaves }}</div>
                                <div class="acz-muted acz-xs">Rejected</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="acz-soft text-center">
                                <div class="fw-bold" style="font-size: 1.2rem;">{{ $requestedLeaves }}</div>
                                <div class="acz-muted acz-xs">Pending</div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary acz-btn acz-btn-outline btn-sm w-100">
                            <i class="ri-add-circle-line me-1"></i> Apply for Leave
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks -->
        <div class="col-xxl-4 col-xl-5 d-flex">
            <div class="card acz-card w-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="acz-title"><i class="ri-task-line"></i> Tasks</h6>
                    <a href="{{ route('assigned-tasks.index') }}" class="btn btn-outline-secondary acz-btn acz-btn-outline btn-sm">
                        <i class="ri-list-check me-1"></i> View All
                    </a>
                </div>

                <div class="card-body">
                    <div class="position-relative mb-3" style="height: 210px;">
                        <canvas id="taskStatisticsChart"></canvas>
                        <div class="attendance-canvas text-center">
                            <div class="acz-muted acz-xs">Completed</div>
                            <div class="fw-bold" style="font-size: 1.1rem;">
                                {{ $taskStatistics['completed'] }}/{{ $totalTasks }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="acz-chip"><i class="ri-checkbox-blank-circle-fill text-primary" style="font-size:10px;"></i> In Progress <b>{{ $taskPercentages['inProgress'] }}%</b></span>
                        <span class="acz-chip"><i class="ri-checkbox-blank-circle-fill text-secondary" style="font-size:10px;"></i> On Hold <b>{{ $taskPercentages['onHold'] }}%</b></span>
                        <span class="acz-chip"><i class="ri-checkbox-blank-circle-fill text-danger" style="font-size:10px;"></i> Overdue <b>{{ $taskPercentages['overdue'] }}%</b></span>
                        <span class="acz-chip"><i class="ri-checkbox-blank-circle-fill text-success" style="font-size:10px;"></i> Completed <b>{{ $taskPercentages['completed'] }}%</b></span>
                        <span class="acz-chip"><i class="ri-checkbox-blank-circle-fill text-warning" style="font-size:10px;"></i> Pending <b>{{ $taskPercentages['pending'] }}%</b></span>
                        <span class="acz-chip"><i class="ri-checkbox-blank-circle-fill text-info" style="font-size:10px;"></i> Review <b>{{ $taskPercentages['review'] }}%</b></span>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary acz-btn acz-btn-outline btn-sm w-100">
                            <i class="ri-add-circle-line me-1"></i> Add New Task
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('admin-dashboard')
    @php
        // Contract stats
        $contractCounts = DB::table('staff')
            ->join('contract_types', 'staff.contract_id', '=', 'contract_types.id')
            ->select('contract_types.name', DB::raw('count(*) as count'))
            ->groupBy('contract_types.name')
            ->get()
            ->pluck('count', 'name')
            ->toArray();

        $totalStaff = array_sum($contractCounts);

        $contractTypes = [
            'Permanent' => ['color' => 'primary', 'icon' => 'ri-user-star-line'],
            'Fixed-Term' => ['color' => 'warning', 'icon' => 'ri-time-line'],
            'Part-Time' => ['color' => 'success', 'icon' => 'ri-user-shared-line'],
            'Temporary Full-Time' => ['color' => 'info', 'icon' => 'ri-user-follow-line'],
            'Internship' => ['color' => 'danger', 'icon' => 'ri-user-heart-line'],
            'Graduate Trainee' => ['color' => 'secondary', 'icon' => 'ri-user-settings-line'],
            'Staff Development Fellow' => ['color' => 'dark', 'icon' => 'ri-user-smile-line'],
            'Tutorial Assistance' => ['color' => 'pink', 'icon' => 'ri-team-line']
        ];

        // Gender stats
        $genderStats = DB::table('staff')
            ->join('basic_informations', 'staff.user_id', '=', 'basic_informations.user_id')
            ->select('basic_informations.sex', DB::raw('count(*) as count'))
            ->groupBy('basic_informations.sex')
            ->get()
            ->pluck('count', 'sex')
            ->toArray();

        $genderTotal = array_sum($genderStats);
        $maleCount = $genderStats['Male'] ?? 0;
        $femaleCount = $genderStats['Female'] ?? 0;

        $malePercentage = $genderTotal > 0 ? round(($maleCount / $genderTotal) * 100) : 0;
        $femalePercentage = $genderTotal > 0 ? round(($femaleCount / $genderTotal) * 100) : 0;

        // Employees on leave
        $currentDate = now()->format('Y-m-d');
        $employeesOnLeave = DB::table('leaves')
            ->join('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
            ->join('basic_informations as employee', 'leaves.user_id', '=', 'employee.user_id')
            ->leftJoin('staff', 'employee.user_id', '=', 'staff.user_id')
            ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
            ->leftJoin('department_positions', 'staff.position_id', '=', 'department_positions.id')
            ->select(
                'employee.first_name',
                'employee.surname',
                'departments.name as department',
                'department_positions.job_title as position',
                'leave_types.name as leave_type',
                'leaves.start_date',
                'leaves.end_date'
            )
            ->where('leaves.status', 'accepted')
            ->where('leaves.start_date', '<=', $currentDate)
            ->where('leaves.end_date', '>', $currentDate)
            ->orderBy('leaves.end_date', 'asc')
            ->limit(4)
            ->get();
    @endphp

    <div class="row">
        <!-- Contract Status -->
        <div class="col-xxl-4 d-flex">
            <div class="card acz-card w-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="acz-title"><i class="ri-file-list-3-line"></i> Contract Status</h6>
                    <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary acz-btn acz-btn-outline btn-sm">
                        <i class="ri-list-check me-1"></i> View All
                    </a>
                </div>

                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="acz-muted acz-small">Total Employees</div>
                        <div class="fw-bold" style="font-size: 1.4rem;">{{ $totalStaff }}</div>
                    </div>

                    <div class="progress-stacked emp-stack mb-3">
                        @foreach($contractTypes as $type => $attrs)
                            @if(isset($contractCounts[$type]) && $totalStaff > 0)
                                <div class="progress" role="progressbar" style="width: {{ ($contractCounts[$type] / $totalStaff) * 100 }}%">
                                    <div class="progress-bar bg-{{ $attrs['color'] }}"></div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="row g-2">
                        @foreach($contractTypes as $type => $attrs)
                            <div class="col-6">
                                <div class="acz-soft">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="acz-xs">
                                            <i class="{{ $attrs['icon'] }} text-{{ $attrs['color'] }} me-1"></i>
                                            {{ Str::limit($type, 16) }}
                                        </div>
                                        <div class="fw-bold">{{ $contractCounts[$type] ?? 0 }}</div>
                                    </div>
                                    @if(isset($contractCounts[$type]) && $totalStaff > 0)
                                        <div class="acz-muted acz-xs mt-1">{{ round(($contractCounts[$type] / $totalStaff) * 100) }}%</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary acz-btn acz-btn-outline btn-sm w-100">
                            <i class="ri-group-line me-1"></i> View All Staff
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gender -->
        <div class="col-xxl-4 d-flex">
            <div class="card acz-card w-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="acz-title"><i class="ri-user-line"></i> Gender Demographics</h6>
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary acz-btn acz-btn-outline btn-sm">
                        <i class="ri-list-check me-1"></i> View All
                    </a>
                </div>

                <div class="card-body">
                    <div style="height: 220px;" class="mb-3">
                        <canvas id="genderPieChart"></canvas>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="acz-soft">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="acz-small"><i class="ri-men-line text-primary me-1"></i> Male</div>
                                    <div class="fw-bold">{{ $maleCount }}</div>
                                </div>
                                <div class="acz-muted acz-xs">{{ $malePercentage }}%</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="acz-soft">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="acz-small"><i class="ri-women-line text-pink me-1"></i> Female</div>
                                    <div class="fw-bold">{{ $femaleCount }}</div>
                                </div>
                                <div class="acz-muted acz-xs">{{ $femalePercentage }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employees on Leave -->
        <div class="col-xxl-4 d-flex">
            <div class="card acz-card w-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="acz-title"><i class="ri-calendar-event-line"></i> Employees on Leave</h6>
                    <a href="{{ route('hr.dashboard') }}" class="btn btn-outline-secondary acz-btn acz-btn-outline btn-sm">
                        <i class="ri-list-check me-1"></i> View All
                    </a>
                </div>

                <div class="card-body">
                    @if($employeesOnLeave->isEmpty())
                        <div class="text-center py-4 acz-soft">
                            <i class="ri-user-follow-line text-primary" style="font-size: 2rem;"></i>
                            <div class="fw-semibold mt-2">No employees currently on leave</div>
                            <div class="acz-muted acz-small">All staff are active today.</div>
                        </div>
                    @else
                        @foreach($employeesOnLeave as $employee)
                            <div class="d-flex align-items-start gap-2 py-2 @if(!$loop->last) border-bottom @endif">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-soft"
                                     style="width: 38px; height: 38px;">
                                    <i class="ri-user-line text-primary"></i>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="fw-semibold acz-small">
                                            {{ $employee->first_name }} {{ $employee->surname }}
                                        </div>
                                        <span class="acz-chip">
                                            <i class="ri-time-line text-success"></i>
                                            {{ \Carbon\Carbon::parse($employee->end_date)->diffForHumans() }}
                                        </span>
                                    </div>

                                    <div class="acz-muted acz-xs mt-1">
                                        <div><i class="ri-briefcase-line me-1"></i> {{ $employee->position ?? 'N/A' }}</div>
                                        <div><i class="ri-building-line me-1"></i> {{ $employee->department ?? 'N/A' }}</div>
                                    </div>

                                    <div class="mt-2">
                                        <span class="acz-chip">
                                            <i class="ri-calendar-event-line text-primary"></i> {{ $employee->leave_type }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Leaves Taken Per Month -->
    <div class="row">
        <div class="col-xxl-12 d-flex">
            <div class="card acz-card w-100">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h6 class="acz-title"><i class="ri-bar-chart-2-line"></i> Leaves Taken Per Month</h6>

                    <div class="d-flex flex-wrap gap-2">
                        <select id="departmentFilter" class="form-select form-select-sm rounded-pill select2" style="min-width: 200px;">
                            <option value="all">All Departments</option>
                            @foreach(DB::table('departments')->select('id', 'name')->orderBy('name', 'asc')->get() as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>

                        <select id="leaveYearFilter" class="form-select form-select-sm rounded-pill select2" style="min-width: 130px;">
                            @php
                                $years = DB::table('leaves')
                                    ->selectRaw('DISTINCT YEAR(start_date) as year')
                                    ->orderBy('year', 'desc')
                                    ->pluck('year')
                                    ->toArray();
                            @endphp
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card-body">
                    <div style="height: 380px;">
                        <canvas id="leavesMonthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        $monthColors = [
            '#FF6B6B', '#FF85A1', '#FFA06E', '#FFD93D', '#6BCB77', '#4D96FF',
            '#9B72AA', '#43919B', '#FF8042', '#00C2A8', '#845EC2', '#D65DB1'
        ];

        $leaveData = DB::table('leaves')
            ->join('staff', 'leaves.user_id', '=', 'staff.user_id')
            ->join('departments', 'staff.department_id', '=', 'departments.id')
            ->select(
                'departments.id as dept_id',
                'departments.name as dept_name',
                DB::raw('YEAR(start_date) as year'),
                DB::raw('MONTH(start_date) as month'),
                DB::raw('COUNT(*) as leave_count')
            )
            ->where('status', 'accepted')
            ->where('start_date', '<=', now())
            ->whereYear('start_date', '>=', date('Y') - 2)
            ->groupBy('departments.id', 'departments.name', 'year', 'month')
            ->get();

        $transformedData = [];
        foreach($leaveData as $record) {
            $transformedData[$record->dept_id][$record->year][$record->month] = [
                'dept_name' => $record->dept_name,
                'leave_count' => $record->leave_count
            ];
        }
    @endphp

    <!-- Applicants & Employees -->
    <div class="row">
        <!-- Jobs Applicants -->
        <div class="col-xxl-6 d-flex">
            <div class="card acz-card w-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="acz-title"><i class="ri-user-search-line"></i> Job Applicants</h6>
                    <a href="{{ route('hr_applicants.index') }}" class="btn btn-outline-secondary acz-btn acz-btn-outline btn-sm">
                        <i class="ri-list-check me-1"></i> View All
                    </a>
                </div>

                <div class="card-body">
                    @forelse ($limitedApplicants as $applicant)
                        <div class="d-flex align-items-center justify-content-between py-2 @if(!$loop->last) border-bottom @endif">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 34px; height: 34px; background: #e7f1ff;">
                                    <i class="ri-user-line"></i>
                                </div>

                                <div class="overflow-hidden">
                                    <div class="fw-semibold acz-small text-truncate">
                                        {{ $applicant['personal_info']['name'] ?? 'N/A' }}
                                    </div>
                                    <div class="acz-muted acz-xs text-truncate">
                                        <i class="ri-briefcase-line me-1"></i> {{ $applicant['job_details']['title'] ?? 'Unknown job' }}
                                    </div>
                                </div>
                            </div>

                            <span class="acz-chip">
                                <i class="ri-flag-line text-primary"></i>
                                {{ $applicant['personal_info']['demographics']['nationality'] ?? 'N/A' }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 acz-soft">
                            <i class="ri-inbox-2-line text-primary" style="font-size: 2rem;"></i>
                            <div class="fw-semibold mt-2">No applicants available</div>
                            <div class="acz-muted acz-small">Applicants will appear here once applications are received.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Employees -->
        <div class="col-xxl-6 d-flex">
            <div class="card acz-card w-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="acz-title"><i class="ri-group-line"></i> Employees</h6>
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary acz-btn acz-btn-outline btn-sm">
                        <i class="ri-list-unordered me-1"></i> View All
                    </a>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 acz-table">
                            <tbody>
                            @forelse ($employees as $employee)
                                <tr>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 34px; height: 34px; background: #e7f1ff;">
                                                <i class="ri-user-3-line"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-semibold acz-small text-truncate">
                                                    {{ $employee->full_name ?? '--' }}
                                                </div>
                                                <div class="acz-muted acz-xs text-truncate">
                                                    <i class="ri-briefcase-line me-1"></i> {{ $employee->designation ?? '--' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-end">
                                        <span class="acz-chip">
                                            <i class="ri-building-line text-primary"></i>
                                            {{ Str::limit($employee->department_name ?? 'No Department', 40, '...') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">
                                        No employee records found.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endcan

    @include('dashboard.addBasicInfoModal')
</section>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    /* ========= Task Doughnut ========= */
    const taskEl = document.getElementById('taskStatisticsChart');
    if (taskEl) {
        const ctx = taskEl.getContext('2d');
        const taskData = @json($taskStatistics);

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In Progress', 'On Hold', 'Overdue', 'Completed', 'Review'],
                datasets: [{
                    data: [
                        taskData.pending,
                        taskData.inProgress,
                        taskData.onHold,
                        taskData.overdue,
                        taskData.completed,
                        taskData.review
                    ],
                    backgroundColor: ['#FFD600', '#1B84FF', '#3B7080', '#E70D0D', '#03C95A', '#67E8F9'],
                    borderWidth: 0,
                    spacing: 2,
                    hoverOffset: 3,
                    cutout: '70%'
                }]
            },
            options: {
                rotation: -100,
                circumference: 185,
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                elements: { arc: { borderRadius: 18 } }
            }
        });
    }

    @can('admin-dashboard')
    /* ========= Gender Pie ========= */
    const genderEl = document.getElementById('genderPieChart');
    if (genderEl) {
        const ctx2 = genderEl.getContext('2d');
        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [{{ $maleCount }}, {{ $femaleCount }}],
                    backgroundColor: ['#4e73df', '#e74a3b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10 } }
                }
            }
        });
    }

    /* ========= Leaves Monthly Bar ========= */
    // Select2 (assumes Select2 is already loaded in your layout)
    if (window.$ && $.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: 'style',
            dropdownAutoWidth: true,
            minimumResultsForSearch: 0
        });
    }

    const months = @json($months);
    const monthColors = @json($monthColors);
    const leaveData = @json($transformedData);

    const leavesEl = document.getElementById('leavesMonthlyChart');
    if (leavesEl) {
        const ctx3 = leavesEl.getContext('2d');

        const leavesChart = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    data: Array(12).fill(0),
                    backgroundColor: monthColors,
                    borderColor: monthColors,
                    borderWidth: 0,
                    borderRadius: 10,
                    borderSkipped: false,
                    maxBarThickness: 26
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });

        function updateChart() {
            const selectedYear = document.getElementById('leaveYearFilter')?.value;
            const selectedDepartment = document.getElementById('departmentFilter')?.value;

            let monthlyData = Array(12).fill(0);

            if (selectedDepartment === 'all') {
                Object.values(leaveData).forEach(deptData => {
                    if (deptData[selectedYear]) {
                        Object.entries(deptData[selectedYear]).forEach(([month, data]) => {
                            monthlyData[parseInt(month) - 1] += data.leave_count;
                        });
                    }
                });
            } else if (leaveData[selectedDepartment] && leaveData[selectedDepartment][selectedYear]) {
                Object.entries(leaveData[selectedDepartment][selectedYear]).forEach(([month, data]) => {
                    monthlyData[parseInt(month) - 1] = data.leave_count;
                });
            }

            leavesChart.data.datasets[0].data = monthlyData;
            leavesChart.update();
        }

        // Select2 events (fallback to normal change)
        if (window.$ && $.fn.select2) {
            $('#leaveYearFilter').on('select2:select', updateChart);
            $('#departmentFilter').on('select2:select', updateChart);
        } else {
            document.getElementById('leaveYearFilter')?.addEventListener('change', updateChart);
            document.getElementById('departmentFilter')?.addEventListener('change', updateChart);
        }

        updateChart();
    }
    @endcan
});
</script>
@endsection