<DOCTYPE html>
<html>
<head>
    <title>Fertilizer Application Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
</head>
<body>
<canvas id="myChart" width="400" height="200"></canvas>
<canvas id="myChartByYear" width="400" height="200"></canvas>
<script>
    const rawData = @json($data);

    // Function to convert month name to month number
    function getMonthNumber(monthName) {
        const monthNames = ["January", "February", "March", "April", "May", "June", 
                            "July", "August", "September", "October", "November", "December"];
        return monthNames.indexOf(monthName);
    }

    const data = rawData.map(function(item) {
        return {
            x: new Date(2024, getMonthNumber(item.month), new Date(item.date).getDate()), // Use a generic year like 2024
            y: item.fertilizer,
            amount: item.amount,
            year: item.year
        };
    });

    // Function to get color based on year
    function getColorByYear(year) {
        const colors = {
            2018: 'rgba(255, 99, 132, 0.6)',
            2019: 'rgba(54, 162, 235, 0.6)',
            2020: 'rgba(255, 206, 86, 0.6)',
            2021: 'rgba(75, 192, 192, 0.6)',
            2022: 'rgba(153, 102, 255, 0.6)',
            2023: 'rgba(255, 159, 64, 0.6)',
            2024: 'rgba(201, 203, 207, 0.6)'
        };
        return colors[year] || 'rgba(201, 203, 207, 0.6)'; // Default color if year not found
    }

    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Fertilizer, Weed, & Bug Killer Applications',
                data: data,
                backgroundColor: data.map(item => getColorByYear(item.year)),
                borderColor: data.map(item => getColorByYear(item.year).replace('0.6', '1')),
                borderWidth: 1,
                pointStyle: 'rect', // Use small squares
                pointRadius: 10 // Increase the size of the squares
            }]
        },
        options: {
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'month',
                        displayFormats: {
                            month: 'MMM' // Display months as short names (Jan, Feb, etc.)
                        }
                    },
                    title: {
                        display: true,
                        text: 'Month'
                    },
                    min: new Date(2024, 0, 1).getTime(), // January 1st
                    max: new Date(2024, 11, 31).getTime() // December 31st
                },
                y: {
                    type: 'category',
                    labels: ['FERTILIZER, PRREMERGENT & WEED CONTROL'],
                    title: {
                        display: true,
                        text: 'Type of Fertilizer'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const date = new Date(context.raw.x);
                            const formattedDate = `${date.getMonth() + 1}/${date.getDate()}/${context.raw.year}`; // Use the actual year
                            return `Date: ${formattedDate}, Fertilizer: ${context.raw.y}, Amount: ${context.raw.amount}`;
                        }
                    }
                }
            }
        }
    });

    // Second chart with years on x-axis
    const dataByYear = rawData.map(function(item) {
        return {
            x: new Date(item.year, getMonthNumber(item.month), new Date(item.date).getDate()),
            y: item.fertilizer,
            amount: item.amount,
            year: item.year
        };
    });

    const ctxByYear = document.getElementById('myChartByYear').getContext('2d');
    const myChartByYear = new Chart(ctxByYear, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Fertilizer Application by Year',
                data: dataByYear,
                backgroundColor: dataByYear.map(item => getColorByYear(item.year)),
                borderColor: dataByYear.map(item => getColorByYear(item.year).replace('0.6', '1')),
                borderWidth: 1,
                pointStyle: 'rect', // Use small squares
                pointRadius: 10 // Increase the size of the squares
            }]
        },
        options: {
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'year',
                        displayFormats: {
                            year: 'yyyy' // Display years
                        }
                    },
                    title: {
                        display: true,
                        text: 'Year'
                    },
                    min: new Date(2018, 0, 1).getTime(), // January 1st, 2018
                    max: new Date(2024, 11, 31).getTime() // December 31st, 2024
                },
                y: {
                    type: 'category',
                    labels: ['FERTILIZER, PRREMERGENT & WEED CONTROL'],
                    title: {
                        display: true,
                        text: 'Type of Fertilizer'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const date = new Date(context.raw.x);
                            const formattedDate = `${date.getMonth() + 1}/${date.getDate()}/${context.raw.year}`; // Use the actual year
                            return `Date: ${formattedDate}, Fertilizer: ${context.raw.y}, Amount: ${context.raw.amount}`;
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>


