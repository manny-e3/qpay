<!DOCTYPE html>
<html>

<head>
    <title>OTP Page</title>
    <link rel="stylesheet" href="https://adgtest.fmdqgroup.com/otp/public/assets/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="otp-container">
        <div class="otp-container-div">
            <div class="logo">
                <img src="https://adgtest.fmdqgroup.com/otp/public/assets/FMDQ-Logo.png" alt="FMDQ FMDQ-Logo">
            </div>
            <h2>Enter OTP</h2>
            <p class="desc">Please enter the OTP sent to your email address for verification</p>
            <!-- We can change the pattern to accomodate alphanumeric -->
            <div class="otp-inputs">
                <input type="text" maxlength="1" pattern="[0-9]" required />
                <input type="text" maxlength="1" pattern="[0-9]" required />
                <input type="text" maxlength="1" pattern="[0-9]" required />
                <input type="text" maxlength="1" pattern="[0-9]" required />
                <input type="text" maxlength="1" pattern="[0-9]" required />
                <input type="text" maxlength="1" pattern="[0-9]" required />
            </div>
            <button class="btn">Submit</button>
            <p class="resend-desc">Didn’t Receive the OTP?</p>
            <p class="resend">Resend Code</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const otpInputs = document.querySelectorAll('.otp-inputs input');

            otpInputs.forEach(function (input, index) {
                input.addEventListener('input', function () {
                    if (this.value.length >= 1) {
                        // Move to the next input box if there's one
                        if (index < otpInputs.length - 1) {
                            otpInputs[index + 1].focus();
                        }
                    } else {
                        // Move to the previous input box if there's one
                        if (index > 0) {
                            otpInputs[index - 1].focus();
                        }
                    }
                });

                // Automatically move to the next input when the current input is filled
                input.addEventListener('keydown', function (event) {
                    if (event.key === 'Backspace' && this.value.length === 0) {
                        // Move to the previous input box if there's one
                        if (index > 0) {
                            otpInputs[index - 1].focus();
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>