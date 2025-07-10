<!-- resources/views/shelly_status.blade.php -->

@extends('dashboard')

@section('control-content')
    <div class="container">
        <h1>Shelly Status</h1>

        @if(isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif
                <!-- LOOP THROUGH EACH SHELLY -->
               <div class="container">

                 <div id="loading" class="loading">
                     <i class="fas fa-hourglass-half"></i> Waiting for status...
                 </div>

                 <div class="grid-container">
                  @foreach($shellyStatus as $shelly)
                     <div class="section-title">{{ $shelly['id'] }}, {{ $shelly['ip'] }}
                     @if(isset($shelly['status']))
                        @if($shelly['statusType'] === 1) 
                         <form action="{{ route('shelly.toggle') }}" method="POST">
                             @csrf
                             <label class="switch">
                                @foreach($shelly['status']['relays'] ?? [] as $relay)
                                   <input type="hidden" name="shelly_id" value="{{ $shelly['id'] }}">
                                   <input type="hidden" name="shelly_ip" value="{{ $shelly['ip'] }}">
                                   <input type="checkbox" name="status" value="on" onchange="this.form.submit()"
                                     {{ $relay['ison'] ? 'checked' : '' }}>
                                @endforeach
                                <span class="slider"></span>
                             </label>
                         </form>
                      </div> <!-- title -->

                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">WiFi Status</div>
                                    <div class="box-body">
                 <p><strong>Connected:</strong> {{ $shelly['status']['wifi_sta']['connected'] ?? 'unknown' ? 'yes' : 'no' }} </p>
                                        <p><strong>SSID:</strong> {{ $shelly['status']['wifi_sta']['ssid'] ?? 'N/A' }}</p>
                                        <p><strong>IP:</strong> {{ $shelly['status']['wifi_sta']['ip'] ?? 'N/A' }}</p>
                                        <p><strong>RSSI:</strong> {{ $shelly['status']['wifi_sta']['rssi'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
        
                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">Cloud Status</div>
                                    <div class="box-body">
                                       <p><strong>Enabled:</strong> {{ $shelly['status']['cloud']['enabled'] ?? 'N/A' }}</p>
                                       <p><strong>Connected:</strong> {{ $shelly['status']['cloud']['connected'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">MQTT Status</div>
                                    <div class="box-body">
                                        <p><strong>Connected:</strong> {{ $shelly['status']['mqtt']['connected'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
 
                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">Device Info</div>
                                    <div class="box-body">
                                        <p><strong>Time:</strong> {{ $shelly['status']['time'] ?? 'N/A' }}</p>
                                        <p><strong>Unix Time:</strong> {{ $shelly['status']['unixtime'] ?? 'N/A' }}</p>
                                        <p><strong>Serial:</strong> {{ $shelly['status']['serial'] ?? 'N/A' }}</p>
                                        <p><strong>MAC:</strong> {{ $shelly['status']['mac'] ?? 'N/A' }}</p>
                                        <p><strong>Uptime:</strong> {{ $shelly['status']['uptime'] ?? 'N/A' }} seconds</p>
                                    </div>
                                </div>
                            </div>
 
                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">Temperature</div>
                                    <div class="box-body">
                                        <p><strong>Temperature (C):</strong> {{ $shelly['status']['temperature'] ?? 'N/A' }}°C</p>
                                        <p><strong>Temperature (F):</strong> {{ $shelly['status']['tmp']['tF'] ?? 'N/A' }}°F</p>
                                        <p><strong>Status:</strong> {{ $shelly['status']['temperature_status'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
 
                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">Relays</div>
                                    <div class="box-body">
                                        @foreach($shelly['status']['relays'] ?? [] as $relay)
                                            <p><strong>Is On:</strong> {{ $relay['ison'] ? 'Yes' : 'No' }}</p>
                                            <p><strong>Source:</strong> {{ $relay['source'] }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
 
                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">Meters</div>
                                    <div class="box-body">
                                        @foreach($shelly['status']['meters'] ?? [] as $meter)
                                            <p><strong>Power:</strong> {{ $meter['power'] }} W</p>
                                            <p><strong>Total:</strong> {{ $meter['total'] }} Wh</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
    
                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">System Info</div>
                                    <div class="box-body">
                                        <p><strong>RAM Total:</strong> {{ $shelly['status']['ram_total'] ?? 'N/A' }} KB</p>
                                        <p><strong>RAM Free:</strong> {{ $shelly['status']['ram_free'] ?? 'N/A' }} KB</p>
                                        <p><strong>FS Size:</strong> {{ $shelly['status']['fs_size'] ?? 'N/A' }} KB</p>
                                        <p><strong>FS Free:</strong> {{ $shelly['status']['fs_free'] ?? 'N/A' }} KB</p>
                                    </div>
                                </div>
                            </div>
      <!-- OLD STYLE FORMAT -->
                       @elseif ($shelly['statusType'] === 2) 
                           <form action="{{ route('shelly.toggle2') }}" method="POST">
                             @csrf
                             <label class="switch">
                                   <input type="hidden" name="shelly_id" value="{{ $shelly['id'] }}">
                                   <input type="hidden" name="shelly_ip" value="{{ $shelly['ip'] }}">
                                   <input type="checkbox" name="status" value="on" onchange="this.form.submit()"
                                     {{ $shelly['status']['switch:0']['output'] ? 'checked' : '' }}>
                                <span class="slider"></span>
                             </label>
                         </form>
                       </div>  <!-- title bar -->
                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">WiFi Status</div>
                                    <div class="box-body">
                 <p><strong>Connected:</strong> {{ $shelly['status']['wifi']['status'] ?? 'unknown' ? 'yes' : 'no' }} </p>
                                        <p><strong>SSID:</strong> {{ $shelly['status']['wifi']['ssid'] ?? 'N/A' }}</p>
                                        <p><strong>IP:</strong> {{ $shelly['status']['wifi']['sta_ip'] ?? 'N/A' }}</p>
                                        <p><strong>RSSI:</strong> {{ $shelly['status']['wifi']['rssi'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">Cloud Status</div>
                                    <div class="box-body">
                                       <p><strong>Connected:</strong> {{ $shelly['status']['cloud']['connected'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">MQTT Status</div>
                                    <div class="box-body">
                                        <p><strong>Connected:</strong> {{ $shelly['status']['mqtt']['connected'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">Device Info</div>
                                    <div class="box-body">
                                        <p><strong>Time:</strong> {{ $shelly['status']['sys']['time'] ?? 'N/A' }}</p>
                                        <p><strong>Unix Time:</strong> {{ $shelly['status']['sys']['unixtime'] ?? 'N/A' }}</p>
                                        <p><strong>MAC:</strong> {{ $shelly['status']['sys']['mac'] ?? 'N/A' }}</p>
                                        <p><strong>Uptime:</strong> {{ $shelly['status']['sys']['uptime'] ?? 'N/A' }} seconds</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">Temperature</div>
                                    <div class="box-body">
                                        <p><strong>Temperature (C):</strong> {{ $shelly['status']['switch:0']['temperature']['tc'] ?? 'N/A' }}°C</p>
                                        <p><strong>Temperature (F):</strong> {{ $shelly['status']['switch:0']['temperature']['tF'] ?? 'N/A' }}°F</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">Relays</div>
                                    <div class="box-body">
                                            <p><strong>Is On:</strong> {{ $shelly['status']['switch:0']['output'] ? 'Yes' : 'No' }}</p>
                                            <p><strong>Source:</strong> {{ $shelly['status']['switch:0']['source'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">Meters</div>
                                    <div class="box-body">
                                            <p><strong>Power:</strong> {{ $shelly['status']['switch:0']['apower'] ?? 'N/A' }} W</p>
                                            <p><strong>Volts:</strong> {{ $shelly['status']['switch:0']['voltage'] ?? 'N/A' }} V</p>
                                            <p><strong>Total:</strong> {{ $shelly['status']['switch:0']['aenergy']['total'] ?? 'N/A' }} Wh</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-box mb-3">
                                    <div class="box-header">System Info</div>
                                    <div class="box-body">
                                        <p><strong>RAM Total:</strong> {{ $shelly['status']['sys']['ram_size'] ?? 'N/A' }} KB</p>
                                        <p><strong>RAM Free:</strong> {{ $shelly['status']['sys']['ram_free'] ?? 'N/A' }} KB</p>
                                        <p><strong>FS Size:</strong> {{ $shelly['status']['sys']['fs_size'] ?? 'N/A' }} KB</p>
                                        <p><strong>FS Free:</strong> {{ $shelly['status']['sys']['fs_free'] ?? 'N/A' }} KB</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                     @else
                         <div class="alert alert-warning">
                             No status data available.
                         </div>
                     @endif
                  @endforeach
                </div>    <!-- End of Grid Container -->
              </div> <!-- End of container -->
    @endsection

@push('head-styles')
<style>
        .loading {
            display: none;
            font-size: 24px;
            color: #007bff;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #28a745;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }
    .grid-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* Equal-width columns */
    gap: 1px; /* Small gap between boxes for gridlines */
    border: 2px solid #ccc; /* Add a border around the entire grid */
    }

    .custom-box {
    background-color: #FFFFFF;
    padding: 5px;
    margin-bottom: 10px;
    border-radius: 5px;
    font-size: 0.9em;
    width: 20ch;
    border: 2px solid #ccc; /* Add a border to each individual box */
    }

    .section-title {
            grid-column: 1 / span 4; /* Span all columns */
            background-color: papayawhip;
            text-align: center;
            font-weight: bold;
            padding: 10px;
            border: 4px solid #000000;
    }

    .box-header {
        font-weight: bold;
        margin-bottom: 10px;
    }
    .box-body p {
        margin: 0;
    }
    form {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .btn {
        padding: 10px 20px;
        font-size: 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-success {
        background-color: #28a745;
        color: #ffffff;
    }

   .btn-success:hover {
       background-color: #218838;
   }

   .btn-danger {
       background-color: #dc3545;
       color: #ffffff;
   }

   .btn-danger:hover {
       background-color: #c82333;
   }
</style>
@endpush
