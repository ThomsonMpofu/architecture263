@extends('layouts.app')

@section('content')

<style>
    /* Custom Styles for Professional Look */
    .pagetitle h3 {
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

<!-- Page Title -->
<div class="pagetitle">
    <h3>{{ __('Documents') }}</h3>
    <nav>
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
        <li class="breadcrumb-item active">Document Upload</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                    <!-- Header Section -->
                    <div class="card-body p-4">
                        <div class="col-12">
                            <div class="card-body p-4">
                                <div class="card-header p-0 d-flex flex-wrap justify-content-between align-items-center">
                                    <h5 class="mb-0">Documents</h5>
                                    
                                    <a class="btn btn-outline-primary btn-sm" style="border-radius: 0.2rem; font-size: 0.65rem;" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                        <i class="ri-add-fill"></i> Upload New Plan
                                    </a>
                                
                                </div>
                                <!-- Success Message -->
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show mt-3">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                            </div>
                        </div>               
                    
                    </div>              

                    <!-- documents Table -->
                    <div id="minutesTableContainer">
                        @include('documents.partials.table')
                    </div>
            </div>
        </div>
    </div>
</section>


<!-- Upload Modal -->
 @include('documents.create')

<style>
 .modal-content {
    border-radius: 15px;
    overflow: hidden;
}

.file-drop-zone:hover {
    border-color: #dee2e6 !important;
    background: #f8fbffff !important;
}

.file-drop-zone.dragover {
    border-color: #dee2e6 !important;
    background: rgba(99, 102, 241, 0.05) !important;
    transform: scale(1.02);
}

.upload-icon-container:hover {
    transform: scale(1.1);
}

/* Progress Bar Styling */
.progress {
    height: 8px;
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 10px;
    background: linear-gradient(45deg, #007bff, #0056b3);
}

/* Custom pagination styling */
.pagination {
    justify-content: center;
}

.page-link {
    border-radius: 8px;
    margin: 0 2px;
    border: none;
    color: #6c757d;
    font-weight: 500;
}

.page-link:hover {
    background-color: #e9ecef;
    color: #495057;
}

.page-item.active .page-link {
    background: linear-gradient(135deg, #28a745, #007bff);
    border-color: #28a745;
    border-radius: 8px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .display-5 {
        font-size: 2rem;
    }
    
    .d-flex.gap-3 {
        flex-direction: column;
        gap: 1rem !important;
    }
    
    .upload-btn {
        width: 100%;
    }
    
    .minutes-table {
        font-size: 0.85rem;
    }
    
    .minutes-table thead th {
        font-size: 0.8rem;
        padding: 0.75rem 1rem;
    }
    
    .table-row td {
        padding: 0.75rem 1rem;
    }
    
    .document-icon {
        width: 1.5rem;
        height: 1.5rem;
        font-size: 0.9rem;
    }
    
    .download-btn {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
    }
}

@media (max-width: 576px) {
    .minutes-table thead th:nth-child(2),
    .minutes-table tbody td:nth-child(2) {
        display: none;
    }
    
    .minutes-table thead th:nth-child(3),
    .minutes-table tbody td:nth-child(3) {
        display: none;
    }
}

/* Animation for file success */
@keyframes successPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.file-success {
    animation: successPulse 0.6s ease-in-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const titleIndicator = document.getElementById('title-indicator');
    const fileInput = document.getElementById('file');
    const fileDropZone = document.getElementById('file-drop-zone');
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const uploadForm = document.getElementById('uploadForm');
    const submitBtn = document.getElementById('submitBtn');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');

    // Title input animation
    if (titleInput && titleIndicator) {
        titleInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                titleIndicator.style.opacity = '1';
            } else {
                titleIndicator.style.opacity = '0';
            }
        });
    }

    // File input change handler
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                displaySelectedFile(e.target.files[0]);
            }
        });
    }

    // Drag and drop functionality
    if (fileDropZone) {
        fileDropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        fileDropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        fileDropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                displaySelectedFile(files[0]);
            }
        });

        // Click to upload (avoid triggering on buttons)
        fileDropZone.addEventListener('click', function(e) {
            if (e.target === this || e.target.closest('.upload-icon-container') || e.target.closest('.upload-text') || e.target.closest('.upload-subtext') || e.target.closest('.file-types')) {
                fileInput.click();
            }
        });
    }

        document.getElementById('visibilitySwitch').addEventListener('change', function () {
        const isChecked = this.checked;
        document.getElementById('visibilityHidden').value = isChecked ? 'private' : 'public';
        document.getElementById('visibilityLabel').textContent = isChecked ? 'Private' : 'Public';
    });


    // Display selected file info
    function displaySelectedFile(file) {
        if (fileName) fileName.textContent = file.name;
        if (fileInfo) fileInfo.style.display = 'block';
        
        // Add success styling
        if (fileDropZone) {
            fileDropZone.style.borderColor = '#10b981';
            fileDropZone.style.backgroundColor = 'rgba(16, 185, 129, 0.05)';
            fileDropZone.classList.add('file-success');
            
            setTimeout(() => {
                fileDropZone.style.borderColor = '';
                fileDropZone.style.backgroundColor = '';
                fileDropZone.classList.remove('file-success');
            }, 2000);
        }
    }

    // Clear selected file
    window.clearFile = function() {
        if (fileInput) fileInput.value = '';
        if (fileInfo) fileInfo.style.display = 'none';
        if (fileDropZone) {
            fileDropZone.style.borderColor = '';
            fileDropZone.style.backgroundColor = '';
        }
    };

    // Form submission with progress
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            const formData = new FormData(uploadForm);
            
            // Show progress
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
            }

            // Create XMLHttpRequest for progress tracking
            const xhr = new XMLHttpRequest();
            // Request completion
            xhr.addEventListener('load', function() {
                if (xhr.status === 200) {
                    // Success
                    showToast('Upload Successful!', 'success');
                } else {
                    // Error
                    showToast('Upload failed. Please try again.', 'error');
                    resetForm();
                }
            });

            // Request error
            xhr.addEventListener('error', function() {
                showToast('Upload failed. Please check your connection and try again.', 'error');
                resetForm();
            });
        });
    }

    // Reset form state
    function resetForm() {
        if (uploadProgress) uploadProgress.style.display = 'none';
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg class="me-2" style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 12l2 2 4-4"></path>
                </svg>
                Upload Document
            `;
        }
        if (progressBar) progressBar.style.width = '0%';
        if (progressPercent) progressPercent.textContent = '0%';
    }

    // Show toast notification
    function showToast(message, type = 'success') {
        // Create toast container if it doesn't exist
        if (!document.getElementById('toast-container')) {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '1060';
            document.body.appendChild(container);
        }

        const toastId = 'toast-' + Date.now();
        const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
        
        const toastHtml = `
            <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header ${bgClass} text-white">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                    <strong class="me-auto">${type === 'success' ? 'Success' : 'Error'}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>
        `;
        
        document.getElementById('toast-container').insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 3000 });
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
    }

    // Reset form when modal is hidden
    const uploadModal = document.getElementById('uploadModal');
    if (uploadModal) {
        uploadModal.addEventListener('hidden.bs.modal', function() {
            if (uploadForm) uploadForm.reset();
            clearFile();
            resetForm();
            if (titleIndicator) titleIndicator.style.opacity = '0';
        });
    }
});
    function clearFilters() {
        $('#filterTitle').val('');
        $('#filterDepartment').val('').trigger('change'); // reset select2 dropdown
        $('#filterDate').val('');

        filterVacancies(); // reload data without filters
    }


    // For pagination clicks to keep filters applied
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        filterVacancies(page);
    });

</script>
@endsection