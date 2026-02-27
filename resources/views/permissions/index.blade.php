@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Permissions</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('access-control.index') }}">Roles & Permissions</a></li>
            <li class="breadcrumb-item active">Permissions</li>
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
                <h5 class="card-title mb-0">Permission List</h5>
                <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm">Add Permission</a>
            </div>

            <div class="table-responsive">
                <table id="permissionsTable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Guard</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $permission)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->guard_name }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('permissions.show', $permission) }}" class="btn btn-outline-primary btn-sm">View</a>
                                    <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-primary btn-sm">Edit</a>
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
        $('#permissionsTable').DataTable({
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

