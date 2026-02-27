@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>View Permission</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('access-control.index') }}">Roles & Permissions</a></li>
            <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permissions</a></li>
            <li class="breadcrumb-item active">View</li>
        </ol>
    </nav>
</div>

<div class="section dashboard">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="card-title mb-0">{{ $permission->name }}</h5>
                <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-primary btn-sm">Edit</a>
            </div>

            <div class="text-muted small">Guard</div>
            <div>{{ $permission->guard_name }}</div>
        </div>
    </div>
</div>
@endsection

