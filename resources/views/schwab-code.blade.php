<!DOCTYPE html>
<html>
<head>
    <title>Enter Schwab Code</title>
</head>
<body>
    <h1>Paste the Schwab Redirect URL</h1>
    @if (session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif
    <form method="POST" action="{{ route('schwab.code.handle') }}">
        @csrf
        <input type="text" name="code" placeholder="e.g., https://127.0.0.1/schwab-callback?code=..." style="width: 400px;" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
