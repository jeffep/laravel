<!-- resources/views/trugreen/show.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>View PDF</title>
    <link rel="stylesheet" href="{{ asset('css/trugreen.css') }}">
</head>
<body>
    <div class="container">
        <h1>View PDF</h1>
        <iframe src="{{ url('/trugreen/' . $directory . '/' . $file) }}" width="100%" height="600px"></iframe>
    </div>
</body>
</html>

