@extends('layouts.app-gtouch')

@section('content')
    <div class="full-screen">
        <img id="slideshow-image" src="" alt="Slideshow" style="max-width: 100%; max-height: 80%;">
        <a href="{{ route('garagetablet.home') }}" class="next-arrow">➡️</a>
    </div>
@endsection

@section('scripts')
    <script>
        const images = [
            @php
                $files = File::files(public_path('images/slideshow'));
                foreach ($files as $file) {
                    echo "'" . asset('images/slideshow/' . $file->getFilename()) . "',";
                }
            @endphp
        ];
        let currentIndex = 0;
        const slideshowImage = document.getElementById('slideshow-image');

        function showNextImage() {
            if (images.length > 0) {
                slideshowImage.src = images[currentIndex];
                currentIndex = (currentIndex + 1) % images.length;
            }
        }

        showNextImage();
        setInterval(showNextImage, 5000); // Change image every 5 seconds
    </script>
@endsection
