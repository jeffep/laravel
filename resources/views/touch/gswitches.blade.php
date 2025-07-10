@extends('layouts.app_ftouch')

@section('content')
<div class="switch-container">
    <div class="switch-item original-switch">
            <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Breakfast Light Switch" class="switch-image" data-ip="192.168.87.46" data-state="off" onclick="toggleSwitch(this)">
            <p>Breakfast Light</p>
    </div>
    <div class="switch-item original-switch">
            <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Camper Light Switch" class="switch-image" data-ip="192.168.87.45" data-state="off" onclick="toggleSwitch(this)">
            <p>Camper Light</p>
    </div>
    <div class="switch-item original-switch">
            <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Garage Light Switch" class="switch-image" data-ip="192.168.87.47" data-state="off" onclick="toggleSwitch(this)">
            <p>Garage Light</p>
    </div>
</div>
<div class="more-less-buttons">
    <button class="more-button" onclick="showMoreSwitches()">More</button>
    <button class="less-button" onclick="showLessSwitches()" style="display: none;">Less</button>
</div>

<script>
async function toggleSwitch(imgElement) {
    console.log('Toggle switch function called');

    const ip = imgElement.getAttribute('data-ip');
    const isOn = imgElement.getAttribute('data-state') === 'on';
    const newState = isOn ? 'off' : 'on';
    const newImageSrc = newState === 'on' ? '{{ asset('images/ThrowSwitchOn.png') }}' : '{{ asset('images/ThrowSwitchOff.png') }}';

    console.log('Current state:', isOn ? 'on' : 'off');
    console.log('New state:', newState);
    console.log('New image source:', newImageSrc);

    try {
        console.log('Sending request to toggle switch...');
        // Send the request but don't wait for the response
        fetch(`http://${ip}/rpc/Switch.Toggle?id=0`, {
            mode: 'no-cors', // Ignore CORS issues
        }).then(() => {
            console.log('Switch toggled (assumed success)');
        }).catch((error) => {
            console.error('Error sending request (ignored):', error);
        });

        // Update the image and state immediately
        imgElement.src = `${newImageSrc}?t=${new Date().getTime()}`;
        imgElement.setAttribute('data-state', newState);
        console.log('Image source updated to:', imgElement.src);
    } catch (error) {
        console.error('Error toggling switch:', error);
    }
}


function showMoreSwitches() {
    const switchContainer = document.querySelector('.switch-container');
    const moreButton = document.querySelector('.more-button');
    const lessButton = document.querySelector('.less-button');

    // Add 3 new switches
    const newSwitches = `
        <div class="switch-item small-switch">
        <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Front Foyer Switch" class="switch-image" data-ip="192.168.87.53" data-state="off" onclick="toggleSwitch(this)">
        <p>Front Foyer Light</p>
        </div>
        <div class="switch-item small-switch">
        <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Porch Light Switch" class="switch-image" data-ip="192.168.87.54" data-state="off" onclick="toggleSwitch(this)">
        <p>Porch Light</p>
        </div>
        <div class="switch-item small-switch">
        <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Lamp Post Light Switch" class="switch-image" data-ip="192.168.87.52" data-state="off" onclick="toggleSwitch(this)">
        <p>Lamp Post Light</p>
        </div>
    `;

    // Append the new switches to the container
    switchContainer.insertAdjacentHTML('beforeend', newSwitches);

    // Change the size of the original switches to 100px
    const originalSwitches = document.querySelectorAll('.switch-item:not(.small-switch)');
    originalSwitches.forEach(switchItem => {
        switchItem.classList.add('small-switch');
    });

    // Hide the "More" button and show the "Less" button
    moreButton.style.display = 'none';
    lessButton.style.display = 'inline-block';

    // Reattach event listeners to new switches
    attachEventListeners();
}

function showLessSwitches() {
    const switchContainer = document.querySelector('.switch-container');
    const moreButton = document.querySelector('.more-button');
    const lessButton = document.querySelector('.less-button');

    // Remove only the additional switches
    const additionalSwitches = document.querySelectorAll('.switch-item.small-switch');
    additionalSwitches.forEach(switchItem => {
        if (!switchItem.classList.contains('original-switch')) {
            switchItem.remove();
        }
    });

    // Change the size of the original switches back to 200px
    const originalSwitches = document.querySelectorAll('.switch-item');
    originalSwitches.forEach(switchItem => {
        switchItem.classList.remove('small-switch');
    });

    // Show the "More" button and hide the "Less" button
    moreButton.style.display = 'inline-block';
    lessButton.style.display = 'none';
}

function attachEventListeners() {
    document.querySelectorAll('.switch-image').forEach(img => {
        img.addEventListener('touchstart', () => {
            img.classList.add('scale-up');
        });

        img.addEventListener('touchend', () => {
            img.classList.remove('scale-up');
        });

        img.addEventListener('mouseenter', () => {
            img.classList.add('scale-up');
        });

        img.addEventListener('mouseleave', () => {
            img.classList.remove('scale-up');
        });
    });
}

// Attach event listeners to existing switches on page load
attachEventListeners();
</script>

<style>
.switch-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    padding: 20px;
}

.switch-item {
    text-align: center;
    width: 200px; /* Default size for large switches */
}

.switch-item.small-switch {
    width: 100px; /* Size for small switches */
}

.switch-image {
    width: 100%;
    height: auto;
    cursor: pointer;
    transition: transform 0.2s;
}

.switch-image.scale-up {
    transform: scale(1.1);
}

.more-less-buttons {
    text-align: center;
    margin: 20px 0;
}

.more-button, .less-button {
    padding: 10px 20px;
    font-size: 1.2em;
    cursor: pointer;
}
</style>
