@extends(auth()->user()->role === 'fronttouchpanel' ? 'layouts.app_ftouch' : 'layouts.app_gtouch')

@section('content')
      <div id="error-log" style="position: fixed; top: 10px; left: 10px; color: red; background: white; padding: 10px; max-height: 200px; overflow-y: auto; z-index: 1000;"></div>
    <div class="full-screen bg-dark text-black">
      <!--  <a href="{{ route('touch.dashboard') }}" class="back-arrow">⬅️</a>  -->
        <h1 id="current-time" style="font-size: 4rem;"></h1>
      <!--  <a href="{{ route('touch.corn-futures') }}" class="next-arrow">➡️</a>  -->
    </div>
@endsection

@section('styles')
    <style>
        body {
            background-color: #000000; /* Black background */
        }
    </style>
@endsection

<div class="switch-container">
    <div class="switch-item">
        <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Breakfast Light Switch" class="switch-image" data-ip="192.168.87.46" onclick="toggleSwitch(this)">
        <p>Breakfast Light</p>
    </div>
    <div class="switch-item">
        <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Camper Light Switch" class="switch-image" data-ip="192.168.87.45" onclick="toggleSwitch(this)">
        <p>Camper Light</p>
    </div>
    <div class="switch-item">
        <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Garage Light Switch" class="switch-image" data-ip="192.168.87.47" onclick="toggleSwitch(this)">
        <p>Garage Light</p>
    </div>
</div>
<button class="more-button" onclick="showMoreSwitches()">More</button>

<script>
async function toggleSwitch(imgElement) {
    const ip = imgElement.getAttribute('data-ip');
    const isOn = imgElement.getAttribute('data-state') === 'on';
    const newState = isOn ? 'off' : 'on';
    const newImageSrc = newState === 'on' ? '{{ asset('images/ThrowSwitchOn.png') }}' : '{{ asset('images/ThrowSwitchOff.png') }}';

    try {
        let response = await fetch(`http://${ip}/rpc/Switch.Toggle?id=0`);
        if (response.ok) {
            // Update the image source and state
           // imgElement.src = newImageSrc;
              imgElement.src = `${newImageSrc}?t=${new Date().getTime()}`;
            imgElement.setAttribute('data-state', newState);
        } else {
            console.error('Failed to toggle switch');
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
function showMoreSwitches() {
    document.getElementById('tab-content').innerHTML = `
        <div class="switch-container small-switches">
            <div class="switch-item">
                <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Breakfast Light Switch" class="switch-image small-switch" data-ip="192.168.87.46" onclick="toggleSwitch(this)">
                <p>Breakfast Light</p>
            </div>
            <div class="switch-item">
                <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Camper Light Switch" class="switch-image small-switch" data-ip="192.168.87.45" onclick="toggleSwitch(this)">
                <p>Camper Light</p>
            </div>
            <div class="switch-item">
                <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Garage Light Switch" class="switch-image small-switch" data-ip="192.168.87.47" onclick="toggleSwitch(this)">
                <p>Garage Light</p>
            </div>
            <div class="switch-item">
                <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Living Room Light Switch" class="switch-image small-switch" data-ip="192.168.87.48" onclick="toggleSwitch(this)">
                <p>Living Room Light</p>
            </div>
            <div class="switch-item">
                <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Porch Light Switch" class="switch-image small-switch" data-ip="192.168.87.49" onclick="toggleSwitch(this)">
                <p>Porch Light</p>
            </div>
            <div class="switch-item">
                <img src="{{ asset('images/ThrowSwitchOff.png') }}" alt="Office Light Switch" class="switch-image small-switch" data-ip="192.168.87.50" onclick="toggleSwitch(this)">
                <p>Office Light</p>
            </div>
        </div>
    `;
}
</script>
<style> 
.small-switch {
    width: 50px; /* Adjust this value as needed */
    height: auto;
}
.switch-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}
.switch-item {
    text-align: center;
}
.switch-image {
    width: 100px;
    height: auto;
    cursor: pointer;
    transition: transform 0.2s;
}
.switch-image:hover {
    transform: scale(1.1);
}
.more-button {
    display: block;
    margin: 20px auto;
    padding: 10px 20px;
    font-size: 1.2em;
    cursor: pointer;
}
</style>
