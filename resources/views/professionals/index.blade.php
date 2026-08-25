@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Professionals Registry</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
            <li class="breadcrumb-item active">Professionals</li>
        </ol>
    </nav>
</div>

<div class="section dashboard">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="card-title mb-0">Registered Architects</h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" id="statTotal">Total: {{ $stats->total_professionals }}</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle" id="statActive">Active: {{ $stats->active_professionals }}</span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle" id="statInactive">Inactive: {{ $stats->inactive_professionals }}</span>
                </div>
                <a href="{{ route('users.invite.create') }}" class="btn btn-outline-primary btn-sm">Invite Architect</a>
            </div>

            <div class="table-responsive">
                <table id="professionalsTable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Registration No</th>
                            <th>Specialty</th>
                            <th>Firm</th>
                            <th>Approval</th>
                            <th>Blue Book</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($professionals as $professional)
                            <tr data-row-id="{{ $professional->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $professional->name }}</td>
                                <td id="registration-no-{{ $professional->id }}">{{ $professional->registration_no ?? '—' }}</td>
                                <td>{{ $professional->specialty ?? '—' }}</td>
                                <td>{{ $professional->firm_name ?? '—' }}</td>
                                <td id="approval-badge-{{ $professional->id }}">
                                    @if ($professional->approved_at)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Approved</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Pending</span>
                                    @endif
                                </td>
                                <td id="subscription-badge-{{ $professional->id }}">
                                    @if ($professional->subscription_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Active until {{ optional($professional->subscription_expires_at)->format('d M Y') }}</span>
                                    @elseif ($professional->blue_book_requested_at)
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Awaiting approval</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Not requested</span>
                                    @endif
                                </td>
                                <td id="status-badge-{{ $professional->id }}">
                                    @if ($professional->is_suspended)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Suspended</span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Ok</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('professionals.show', $professional->id) }}">
                                                    <i class="ri-eye-line me-2"></i> View Profile
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item approve-btn {{ $professional->approved_at ? 'disabled' : '' }}" href="#" data-id="{{ $professional->id }}">
                                                    <i class="ri-check-line me-2"></i> Approve Architect
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item activate-subscription-btn {{ ($professional->subscription_active || ! $professional->blue_book_requested_at) ? 'disabled' : '' }}" href="#" data-id="{{ $professional->id }}">
                                                    <i class="ri-vip-crown-line me-2"></i> Approve Blue Book
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item toggle-suspend-btn" href="#" data-id="{{ $professional->id }}">
                                                    @if ($professional->is_suspended)
                                                        <span class="text-success"><i class="ri-check-line me-2"></i> Reactivate</span>
                                                    @else
                                                        <span class="text-danger"><i class="ri-prohibited-line me-2"></i> Suspend</span>
                                                    @endif
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#professionalsTable').DataTable({
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            order: []
        });
    });

    // Force a fresh reload when this page is restored from the browser's
    // back/forward cache (e.g. clicking Back after approving/activating on
    // a professional's detail page) — bfcache restores the old DOM snapshot
    // without hitting the server, so it would otherwise show stale data.
    $(window).on('pageshow', function(e) {
        if (e.originalEvent.persisted || (window.performance && window.performance.getEntriesByType('navigation')[0]?.type === 'back_forward')) {
            location.reload();
        }
    });

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    function applyProfessional(p) {
        if (p.registration_no) {
            document.getElementById('registration-no-' + p.id).innerText = p.registration_no;
        }

        const approvalCell = document.getElementById('approval-badge-' + p.id);
        approvalCell.innerHTML = p.approved_at
            ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Approved</span>'
            : '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">Pending</span>';

        const subCell = document.getElementById('subscription-badge-' + p.id);
        if (p.subscription_active) {
            subCell.innerHTML = '<span class="badge bg-success-subtle text-success border border-success-subtle">Active until ' + p.subscription_expires_at + '</span>';
        } else if (p.blue_book_requested_at) {
            subCell.innerHTML = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">Awaiting approval</span>';
        } else {
            subCell.innerHTML = '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Not requested</span>';
        }

        const statusCell = document.getElementById('status-badge-' + p.id);
        statusCell.innerHTML = p.is_suspended
            ? '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Suspended</span>'
            : '<span class="badge bg-primary-subtle text-primary border border-primary-subtle">Ok</span>';

        const row = document.querySelector('tr[data-row-id="' + p.id + '"]');
        const approveBtn = row.querySelector('.approve-btn');
        const activateBtn = row.querySelector('.activate-subscription-btn');
        const suspendBtn = row.querySelector('.toggle-suspend-btn');
        approveBtn.classList.toggle('disabled', !!p.approved_at);
        activateBtn.classList.toggle('disabled', !!p.subscription_active || !p.blue_book_requested_at);
        suspendBtn.innerHTML = p.is_suspended
            ? '<span class="text-success"><i class="ri-check-line me-2"></i> Reactivate</span>'
            : '<span class="text-danger"><i class="ri-prohibited-line me-2"></i> Suspend</span>';
    }

    document.addEventListener('click', function(e) {
        // Approve
        if (e.target.closest('.approve-btn') && !e.target.closest('.approve-btn').classList.contains('disabled')) {
            e.preventDefault();
            const id = e.target.closest('.approve-btn').getAttribute('data-id');

            Swal.fire({
                title: 'Approve this architect?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#012970',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve'
            }).then((result) => {
                if (!result.isConfirmed) return;

                fetch(`/professionals/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json().then(body => ({ status: r.status, body })))
                .then(({ status, body }) => {
                    if (status === 200) {
                        applyProfessional(body.professional);
                        Toast.fire({ icon: 'success', title: body.message });
                    } else {
                        Toast.fire({ icon: 'error', title: body.message || 'Action failed.' });
                    }
                })
                .catch(() => Toast.fire({ icon: 'error', title: 'An unexpected error occurred.' }));
            });
        }

        // Activate subscription (with duration prompt)
        if (e.target.closest('.activate-subscription-btn') && !e.target.closest('.activate-subscription-btn').classList.contains('disabled')) {
            e.preventDefault();
            const id = e.target.closest('.activate-subscription-btn').getAttribute('data-id');

            Swal.fire({
                title: 'Approve Blue Book access',
                text: 'How many years has this architect paid for?',
                input: 'select',
                inputOptions: { 1: '1 year', 2: '2 years', 3: '3 years', 5: '5 years', 10: '10 years' },
                inputValue: 1,
                showCancelButton: true,
                confirmButtonColor: '#012970',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Approve'
            }).then((result) => {
                if (!result.isConfirmed) return;

                fetch(`/professionals/${id}/blue-book/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ duration_years: parseInt(result.value, 10) })
                })
                .then(r => r.json().then(body => ({ status: r.status, body })))
                .then(({ status, body }) => {
                    if (status === 200) {
                        applyProfessional(body.professional);
                        Toast.fire({ icon: 'success', title: body.message });
                    } else {
                        Toast.fire({ icon: 'error', title: body.message || 'Action failed.' });
                    }
                })
                .catch(() => Toast.fire({ icon: 'error', title: 'An unexpected error occurred.' }));
            });
        }

        // Toggle suspend
        if (e.target.closest('.toggle-suspend-btn')) {
            e.preventDefault();
            const id = e.target.closest('.toggle-suspend-btn').getAttribute('data-id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to change the suspension status of this architect.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#012970',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (!result.isConfirmed) return;

                fetch(`/users/${id}/toggle-suspend`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.message) {
                        const isSuspended = data.status === 'Suspended';

                        document.getElementById('status-badge-' + id).innerHTML = isSuspended
                            ? '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Suspended</span>'
                            : '<span class="badge bg-primary-subtle text-primary border border-primary-subtle">Ok</span>';

                        document.querySelector('tr[data-row-id="' + id + '"] .toggle-suspend-btn').innerHTML = isSuspended
                            ? '<span class="text-success"><i class="ri-check-line me-2"></i> Reactivate</span>'
                            : '<span class="text-danger"><i class="ri-prohibited-line me-2"></i> Suspend</span>';

                        Toast.fire({ icon: 'success', title: data.message });
                    } else {
                        Toast.fire({ icon: 'error', title: data.error || 'Action failed.' });
                    }
                })
                .catch(() => Toast.fire({ icon: 'error', title: 'An unexpected error occurred.' }));
            });
        }
    });
</script>
@endsection
