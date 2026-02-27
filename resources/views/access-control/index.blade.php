@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Roles & Permissions</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
            <li class="breadcrumb-item active">Roles & Permissions</li>
        </ol>
    </nav>
</div>

<div class="section dashboard">
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title mb-1">Roles</h5>
                            <div class="text-muted small">{{ $rolesCount }} total</div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-primary btn-sm">View</a>
                            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">Add Role</a>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-muted small">Create roles and assign permissions to each role.</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title mb-1">Permissions</h5>
                            <div class="text-muted small">{{ $permissionsCount }} total</div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('permissions.index') }}" class="btn btn-outline-primary btn-sm">View</a>
                            <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm">Add Permission</a>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-muted small">Create permissions and manage them independently from roles.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

