OTP_TYPE={{ $appConfig->type }}
OTP_LENGTH={{ $appConfig->otp_length }}
OTP_APP_ID={{ $appConfig->appID }}
OTP_USERNAME={{ $appConfig->username }}
OTP_PASSWORD={{ $appConfig->password }}
OTP_URL={{ url('/api/master') }}

---
Connection Instructions:
These credentials must be sent as HTTP Headers in your requests:
- ID: {{ $appConfig->appID }}
- Username: {{ $appConfig->username }}
- Password: {{ $appConfig->password }}

The API expects a POST request to {{ url('/api/master/generator') }}
with a JSON body like:
{
  "appID": "{{ $appConfig->appID }}",
  "username": "recipient@email.com",
  "name": "Recipient Name"
}
