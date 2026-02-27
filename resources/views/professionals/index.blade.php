@extends('layouts.app')
@section('title', 'Registered Professionals')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Professional Registry</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Professionals</li>
                <li class="breadcrumb-item active">List</li>
            </ul>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="card-header p-0">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="card-title mb-0"><i class="bx bx-award me-2"></i>Registered Professionals</h5>
                            <span class="badge bg-primary-transparent me-2" style="font-size: 0.65rem">Total: {{ $stats->total_professionals }}</span>
                            <span class="badge bg-success-transparent me-2" style="font-size: 0.65rem">Active: {{ $stats->active_professionals }}</span>
                            <span class="badge bg-danger-transparent me-2" style="font-size: 0.65rem">Inactive: {{ $stats->inactive_professionals }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-download me-1"></i> Export
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                    <li><a class="dropdown-item" href="#"><i class="ri-file-pdf-line me-2"></i>Export PDF</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="ri-file-excel-line me-2"></i>Export Excel</a></li>
                                </ul>
                            </div>
                            @can('add-professional')
                            <a class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#registerProfessionalModal">
                                <i class="bx bx-plus me-1"></i> Register Professional
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Reg No</th>
                                <th>Professional Name</th>
                                <th>Specialty/Grade</th>
                                <th>Organization/Dept</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($professionals as $key => $professional)
                                <tr class="align-middle">
                                    <td>{{ $key + 1 }}</td>
                                    <td><span class="fw-bold text-primary">{{ $professional->registration_no }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span>
                                                <span class="fw-medium text-dark">{{ \Illuminate\Support\Str::title($professional->full_name) }}</span>
                                                <br>
                                                <small class="text-muted">{{ $professional->license_type ?? 'Professional' }}</small>
                                            </span>
                                        </div>
                                    </td>
                                    <td>{{ $professional->specialty ?? $professional->grade ?? 'N/A' }}</td>
                                    <td>{{ $professional->organization_name ?? 'N/A' }}</td>
                                    <td>
                                        <i class="bx bx-calendar-check me-1 text-muted"></i>
                                        {{ $professional->registration_date ? \Carbon\Carbon::parse($professional->registration_date)->format('d M Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        @if($professional->status === 'active')
                                            <span class="badge bg-success-transparent text-success">
                                                <i class="bx bx-check-shield me-1"></i>Verified
                                            </span>
                                        @else
                                            <span class="badge bg-danger-transparent text-danger">
                                                <i class="bx bx-error-alt me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-dots-vertical-rounded"></i></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="{{ route('professionals.show', $professional->id) }}"><i class="bx bx-show me-2"></i> View Profile</a>
                                                <a class="dropdown-item" href="#"><i class="bx bx-edit-alt me-2"></i> Edit</a>
                                            </div>
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

{{-- Modals --}}
@include('HRModules.professionals.partials.registerModal')


@endsection