@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Add Role</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('access-control.index') }}">Roles & Permissions</a></li>
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
            <li class="breadcrumb-item active">Add</li>
        </ol>
    </nav>
</div>

<div class="section dashboard">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Role Details</h5>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('roles.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Assign Permissions</h6>
                    <div class="text-muted small">{{ $permissions->count() }} available</div>
                </div>

                <div class="row mt-3">
                    @foreach ($permissions as $permission)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="permission_ids[]"
                                    value="{{ $permission->id }}"
                                    id="perm_{{ $permission->id }}"
                                    @checked(in_array($permission->id, old('permission_ids', [])))
                                >
                                <label class="form-check-label" for="perm_{{ $permission->id }}">{{ $permission->name }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

