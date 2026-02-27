@extends('layouts.app')

@section('content')
<style>
    /* Custom Styles for Professional Look */
    .pagetitle h1 {
        font-family: 'HP Simplified', 'Segoe UI', sans-serif; /* Maintain font */
        color: #012970;
    }
    .card {
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); /* Subtler shadow */
        border: 1px solid #f1f3f5;
    }
    .card-title {
        font-family: 'HP Simplified', 'Segoe UI', sans-serif;
        color: #012970;
        font-weight: 600;
    }
    .form-label {
        color: #444;
        font-weight: 500;
    }
    .btn-primary-custom {
        background-color: #012970; /* Navy Blue instead of bright blue */
        border-color: #012970;
        color: #fff;
    }
    .btn-primary-custom:hover {
        background-color: #0d3d91;
        border-color: #0d3d91;
    }
    /* Table Styles */
    .table-custom th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        border-top: none;
    }
    .badge-status {
        font-size: 0.8rem;
        padding: 0.4em 0.7em;
    }
</style>

<div class="pagetitle">
    <h1>User Management</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
            <li class="breadcrumb-item active">Invite User</li>
        </ol>
    </nav>
</div>

<div class="section dashboard">
    <div class="row">
        <!-- Invitation Form -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Invite New User</h5>

                    <form id="inviteUserForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middle_name" name="middle_name">
                            </div>
                            <div class="col-md-4">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary-custom px-4" id="inviteBtn">
                                <i class="ri-send-plane-fill me-1"></i> Send Invitation
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- User List Table -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Registered Users & Invitations</h5>
                    </div>

                    <!-- Filters -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="ri-search-line"></i></span>
                                <input type="text" class="form-control" id="customSearch" placeholder="Search by name, username or email">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="customStatus">
                                <option value="">All Statuses</option>
                                <option value="Active">Active</option>
                                <option value="Pending">Pending</option>
                                <option value="Expired">Expired</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table id="usersTable" class="table table-hover table-custom align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Registered/Invited</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge {{ $user->badge_class }} badge-status">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            @if($user->email_verified_at)
                                                {{ \Carbon\Carbon::parse($user->email_verified_at)->format('d M Y, H:i') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y, H:i') }}
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        @if($user->status === 'Pending' || $user->status === 'Expired')
                                            <button class="btn btn-sm btn-outline-primary resend-btn" data-id="{{ $user->id }}">
                                                <i class="ri-refresh-line"></i> Resend
                                            </button>
                                        @elseif($user->status === 'Active')
                                            <span class="text-success"><i class="ri-check-double-line"></i> Verified</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var table = $('#usersTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "dom": "lrtip", // Hide default search box (f)
            "order": [], // Disable initial sort to respect server-side ordering
            "language": {
                "emptyTable": "No users found"
            }
        });

        // Custom Search Input
        $('#customSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Custom Status Filter
        $('#customStatus').on('change', function() {
            var status = this.value;
            // Column 4 is Status
            table.column(4).search(status).draw();
        });
    });

    // Initialize SweetAlert Toast
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

    // Auto-fill username logic
    const firstNameInput = document.getElementById('first_name');
    const lastNameInput = document.getElementById('last_name');
    const usernameInput = document.getElementById('username');

    function updateUsername() {
        const first = firstNameInput.value.trim().toLowerCase();
        const last = lastNameInput.value.trim().toLowerCase();
        if (first && last) {
            if (!usernameInput.value || usernameInput.dataset.autoGenerated === 'true') {
                usernameInput.value = `${first}.${last}`;
                usernameInput.dataset.autoGenerated = 'true';
            }
        }
    }

    firstNameInput.addEventListener('input', updateUsername);
    lastNameInput.addEventListener('input', updateUsername);

    // If user manually edits username, stop auto-generating
    usernameInput.addEventListener('input', function() {
        if (this.value) {
            this.dataset.autoGenerated = 'false';
        }
    });

    // Invitation Form Submission
    document.getElementById('inviteUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('inviteBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';

        const formData = new FormData(this);

        fetch("{{ route('users.invite') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(result => {
            if (result.status === 201) {
                Toast.fire({
                    icon: 'success',
                    title: result.body.message
                });
                document.getElementById('inviteUserForm').reset();
                usernameInput.dataset.autoGenerated = 'true';
                // Reload page to show new user in list
                setTimeout(() => location.reload(), 1500);
            } else {
                let errorMsg = result.body.message || 'An error occurred.';
                if (result.body.errors) {
                    let errorList = '<ul style="text-align: left;">';
                    for (const [key, messages] of Object.entries(result.body.errors)) {
                        messages.forEach(msg => errorList += `<li>${msg}</li>`);
                    }
                    errorList += '</ul>';
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: errorList,
                        confirmButtonColor: '#012970'
                    });
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: errorMsg
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Toast.fire({
                icon: 'error',
                title: 'An unexpected error occurred.'
            });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    // Resend Invitation Logic
    document.querySelectorAll('.resend-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            fetch(`/users/resend/${userId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(result => {
                if (result.status === 200) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Invitation resent successfully!'
                    });
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: result.body.message || result.body.error
                    });
                }
            })
            .catch(error => {
                Toast.fire({
                    icon: 'error',
                    title: 'An unexpected error occurred.'
                });
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    });
</script>
@endsection