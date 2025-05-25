<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
// PIE CHART
const pieCtx = document.getElementById("disabilityPie").getContext("2d");
const pieLabels = Object.keys(data.disabilityCounts);
const pieData = Object.values(data.disabilityCounts);
const pieColors = [
    "#4B61D1", "#F07C46", "#9D4EDD", "#00B8A9", "#F76C6C", "#6C5B7B"
];

new Chart(pieCtx, {
    type: "pie",
    data: {
        labels: pieLabels,
        datasets: [{
            data: pieData,
            backgroundColor: pieColors,
            borderColor: "#fff",
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: "right",
                labels: {
                    boxWidth: 20,
                    padding: 15,
                    font: {
                        size: 14
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || "";
                        const value = context.raw;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});