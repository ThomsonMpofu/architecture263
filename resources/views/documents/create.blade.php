<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="modal-title" id="uploadModalLabel">Upload Plans</h5>
                        <small class="opacity-75">Add new plans to the system</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="uploadForm" action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Title Field -->
                    <div class="mb-4">
                        <label for="title" class="form-label fw-semibold">
                            Title
                        </label>
                        <div class="position-relative">
                            <input type="text" class="form-control form-control-lg" id="title" name="title" 
                                   placeholder="Enter document title..." required>
                            <div id="title-indicator" class="position-absolute top-50 end-0 translate-middle-y me-3" 
                                 style="opacity: 0; transition: opacity 0.3s ease;">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                        </div>
                    </div>
                    <!-- File Upload Field -->
                    <div class="mb-4">
                        <label for="file" class="form-label fw-semibold">
                            <i class="fas fa-file-upload me-2 text-info"></i>Document File
                        </label>
                        <div id="file-drop-zone" class="file-drop-zone p-4 text-center position-relative rounded border">
                            <input type="file" class="form-control d-none" id="file" name="file" 
                                   accept=".pdf,.doc,.docx,.txt" required>
                            
                            <div class="upload-icon-container mb-3" style="transition: transform 0.3s ease;">
                                <i class="fas fa-cloud-upload-alt text-muted" style="font-size: 3rem;"></i>
                            </div>
                            
                            <div class="upload-text">
                                <h6 class="fw-semibold mb-2">Drop your file here or click to browse</h6>
                                <p class="text-muted mb-2 upload-subtext">Choose a file to upload your meeting minutes</p>
                            </div>
                            
                            <div class="file-types">
                                <p class="text-muted">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>
                                    <i class="bi bi-file-earmark-word-fill text-primary me-1"></i> 
                                    <i class="bi bi-file-earmark-text-fill text-secondary me-1"></i>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Selected File Info -->
                        <div id="file-info" class="mt-3 p-3 bg-light rounded-2" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-fill text-success me-2"></i>
                                    <span id="file-name" class="fw-semibold"></span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile()">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upload Progress -->
                    <div id="uploadProgress" class="mb-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Uploading...</small>
                            <small id="progressPercent" class="text-muted">0%</small>
                        </div>
                        <div class="progress">
                            <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="submit" form="uploadForm" id="submitBtn" class="btn btn-success">
                    <svg class="me-2" style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 12l2 2 4-4"></path>
                    </svg>
                    Upload Document
                </button>
            </div>
        </div>
    </div>
</div>