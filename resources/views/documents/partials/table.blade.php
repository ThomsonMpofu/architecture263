@if($documents->count() > 0)
    <div class="border-0 table-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">
                                #
                            </th>
                            <th class="px-4 py-3">
                                Title
                            </th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 0; @endphp
                        @foreach ($documents as $document)
                                <tr class="table-row">
                                    <td>{{ ++$i }}</td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0 fw-semibold text-dark">{{ $document->file_name }}</h6>
                                            </div>
                                        </div>
                                    </td>                                    
                                    <td class="px-4 py-3 align-middle text-center">                                       
                                            <a href="#" 
                                            class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-download me-1"></i>View
                                            </a>
                                            <a href="#" 
                                                class="btn btn-outline-secondary btn-sm">
                                                Download
                                            </a>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#DeleteMinutesModal" 
                                            class="btn btn-outline-danger btn-sm"
                                            data-id="{{ $document->id }}"
                                            data-title="{{ $document->file_name}}">
                                                Delete
                                            </a>

                                    </td>
                                </tr>
                            
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <!-- Empty State -->
    <div class="text-center py-5">
        <div class="mb-4">
            <i class="fas fa-file-alt text-muted" style="font-size: 4rem;"></i>
        </div>
        <h3 class="text-muted mb-3">No Plans Available</h3>
        <p class="text-muted mb-4">Plans will appear here once they're uploaded.</p>
    </div>
@endif