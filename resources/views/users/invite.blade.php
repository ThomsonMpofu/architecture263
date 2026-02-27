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
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item edit-user-btn" href="#" 
                                                       data-id="{{ $user->id }}" 
                                                       data-first-name="{{ $user->first_name }}" 
                                                       data-middle-name="{{ $user->middle_name }}" 
                                                       data-last-name="{{ $user->last_name }}" 
                                                       data-username="{{ $user->username }}" 
                                                       data-email="{{ $user->email }}">
                                                        <i class="ri-pencil-line me-2"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item toggle-suspend-btn" href="#" data-id="{{ $user->id }}">
                                                        @if($user->is_suspended)
                                                            <span class="text-success"><i class="ri-check-line me-2"></i> Activate</span>
                                                        @else
                                                            <span class="text-danger"><i class="ri-prohibited-line me-2"></i> Suspend</span>
                                                        @endif
                                                    </a>
                                                </li>
                                                @if($user->status === 'Pending')
                                                    <li>
                                                        <a class="dropdown-item expire-link-btn text-warning" href="#" data-id="{{ $user->id }}">
                                                            <i class="ri-time-line me-2"></i> Expire Link
                                                        </a>
                                                    </li>
                                                @endif
                                                @if($user->status === 'Expired' || $user->status === 'Inactive' || $user->is_suspended)
                                                    <li>
                                                        <a class="dropdown-item reactivate-link-btn text-primary" href="#" data-id="{{ $user->id }}">
                                                            <i class="ri-mail-send-line me-2"></i> Reactivate Link
                                                        </a>
                                                    </li>
                                                @endif
                                                @if(($user->status === 'Pending' || $user->status === 'Expired') && !$user->is_suspended)
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item resend-btn text-primary" href="#" data-id="{{ $user->id }}">
                                                            <i class="ri-send-plane-fill me-2"></i> Resend Invite
                                                        </a>
                                                    </li>
                                                @endif
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
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_user_id">
                    <div class="mb-3">
                        <label for="edit_first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_middle_name" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="edit_middle_name" name="middle_name">
                    </div>
                    <div class="mb-3">
                        <label for="edit_last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom" id="updateUserBtn">Update User</button>
                    </div>
                </form>
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

    // Event Delegation for Dynamic Elements
    document.addEventListener('click', function(e) {
        
        // Edit User
        if (e.target.closest('.edit-user-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.edit-user-btn');
            const id = btn.getAttribute('data-id');
            const firstName = btn.getAttribute('data-first-name');
            const middleName = btn.getAttribute('data-middle-name');
            const lastName = btn.getAttribute('data-last-name');
            const username = btn.getAttribute('data-username');
            const email = btn.getAttribute('data-email');

            document.getElementById('edit_user_id').value = id;
            document.getElementById('edit_first_name').value = firstName;
            document.getElementById('edit_middle_name').value = middleName;
            document.getElementById('edit_last_name').value = lastName;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_email').value = email;

            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }

        // Toggle Suspend
        if (e.target.closest('.toggle-suspend-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.toggle-suspend-btn');
            const id = btn.getAttribute('data-id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to change the suspension status of this user.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#012970',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/users/${id}/toggle-suspend`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            Toast.fire({ icon: 'success', title: data.message });
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Toast.fire({ icon: 'error', title: data.error || 'Action failed.' });
                        }
                    });
                }
            });
        }

        // Expire Link
        if (e.target.closest('.expire-link-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.expire-link-btn');
            const id = btn.getAttribute('data-id');

            Swal.fire({
                title: 'Expire Invitation?',
                text: "This will invalidate the current invitation link.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f0ad4e',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, expire it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/users/${id}/expire-link`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            Toast.fire({ icon: 'success', title: data.message });
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Toast.fire({ icon: 'error', title: data.error || 'Action failed.' });
                        }
                    });
                }
            });
        }

        if (e.target.closest('.reactivate-link-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.reactivate-link-btn');
            const id = btn.getAttribute('data-id');

            Swal.fire({
                title: 'Reactivate link?',
                text: "This will generate a new link and send an email to the user to set a password.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#012970',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send email'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/users/${id}/reactivate-link`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json().then(data => ({ status: response.status, body: data })))
                    .then(result => {
                        if (result.status === 200) {
                            Toast.fire({ icon: 'success', title: result.body.message });
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Toast.fire({ icon: 'error', title: result.body.message || result.body.error || 'Action failed.' });
                        }
                    })
                    .catch(() => {
                        Toast.fire({ icon: 'error', title: 'An unexpected error occurred.' });
                    });
                }
            });
        }

        // Resend Invite
        if (e.target.closest('.resend-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.resend-btn');
            const userId = btn.getAttribute('data-id');
            const originalText = btn.innerHTML;
            
            // Disable logic for anchor tag
            btn.style.pointerEvents = 'none';
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';

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
                    Toast.fire({ icon: 'success', title: 'Invitation resent successfully!' });
                } else {
                    Toast.fire({ icon: 'error', title: result.body.message || result.body.error });
                }
            })
            .catch(error => {
                Toast.fire({ icon: 'error', title: 'An unexpected error occurred.' });
            })
            .finally(() => {
                btn.style.pointerEvents = 'auto';
                btn.innerHTML = originalText;
            });
        }
    });

    // Handle Edit Form Submit
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('edit_user_id').value;
        const formData = new FormData(this);
        formData.append('_method', 'PUT'); 

        const btn = document.getElementById('updateUserBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';

        fetch(`/users/${id}`, { 
            method: 'POST', 
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(result => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            if (result.status === 200) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                modal.hide();
                Toast.fire({ icon: 'success', title: result.body.message });
                setTimeout(() => location.reload(), 1500);
            } else {
                let errorMsg = result.body.message || 'Failed to update user.';
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
                    Swal.fire('Error', errorMsg, 'error');
                }
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            Swal.fire('Error', 'An unexpected error occurred.', 'error');
        });
    });
</script>
@endsection
