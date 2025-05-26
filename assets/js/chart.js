document.addEventListener("DOMContentLoaded", function () {
  fetch('../charts/chart-data.php')
    .then(response => response.json())
    .then(data => {
      if (!data || data.error) {
        console.error("Error loading chart data:", data?.error ?? "Unknown error");
        return;
      }

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
      if (document.getElementById("mostCommonDisability")) {
        document.getElementById("mostCommonDisability").textContent =
          data.mostCommonDisability ?? 'N/A';
      }
      
      // Total Applicants
      if (document.getElementById("totalApplicantsCount")) {
        document.getElementById("totalApplicantsCount").textContent =
          data.totalApplicants ?? '0';
      }
      // Display Top Companies Hiring PWDs
      if (document.getElementById("companyList")) {
        const list = document.getElementById("companyList");
        list.innerHTML = "";

        if (data.topCompanyNames && data.topCompanyNames.length > 0) {
          data.topCompanyNames.forEach((name, index) => {
            const count = data.topCompanyCounts[index];
            const li = document.createElement("li");
            li.textContent = `${name} (${count} job${count > 1 ? 's' : ''})`;
            list.appendChild(li);
          });
        } else {
          list.innerHTML = "<li>No data available</li>";
        }
      }

      if (document.getElementById("applicantPieChart")) {
        const pieChartCanvas = document.getElementById("applicantPieChart");
      
        const applicantPieChart = new Chart(pieChartCanvas, {
          type: "pie",
          data: {
            labels: data.clientDisabilityLabels,
            datasets: [{
              data: data.clientDisabilityCounts,
              backgroundColor: [
                "#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF", "#FF9F40"
              ],
              hoverOffset: 10
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'bottom',
              },
              title: {
                display: true,
                text: 'Applicants by Disability Type'
              }
            },
            onClick: (e, elements) => {
              if (elements.length > 0) {
                const chart = elements[0];
                const index = chart.index;
                const selectedLabel = applicantPieChart.data.labels[index];
      
                fetch(`../charts/fetch-users-by-disability.php?disability=${encodeURIComponent(selectedLabel)}`)
                  .then(response => response.json())
                  .then(users => {
                    const userList = document.getElementById("userListContent");
                    userList.innerHTML = "";
      
                    if (users.length === 0 || users.error) {
                      userList.innerHTML = "<li class='text-danger'>No users found.</li>";
                      return;
                    }
      
                    users.forEach(user => {
                      const li = document.createElement("li");
                      li.textContent = user.fullname;
                      userList.appendChild(li);
                    });
                  })
                  .catch(error => {
                    console.error("Error fetching users:", error);
                    const userList = document.getElementById("userListContent");
                    userList.innerHTML = "<li class='text-danger'>Error loading data.</li>";
                  });
              }
            }
          }
        });
      }
      
      
      // Volunteer bar chart
      // Volunteer bar chart
if (document.getElementById("applicantBarChart")) {
  const barChartCanvas = document.getElementById("applicantBarChart");
  const volunteerBarChart = new Chart(barChartCanvas, {
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
      },
      onClick: (e, elements) => {
        if (elements.length > 0) {
          const index = elements[0].index;
          const selectedMonth = volunteerBarChart.data.labels[index];

          fetch(`../charts/fetch-volunteers-by-month.php?month=${encodeURIComponent(selectedMonth)}`)
            .then(response => response.json())
            .then(volunteers => {
              const list = document.getElementById("volunteerListContent");
              list.innerHTML = "";

              if (!volunteers || volunteers.length === 0 || volunteers.error) {
                list.innerHTML = "<li class='text-danger'>No volunteers found.</li>";
                return;
              }

              volunteers.forEach(volunteer => {
                const li = document.createElement("li");
                li.textContent = volunteer.fullname;
                list.appendChild(li);
              });
            })
            .catch(error => {
              console.error("Error fetching volunteers:", error);
              const list = document.getElementById("volunteerListContent");
              list.innerHTML = "<li class='text-danger'>Error loading data.</li>";
            });
        }
      }
    }
  });
}


      // Top Skills chart
      if (document.getElementById("skillsBarChart")) {
        new Chart(document.getElementById("skillsBarChart"), {
          type: "bar",
          data: {
            labels: data.topSkillLabels,
            datasets: [{
              label: "Top Required Skills",
              data: data.topSkillCounts,
              backgroundColor: "rgba(255, 159, 64, 0.7)",
              borderColor: "rgba(255, 159, 64, 1)",
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true,
                title: {
                  display: true,
                  text: 'Count'
                }
              },
              x: {
                title: {
                  display: true,
                  text: 'Skill'
                }
              }
            }
          }
        });
      }
    })
    .catch(err => {
      console.error('Fetch error:', err);
    });
});
