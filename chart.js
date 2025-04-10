document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar', // Bar chart for sales revenue
        data: {
            labels: ['January', 'February', 'March', 'April', 'May'],
            datasets: [
                {
                    label: 'Sales Revenue',
                    data: [12, 19, 3, 5, 2], // Monthly sales data
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    type: 'bar' // Bar chart type for sales revenue
                },
                {
                    label: 'Canceled Orders Revenue',
                    data: [5, 2, 7, 3, 1], // Monthly canceled orders data
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    fill: false, // No fill for the line chart
                    type: 'line', // Line chart type for canceled orders
                    tension: 0.4 // Smooth line for canceled orders
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true // Ensure the Y-axis starts at 0
                }
            }
        }
    });
});
