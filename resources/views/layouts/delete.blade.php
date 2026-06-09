<body>
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-trash text-danger fs-1"></i>
                    <h5 class="mb-4 mt-3">Are you sure you want to delete?</h5>

                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-secondary px-4" type="button" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button class="btn btn-danger px-4" type="submit">
                                delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>


<script>
    document.addEventListener("DOMContentLoaded", function() {

        const deleteButtons = document.querySelectorAll('[data-bs-target="#deleteModal"]');

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {

                let url = this.getAttribute('data-url');

                document.getElementById('deleteForm').action = url;
            });
        });

    });
</script>