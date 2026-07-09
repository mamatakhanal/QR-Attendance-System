<!-- Animate CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .small-toast {
        width: 280px !important;
        min-height: 50px !important;
        padding: 10px !important;
        font-size: 13px !important;
    }
</style>

@if (session('success'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ session('success') }}',
            customClass: {
                popup: 'small-toast'
            },

            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,

            showClass: {
                popup: 'animate__animated animate__slideInRight'
            },

            hideClass: {
                popup: 'animate__animated animate__slideOutRight'
            }
        });
    </script>
@endif

{{-- Error Toast --}}
@if (session('error'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session('error') }}',
            customClass: {
                popup: 'small-toast'
            },
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            showClass: {
                popup: 'animate__animated animate__slideInRight'
            },
            hideClass: {
                popup: 'animate__animated animate__slideOutRight'
            }
        });
    </script>
@endif
