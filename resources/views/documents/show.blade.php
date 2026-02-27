@extends('layouts.app')

@section('content')
<div class="container">				
   <!-- Header Section -->
   <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between py-3">
                <div>
                    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
                        <div class="back-icon-wrapper me-2">
                            <i class="bi bi-arrow-left"></i>
                        </div>
                        <span class="back-text">Back</span>
                    </a>
                </div>
                <div>
                    <a href="{{ route('documents.download', $document->id) }}" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-download me-2"></i>Download
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    
    <h2>{{ $document->file_name }}</h2>
    @php
    $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
    @endphp

    @if($extension === 'pdf')
        <iframe src="{{ asset('storage/' . $document->file_path) }}" width="100%" height="600px"></iframe>
    @elseif($extension === 'docx')
        <div id="docx-container" style="min-height: 600px; padding: 20px;"></div>
        
            <script src="https://unpkg.com/jszip/dist/jszip.min.js"></script>
            <script src="https://unpkg.com/docx-preview/dist/docx-preview.min.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    fetch("{{ asset('storage/' . $document->file_path) }}")
                        .then(response => {
                            if (!response.ok) throw new Error("File not found: " + response.status);
                            return response.blob();
                        })
                        .then(blob => docx.renderAsync(blob, document.getElementById("docx-container")))
                        .catch(error => console.error("DOCX preview error:", error));
                });
            </script>
        
    @else
        <p>Preview not available for this file type.</p>
    @endif

    <br>
</div>

@push('styles')
<style>    
    .back-link {
        color: #198754 !important;
        text-decoration: none;
        transition: all 0.3s ease;
        padding: 8px 12px;
        border-radius: 8px;
        background: transparent;
    }
    .back-link:hover {
        color: #007bff;
        background: rgba(0, 123, 255, 0.1);
        transform: translateX(-2px);
    }
    
    .back-icon-wrapper {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(108, 117, 125, 0.1);
        transition: all 0.3s ease;
    }
    
    .back-link:hover .back-icon-wrapper {
        background: rgba(0, 123, 255, 0.2);
        transform: scale(1.1);
    }
    
    .back-text {
        font-weight: 500;
        font-size: 0.95rem;
    }

    
</style>
@endpush
@endsection
