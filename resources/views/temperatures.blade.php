<!-- resources/views/test3.blade.php -->


    <!-- Include Highcharts library -->
    @push('head-scripts')
     <script src="https://code.highcharts.com/12.1/highcharts.js"></script>
    @endpush
@push('styles')
    <style>
        .chart-wrapper {
            text-align: center;
            margin: 20px;
        }
        .chart-title {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .chart-container {
            display: inline-block;
            width: 45%; /* Adjust the width as needed */
            height: 300px; /* Adjust the height as needed */
            margin: 10px;
        }
    </style>
@endpush

@section('control-content')

    <div class="chart-wrapper">
    <div class="chart-title">Work Room Environment</div>


    <!-- Container for the temperature chart -->
    <div class="chart-container" id="temperature-chart"></div>

    <!-- Container for the humidity chart -->
    <div class="chart-container" id="humidity-chart"></div>
    </div> // Chart wrapper
@endsection

@section('body-inline-scripts')
    <script>
            //The controller should have opened the file and skipped to the last
              300 lines, and then pass it to us here.
            document.addEventListener('DOMContentLoaded', function() {

            if (typeof Highcharts === 'undefined') {
                   console.error('Highcharts library not loaded!');
            } else { 
                   var docBody = document.body;
                   var closestLang = docBody.closest ? docBody.closest("[lang]") : null;
                   var pageLang = closestLang ? closestLang.lang : undefined;

              // Get the last 300 entries passed from the controller
              const data = @json($last300Entries);

             // Function to convert epoch timestamps to readable dates
              function convertEpochToReadableDates(epochTimes) {
                  return epochTimes.map(epochTime => {
                      const timestamp = new Date(epochTime * 1000); // Convert to milliseconds
                      return timestamp.toLocaleString(); // Adjust format as needed
                  });
              }

              // Extract temperature and humidity arrays from the last 300 entries
              const epochTimes = data.map(entry => entry.Time);
              const temperatures = data.map(entry => parseFloat(entry.Temperature));
              const humidities = data.map(entry => parseFloat(entry.Humidity));

              // Convert Epoch time to readable date
              const formatted_dates = convertEpochToReadableDates(epochTimes);

              // Now you can use formatted_dates, temperatures, and humidities to create your charts
              console.log('Formatted dates:', formatted_dates);
              console.log('Temperatures:', temperatures);
              console.log('Humidities:', humidities);

                    // Create temperature chart
                    Highcharts.chart('temperature-chart', {
                       title: { text: 'Temperature Chart' },
                       xAxis: {
                           type: 'datetime', // Specify time-based x-axis
                           categories: formatted_dates, // Your array of timestamps
                       },
                       series: [{ name: 'Temperature', data: temperatures }]
                    });

                    // Create humidity chart
                    Highcharts.chart('humidity-chart', {
                       title: { text: 'Humidity Chart' },
                       xAxis: {
                          type: 'datetime', // Specify time-based x-axis
                          categories: formatted_dates, // Your array of timestamps
                       },
                       series: [{ name: 'Humidity', data: humidities }]
                    });
                })
                .catch(error => console.error('Error fetching data:', error));
              }
           });

          // Function to convert Epoch times to readable dates
       function convertEpochToReadableDates(epochTimes) {
          const readableDates = [];

          epochTimes.forEach(epochTime => {
             const timestamp = new Date(epochTime * 1000); // Convert to milliseconds
             const formattedDate = timestamp.toLocaleString(); // Adjust format as needed
             readableDates.push(formattedDate);
             // Log each formatted date for debugging
             console.log(`Epoch ${epochTime}: ${formattedDate}`);
          });
          return readableDates;
       }

    </script>
@endsection
