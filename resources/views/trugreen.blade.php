@extends('dashboard')

@section('control-content')
    <title>Trugreen Directory</title>
    <div class="container">
        <h1>Trugreen Directory</h1>
        <ul>
            @foreach ($directories as $directory)
                <li>
                    <a href="{{ url('/TRUGREEN/' . basename($directory)) }}">{{ basename($directory) }}</a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
@push('head-styles')
<script>
.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #ffffff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

h1 {
    text-align: center;
    color: #333333;
}

ul {
    list-style-type: none;
    padding: 0;
}

li {
    margin: 10px 0;
}

a {
    text-decoration: none;
    color: #007bff;
}

a:hover {
    text-decoration: underline;
}
</script>
@endpush
