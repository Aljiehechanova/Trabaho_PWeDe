document.addEventListener("DOMContentLoaded", function () {
  fetch('../api/chart-data.php')
    .then(res => res.json())
    .then(data => {
      if (!data || !data.labels || !data.counts) {
        console.error('Invalid data:', data);
        return;
      }

      // Update text stats
      document.getElementById('totalJobs').textContent = data.totalJobs ?? '0';
      document.getElementById('jobsAvailable').textContent = data.pipeline?.available ?? '0';
      document.getElementById('jobsCompleted').textContent = data.pipeline?.completed ?? '0';
      document.getElementById('jobsOffered').textContent = data.pipeline?.offered ?? '0';

      // Pie chart
      const ctxPie = document.getElementById('disabilityPie').getContext('2d');
      new Chart(ctxPie, {
        type: 'pie',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Disability Requirements',
            data: data.counts,
            backgroundColor: ['#007bff', '#ffc107', '#dc3545', '#28a745', '#6610f2'],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });

      // Bar chart
      const ctxBar = document.getElementById('disabilityBar').getContext('2d');
      new Chart(ctxBar, {
        type: 'bar',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Job Posts by Disability',
            data: data.counts,
            backgroundColor: 'rgba(0, 123, 255, 0.5)',
            borderColor: '#007bff',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: 'Job Count' }
            },
            x: {
              title: { display: true, text: 'Disability Type' }
            }
          }
        }
      });
    })
    .catch(err => {
      console.error('Fetch error:', err);
    });
});
