@extends(auth()->user()->role === 'fronttouchpanel' ? 'layouts.app_ftouch' : 'layouts.app_gtouch')

@section('content')
    <h1>Slideshow</h1>
    <div class="slideshow-container" style="max-width: 800px; max-height: 480px; margin: auto; position: relative;">
        <img id="slideshow-image" src="" alt="Slideshow Image" style="width: 100%; height: auto; object-fit: contain;">
        <div class="controls" style="position: absolute; bottom: 10px; width: 100%; text-align: center;">
            <button onclick="prevSlide()" class="btn btn-secondary">Previous</button>
            <button onclick="nextSlide()" class="btn btn-secondary">Next</button>
        </div>
    </div>

    <script>
        // Array to hold image paths (replace with your actual image paths)
        const images = [
            @php
                $directory = public_path('slideshow');
                $files = File::files($directory);
                $imagePaths = array_filter($files, function($file) {
                    return in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'gif']);
                });
                foreach ($imagePaths as $file) {
                    echo '"' . asset('slideshow/' . $file->getFilename()) . '",';
                }
            @endphp
        ];

        let currentIndex = 0;
        const slideshowImage = document.getElementById('slideshow-image');

        // Function to display the current image
        function showImage(index) {
            if (images.length > 0) {
                slideshowImage.src = images[index];
            } else {
                slideshowImage.src = ''; // Fallback if no images
                slideshowImage.alt = 'No images available';
            }
        }

        // Function to go to the next slide
        function nextSlide() {
            currentIndex = (currentIndex + 1) % images.length;
            showImage(currentIndex);
        }

        // Function to go to the previous slide
        function prevSlide() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            showImage(currentIndex);
        }

        // Auto-cycle through images every 5 seconds
        let slideshowInterval = setInterval(nextSlide, 5000);

        // Pause auto-cycle on hover (optional)
        slideshowImage.addEventListener('mouseenter', () => {
            clearInterval(slideshowInterval);
        });

        // Resume auto-cycle when not hovering
        slideshowImage.addEventListener('mouseleave', () => {
            slideshowInterval = setInterval(nextSlide, 5000);
        });

        // Show the first image on load
        showImage(currentIndex);
    </script>

    <style>
        .slideshow-container {
            width: 100%;
            max-width: 800px;
            max-height: 480px;
            overflow: hidden;
            background: #000; /* Black background for better image visibility */
        }
        .btn-secondary {
            background-color: #555;
            color: #fff;
            padding: 5px 10px;
            margin: 0 5px;
            border: none;
            cursor: pointer;
        }
        .btn-secondary:hover {
            background-color: #777;
        }
    </style>
@endsection
