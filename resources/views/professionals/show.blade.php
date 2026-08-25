@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Professional Profile</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('professionals.index') }}">Professionals</a></li>
            <li class="breadcrumb-item active">{{ $professional->name }}</li>
        </ol>
    </nav>
</div>

<div class="section dashboard">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $professional->name }}</h5>
            <table class="table">
                <tr>
                    <th style="width: 220px">Username</th>
                    <td>{{ $professional->username }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $professional->email }}</td>
                </tr>
                <tr>
                    <th>Registration No</th>
                    <td id="registration-no">{{ $professional->registration_no ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Specialty</th>
                    <td id="specialty">{{ $professional->specialty ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Firm</th>
                    <td id="firm-name">{{ $professional->firm_name ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Approval status</th>
                    <td id="approval-status">
                        @if ($professional->approved_at)
                            Approved on {{ $professional->approved_at->format('Y-m-d') }}
                        @else
                            <span class="text-warning">Pending admin approval</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Blue Book</th>
                    <td id="subscription-status">
                        @if ($professional->subscription_active)
                            Active — expires {{ optional($professional->subscription_expires_at)->format('Y-m-d') ?? 'never' }}
                        @elseif ($professional->blue_book_requested_at)
                            <span class="text-warning">Purchase requested on {{ $professional->blue_book_requested_at->format('Y-m-d') }} — awaiting approval</span>
                        @else
                            <span class="text-secondary">Not requested yet</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Account status</th>
                    <td id="account-status">
                        @if ($professional->is_suspended)
                            <span class="text-danger">Suspended</span>
                        @else
                            Ok
                        @endif
                    </td>
                </tr>
            </table>

            <div class="d-flex gap-2 mt-3">
                <button type="button" id="approveBtn" class="btn btn-success btn-sm" {{ $professional->approved_at ? 'disabled' : '' }}>
                    Approve Architect
                </button>

                <button type="button" id="activateSubscriptionBtn" class="btn btn-primary btn-sm" {{ ($professional->subscription_active || ! $professional->blue_book_requested_at) ? 'disabled' : '' }}>
                    Approve Blue Book
                </button>

                <button type="button" id="toggleSuspendBtn" class="btn btn-outline-danger btn-sm">
                    {{ $professional->is_suspended ? 'Reactivate' : 'Suspend' }}
                </button>

                <button type="button" id="editDetailsBtn" class="btn btn-outline-secondary btn-sm">
                    Edit Details
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const professionalId = {{ $professional->id }};

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

    document.getElementById('approveBtn').addEventListener('click', function() {
        Swal.fire({
            title: 'Approve this architect?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#012970',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approve'
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch(`/professionals/${professionalId}/approve`, {
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
                    document.getElementById('approval-status').innerText = 'Approved on ' + body.professional.approved_at;
                    document.getElementById('registration-no').innerText = body.professional.registration_no || '—';
                    document.getElementById('approveBtn').disabled = true;
                    Toast.fire({ icon: 'success', title: body.message });
                } else {
                    Toast.fire({ icon: 'error', title: body.message || 'Action failed.' });
                }
            })
            .catch(() => Toast.fire({ icon: 'error', title: 'An unexpected error occurred.' }));
        });
    });

    document.getElementById('activateSubscriptionBtn').addEventListener('click', function() {
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

            fetch(`/professionals/${professionalId}/blue-book/approve`, {
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
                    document.getElementById('subscription-status').innerText = 'Active — expires ' + body.professional.subscription_expires_at;
                    document.getElementById('activateSubscriptionBtn').disabled = true;
                    Toast.fire({ icon: 'success', title: body.message });
                } else {
                    Toast.fire({ icon: 'error', title: body.message || 'Action failed.' });
                }
            })
            .catch(() => Toast.fire({ icon: 'error', title: 'An unexpected error occurred.' }));
        });
    });

    const specialtyOptions = @json($specialties);
    const firmOptions = @json($firms);

    document.getElementById('editDetailsBtn').addEventListener('click', function() {
        const current = {
            specialty: @json($professional->specialty),
            firm_name: @json($professional->firm_name),
        };

        const specialtySelectHtml = '<select id="swal-specialty" class="swal2-select" style="display:block;width:80%;margin:0.5em auto;">' +
            '<option value="">Select specialty</option>' +
            specialtyOptions.map(s => '<option value="' + s + '"' + (s === current.specialty ? ' selected' : '') + '>' + s + '</option>').join('') +
            '</select>';

        const firmIsExisting = current.firm_name && firmOptions.includes(current.firm_name);
        const firmSelectHtml = '<select id="swal-firm-select" class="swal2-select" style="display:block;width:80%;margin:0.5em auto;">' +
            '<option value="">Select firm</option>' +
            firmOptions.map(f => '<option value="' + f + '"' + (f === current.firm_name ? ' selected' : '') + '>' + f + '</option>').join('') +
            '<option value="__new__"' + (current.firm_name && !firmIsExisting ? ' selected' : '') + '>+ Add new firm…</option>' +
            '</select>' +
            '<input id="swal-firm-name" class="swal2-input" placeholder="New firm name" value="' + (!firmIsExisting ? (current.firm_name ?? '') : '') + '" style="' + (firmIsExisting || !current.firm_name ? 'display:none' : '') + '">';

        Swal.fire({
            title: 'Edit Professional Details',
            html: specialtySelectHtml + firmSelectHtml,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonColor: '#012970',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Save',
            didOpen: () => {
                const firmSelect = document.getElementById('swal-firm-select');
                const firmInput = document.getElementById('swal-firm-name');
                firmSelect.addEventListener('change', function() {
                    if (this.value === '__new__') {
                        firmInput.style.display = '';
                        firmInput.value = '';
                        firmInput.focus();
                    } else {
                        firmInput.style.display = 'none';
                        firmInput.value = this.value;
                    }
                });
            },
            preConfirm: () => ({
                specialty: document.getElementById('swal-specialty').value,
                firm_name: document.getElementById('swal-firm-name').value,
            })
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch(`/professionals/${professionalId}/details`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(result.value)
            })
            .then(r => r.json().then(body => ({ status: r.status, body })))
            .then(({ status, body }) => {
                if (status === 200) {
                    document.getElementById('specialty').innerText = body.professional.specialty || '—';
                    document.getElementById('firm-name').innerText = body.professional.firm_name || '—';
                    Toast.fire({ icon: 'success', title: body.message });
                } else {
                    Toast.fire({ icon: 'error', title: body.message || 'Action failed.' });
                }
            })
            .catch(() => Toast.fire({ icon: 'error', title: 'An unexpected error occurred.' }));
        });
    });

    document.getElementById('toggleSuspendBtn').addEventListener('click', function() {
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

            fetch(`/users/${professionalId}/toggle-suspend`, {
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
                    document.getElementById('account-status').innerHTML = isSuspended
                        ? '<span class="text-danger">Suspended</span>'
                        : 'Ok';
                    document.getElementById('toggleSuspendBtn').innerText = isSuspended ? 'Reactivate' : 'Suspend';
                    Toast.fire({ icon: 'success', title: data.message });
                } else {
                    Toast.fire({ icon: 'error', title: data.error || 'Action failed.' });
                }
            })
            .catch(() => Toast.fire({ icon: 'error', title: 'An unexpected error occurred.' }));
        });
    });
</script>
@endsection
