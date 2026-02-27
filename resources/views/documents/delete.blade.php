<div class="modal fade" id="DeleteDocumentsModal" tabindex="-1"aria-labelledby="DeleteDocumentsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('documents.destroy', ['document' => '__id__']) }}" id="deleteDocumentsForm">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Doccument</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this?</p>
                    <p class="fw-bold text-danger mb-0" id="deleteDocumentsTitle"></p>
                </div>
                <div class="modal-footer d-flex gap-1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('DeleteDocumentsModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const title = button.getAttribute('data-title');

        const form = document.getElementById('deleteDocumentsForm');
        form.action = "{{ route('documents.destroy', '__id__') }}".replace('__id__', id);

        document.getElementById('deleteDocumentsTitle').textContent = title;
    });

    deleteModal.addEventListener('hidden.bs.modal', function () {
        const form = document.getElementById('deleteDocumentsForm');
        form.action = "{{ route('documents.destroy', '__id__') }}";
    });
});
</script>