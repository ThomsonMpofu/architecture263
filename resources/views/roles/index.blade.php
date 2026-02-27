@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Roles</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('access-control.index') }}">Roles & Permissions</a></li>
            <li class="breadcrumb-item active">Roles</li>
        </ol>
    </nav>
</div>

<div class="section dashboard">
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="card-title mb-0">Role List</h5>
                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">Add Role</a>
            </div>

            <div class="table-responsive">
                <table id="rolesTable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Guard</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->guard_name }}</td>
                                <td>{{ $role->permissions_count }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('roles.show', $role) }}" class="btn btn-outline-primary btn-sm">View</a>
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary btn-sm">Edit</a>
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
        $('#rolesTable').DataTable({
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
</script>
@endsection

