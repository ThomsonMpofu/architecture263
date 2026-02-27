@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Add Permission</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('access-control.index') }}">Roles & Permissions</a></li>
            <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permissions</a></li>
            <li class="breadcrumb-item active">Add</li>
        </ol>
    </nav>
</div>

<div class="section dashboard">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Permission Details</h5>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('permissions.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Permission Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

