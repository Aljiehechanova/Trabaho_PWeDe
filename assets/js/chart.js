document.addEventListener("DOMContentLoaded", () => {
    fetch("../charts/chart-data.php")
      .then((res) => res.json())
      .then((data) => {
        if (data.error) {
          console.error(data.error);
          return;
        }
  
        document.getElementById("totalJobs").textContent = data.totalJobs;
        document.getElementById("mostCommonDisability").textContent = data.mostCommon;
  
        const pieCtx = document.getElementById("disabilityPie").getContext("2d");
        const labels = Object.keys(data.disabilityCounts);
        const values = Object.values(data.disabilityCounts);
  
        new Chart(pieCtx, {
          type: "pie",
          data: {
            labels: labels,
            datasets: [{
              data: values,
              backgroundColor: [
                "#4B61D1", "#F07C46", "#9D4EDD",
                "#00B8A9", "#F76C6C", "#6C5B7B"
              ],
              borderColor: "#fff",
              borderWidth: 2
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { position: "bottom" },
              tooltip: {
                callbacks: {
                  label: function (context) {
                    const value = context.raw;
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percent = ((value / total) * 100).toFixed(1);
                    return `${context.label}: ${value} (${percent}%)`;
                  }
                }
              }
            }
          }
        });
  
        const barCtx = document.getElementById("disabilityBar").getContext("2d");
  
        new Chart(barCtx, {
          type: "bar",
          data: {
            labels: labels,
            datasets: [{
              label: "Jobs",
              data: values,
              backgroundColor: "#36A2EB"
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
      })
      .catch((err) => {
        console.error("Error fetching chart data:", err);
      });
  });
  