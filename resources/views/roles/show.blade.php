@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>View Role</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('access-control.index') }}">Roles & Permissions</a></li>
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
            <li class="breadcrumb-item active">View</li>
        </ol>
    </nav>
</div>

<div class="section dashboard">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="card-title mb-0">{{ $role->name }}</h5>
                <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary btn-sm">Edit</a>
            </div>

            <div class="mb-3">
                <div class="text-muted small">Guard</div>
                <div>{{ $role->guard_name }}</div>
            </div>

            <div class="mb-2">
                <div class="text-muted small">Permissions</div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                @forelse ($role->permissions as $permission)
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">{{ $permission->name }}</span>
                @empty
                    <span class="text-muted">No permissions assigned.</span>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

