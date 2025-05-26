document.addEventListener("DOMContentLoaded", function () {
  fetch('../charts/chart-data.php')
    .then(response => response.json())
    .then(data => {
      if (!data || data.error) {
        console.error("Error loading chart data:", data?.error ?? "Unknown error");
        return;
      }

      // === For userD.php ===
      if (document.getElementById("mostCommonJob")) {
        document.getElementById("mostCommonJob").textContent = data.mostCommonJob ?? 'N/A';
      }
      if (document.getElementById("totalWorkshops")) {
        document.getElementById("totalWorkshops").textContent = data.totalWorkshops ?? '0';
      }
      if (document.getElementById("jobsAvailable")) {
        document.getElementById("jobsAvailable").textContent = data.pipeline?.available ?? '0';
      }
      if (document.getElementById("jobsCompleted")) {
        document.getElementById("jobsCompleted").textContent = data.pipeline?.completed ?? '0';
      }
      if (document.getElementById("jobsOffered")) {
        document.getElementById("jobsOffered").textContent = data.pipeline?.offered ?? '0';
      }

      // === Donut Chart for userD.php ===
      if (document.getElementById("workshopDonut")) {
        new Chart(document.getElementById("workshopDonut"), {
          type: 'doughnut',
          data: {
            labels: data.disabilityLabels,
            datasets: [{
              data: data.disabilityCounts,
              backgroundColor: ['#1abc9c', '#f39c12', '#3498db', '#9b59b6', '#e74c3c', '#2ecc71']
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { position: 'bottom' }
            }
          }
        });
      }

      // === Bar Chart for userD.php ===
      if (document.getElementById("workshopBar")) {
        new Chart(document.getElementById("workshopBar"), {
          type: 'bar',
          data: {
            labels: data.monthlyLabels,
            datasets: [{
              label: 'Workshop Activity',
              data: data.monthlyCounts,
              backgroundColor: 'rgba(52, 152, 219, 0.6)',
              borderColor: '#3498db',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true,
                title: { display: true, text: 'Entries' }
              },
              x: {
                title: { display: true, text: 'Month' }
              }
            }
          }
        });
      }

      // === For clientD.php ===
      if (document.getElementById("mostCommonDisability")) {
        document.getElementById("mostCommonDisability").textContent = data.most_common_disability ?? 'N/A';
      }
      if (document.getElementById("totalApplicantsCount")) {
        document.getElementById("totalApplicantsCount").textContent = data.total_applicants ?? '0';
      }

      // === Pie Chart for clientD.php ===
      if (document.getElementById("applicantPieChart")) {
        new Chart(document.getElementById("applicantPieChart"), {
          type: "pie",
          data: {
            labels: data.disabilityLabels,
            datasets: [{
              label: "Applicants",
              data: data.disabilityCounts,
              backgroundColor: ["#007bff", "#28a745", "#ffc107", "#dc3545", "#6f42c1"]
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { position: 'bottom' }
            }
          }
        });
      }

      // === Bar Chart for clientD.php ===
      if (document.getElementById("applicantBarChart")) {
        new Chart(document.getElementById("applicantBarChart"), {
          type: "bar",
          data: {
            labels: data.volunteerLabels,
            datasets: [{
              label: "Volunteers per Month",
              data: data.volunteerCounts,
              backgroundColor: "#28a745"
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true
              }
            },
            plugins: {
              legend: { display: false }
            }
          }
        });
      }      
    })
    .catch(err => {
      console.error('Fetch error:', err);
    });
});
