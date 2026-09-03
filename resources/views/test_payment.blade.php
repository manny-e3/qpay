<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Payment Integration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .payment-card { max-width: 500px; margin: 50px auto; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-pay { background-color: #09ad95; border: none; padding: 12px; font-weight: bold; color: white; }
        .btn-pay:hover { background-color: #078a77; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="card payment-card">
        <div class="card-body p-5">
            <h3 class="text-center mb-4">Sample Checkout</h3>
            <div class="mb-4 text-center">
                <img src="https://via.placeholder.com/100" alt="Product" class="img-fluid rounded mb-2">
                <h5>Premium Package</h5>
                <p class="text-muted">₦2,500.00</p>
            </div>

            <form id="paymentForm">
                <input type="hidden" id="appID" value="{{ $app->appID ?? '101' }}">
                <input type="hidden" id="amount" value="2500">
                
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" id="email" class="form-control" placeholder="customer@example.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" id="name" class="form-control" placeholder="John Doe">
                </div>

                <button type="submit" id="payBtn" class="btn btn-pay w-100 mt-3">
                    Pay Now
                </button>
            </form>

            <div id="responseMsg" class="mt-4 d-none">
                <div class="alert alert-info"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        
        const payBtn = $('#payBtn');
        const responseMsg = $('#responseMsg');
        
        payBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');
        responseMsg.addClass('d-none');

        const payload = {
            appID: $('#appID').val(),
            amount: $('#amount').val(),
            email: $('#email').val(),
            callback_url: window.location.href, // Redirect back here
            metadata: {
                customer_name: $('#name').val(),
                product: 'Premium Package'
            }
        };

        $.ajax({
            url: '{{ url("api/payment/initiate") }}',
            method: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = response.data.checkout_url;
                } else {
                    responseMsg.removeClass('d-none').find('.alert').removeClass('alert-info').addClass('alert-danger').text(response.message);
                    payBtn.prop('disabled', false).text('Pay Now');
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
                responseMsg.removeClass('d-none').find('.alert').removeClass('alert-info').addClass('alert-danger').text(msg);
                payBtn.prop('disabled', false).text('Pay Now');
            }
        });
    });

    // Check for callback params in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('status')) {
        const status = urlParams.get('status');
        const reference = urlParams.get('reference');
        const responseMsg = $('#responseMsg');
        
        responseMsg.removeClass('d-none');
        if (status === 'successful') {
            responseMsg.find('.alert').addClass('alert-success').html('<strong>Success!</strong> Payment was successful. Ref: ' + reference);
        } else {
            responseMsg.find('.alert').addClass('alert-danger').html('<strong>Failed!</strong> Payment was not successful. Ref: ' + reference);
        }
    }
</script>

</body>
</html>
