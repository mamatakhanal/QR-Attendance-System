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
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Set delete URL when Delete button is clicked
        document.addEventListener('click', function(e) {

            const deleteButton = e.target.closest(
                '[data-bs-target="#deleteModal"]'
            );

            if (!deleteButton) {
                return;
            }

            const url = deleteButton.getAttribute('data-url');

            document.getElementById('deleteForm').setAttribute(
                'action',
                url
            );
        });


        // Handle delete form without leaving the page
        const deleteForm = document.getElementById('deleteForm');

        if (deleteForm) {

            deleteForm.addEventListener('submit', function(e) {

                e.preventDefault();

                const form = this;
                const url = form.getAttribute('action');

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'input[name="_token"]'
                            ).value,
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    })
                    .then(response => response.json())
                    .then(data => {

                        if (data.success) {

                            // Close delete modal
                            const modalElement =
                                document.getElementById('deleteModal');

                            const modal =
                                bootstrap.Modal.getInstance(modalElement);

                            if (modal) {
                                modal.hide();
                            }

                            // Show success toast
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,

                                customClass: {
                                    popup: 'small-toast'
                                },

                                showClass: {
                                    popup: 'animate__animated animate__fadeInRight'
                                },

                                hideClass: {
                                    popup: 'animate__animated animate__fadeOutRight'
                                }
                            });

                            // Reload after popup
                            setTimeout(function() {
                                location.reload();
                            }, 1000);

                        } else {

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: data.message || 'Delete failed',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });

                        }

                    })
                    .catch(error => {

                        console.error(error);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Something went wrong',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });

                    });

            });
        }

    });
</script>
