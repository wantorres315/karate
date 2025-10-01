  window.addEventListener('DOMContentLoaded', async () => {
    try {
      //const res = await fetch('/dashboard/chart-data');
      
      //const data = await res.json();
        const data = [];
      new Chart(document.getElementById('pesoPorPaciente'), {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: 'Peso total (kg)',
            data: data.pesoTotal,
            borderColor: 'rgb(244 63 94)',
            backgroundColor: 'rgba(244, 63, 94, 0.3)',
            fill: true,
            tension: 0.3,
            pointRadius: 3,
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: { beginAtZero: false }
          }
        }
      });

    } catch (error) {
      console.error('Erro ao carregar dados dos gráficos:', error);
    }

    
    
  });
