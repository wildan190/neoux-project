<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });

    function handlePrFormSubmit(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitBtn.innerHTML = '<div class="flex items-center gap-2"><div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>Processing...</div>';
        }
        return true;
    }

    function shipOrder(doId) {
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 is not loaded');
            return;
        }
        
        Swal.fire({
            title: 'Mark as Shipped',
            text: 'Please enter the shipping tracking number (Resi):',
            input: 'text',
            inputPlaceholder: 'e.g. JB0018829922',
            showCancelButton: true,
            confirmButtonText: 'Ship Order',
            confirmButtonColor: '#4F46E5', // Indigo-600
            showLoaderOnConfirm: true,
            preConfirm: (trackingNumber) => {
                if (!trackingNumber) {
                    Swal.showValidationMessage('Tracking number is required');
                }
                return trackingNumber;
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('tracking-input-' + doId).value = result.value;
                document.getElementById('ship-form-' + doId).submit();
            }
        });
    }
</script>
