<script src="https://app.midtrans.com/snap/snap.js"
    data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
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

    function initSnapPayment() {
        const btn = document.getElementById('snapPayBtn');
        const errMsg = document.getElementById('snapErrorMsg');

        // Show loading state
        btn.disabled = true;
        btn.innerHTML = '<div class="flex items-center gap-2"><div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>Memproses...</div>';
        errMsg.classList.add('hidden');

        fetch('{{ route("procurement.po.escrow-pay", $purchaseOrder) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.snap_token) {
                    // Close our modal first
                    document.getElementById('escrowPayModal').classList.add('hidden');

                    // Open Midtrans Snap as a floating modal
                    snap.pay(data.snap_token, {
                        onSuccess: function (result) {
                            console.log('Payment success', result);
                            // Verify & persist payment server-side (webhook may not work on localhost)
                            fetch('{{ route("procurement.po.escrow-verify", $purchaseOrder) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ order_id: result.order_id })
                            })
                            .then(() => {
                                window.location.href = '{{ route("procurement.midtrans.finish") }}?order_id=' + result.order_id + '&transaction_status=' + result.transaction_status + '&status_code=' + result.status_code;
                            })
                            .catch(() => {
                                // Even if verify fails, redirect so user sees result
                                window.location.href = '{{ route("procurement.midtrans.finish") }}?order_id=' + result.order_id + '&transaction_status=' + result.transaction_status + '&status_code=' + result.status_code;
                            });
                        },
                        onPending: function (result) {
                            console.log('Payment pending', result);
                            fetch('{{ route("procurement.po.escrow-verify", $purchaseOrder) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ order_id: result.order_id })
                            })
                            .finally(() => {
                                window.location.href = '{{ route("procurement.midtrans.finish") }}?order_id=' + result.order_id + '&transaction_status=' + result.transaction_status + '&status_code=' + result.status_code;
                            });
                        },
                        onError: function (result) {
                            console.error('Payment error', result);
                            window.location.href = '{{ route("procurement.midtrans.finish") }}?order_id=' + (result.order_id || '') + '&transaction_status=failed&status_code=400';
                        },
                        onClose: function () {
                            // User closed without paying — just re-enable the button
                            document.getElementById('escrowPayModal').classList.remove('hidden');
                            btn.disabled = false;
                            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg> Bayar Sekarang';
                            if (typeof feather !== 'undefined') feather.replace();
                        }
                    });
                } else if (data.error) {
                    errMsg.textContent = 'Gagal memproses pembayaran: ' + data.error;
                    errMsg.classList.remove('hidden');
                    btn.disabled = false;
                    btn.innerHTML = '<i data-feather="credit-card" class="w-5 h-5"></i> Bayar Sekarang';
                    if (typeof feather !== 'undefined') feather.replace();
                }
            })
            .catch(err => {
                console.error('Fetch error', err);
                errMsg.textContent = 'Terjadi kesalahan koneksi. Silakan coba lagi.';
                errMsg.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = '<i data-feather="credit-card" class="w-5 h-5"></i> Bayar Sekarang';
                if (typeof feather !== 'undefined') feather.replace();
            });
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
            confirmButtonColor: '#4F46E5',
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