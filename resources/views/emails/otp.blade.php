<!DOCTYPE html>
<html>

<head>
    <title>Your OTP</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css"
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>

<body>
    <p>Dear {{ $info['name'] }},</p>

    <p>{{ $info['body'] }}</p>
    <p>Please approve with OTP: <strong>{{ $info['otp'] }}</strong> which expires in <strong>
            {{ $info['duration']}}</strong> minutes.</p>

    {{-- <a href="{{ url('/') }}" class="btn btn-primary">Validate your OTP</a> --}}
    {{-- <p>This OTP would expire in <strong>{{ $info['duration'] }} mins</strong> at exactly {{ $info['end'] }} </p>
    --}}
</body>

</html>
