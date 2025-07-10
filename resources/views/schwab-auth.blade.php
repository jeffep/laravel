<!DOCTYPE html>
<html>
<head>
    <title>Schwab Auth</title>
</head>
<body>
    <h1>Schwab Authentication</h1>
    <p>Click <a href="{{ $authUrl }}" target="_blank">here</a> to authenticate with Schwab.</p>
    <p>After approval, copy the full URL from your browser (e.g., https://127.0.0.1/schwab-callback?code=...) and paste it into <a href="{{ route('schwab.code.form') }}">this form</a>.</p>
</body>
</html>
