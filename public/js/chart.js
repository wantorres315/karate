  window.addEventListener('DOMContentLoaded', async () => {
    try {
      const res = await fetch('/dashboard/chart-data');
      const data = await res.json();

      const labels = data.labels;
      // Gráfico de calorias comparativas
      new Chart(document.getElementById('graficoCaloriasComparativo'), {
        type: 'line',
        data: {
          labels,
          datasets: [
            {
              label: 'Calorias Consumidas (kcal)',
              data: data.caloriasConsumidas,
              borderColor: 'rgb(99, 102, 241)',
              backgroundColor: 'rgba(99, 102, 241, 0.2)',
              fill: false,
              tension: 0.3,
              pointRadius: 3,
            },
            {
              label: 'Calorias Disponiveis (kcal)',
              data: data.caloriasDisponiveis,
              borderColor: 'rgb(16, 185, 129)',
              backgroundColor: 'rgba(16, 185, 129, 0.2)',
              fill: false,
              tension: 0.3,
              pointRadius: 3,
            }
          ]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
              labels: { color: '#000' }
            },
            title: {
              display: true,
              text: 'Comparativo: Calorias Consumidas vs. Disponíveis',
              color: '#000'
            }
          },
          scales: {
            y: { beginAtZero: true, ticks: { color: '#000' } },
            x: { ticks: { color: '#000' } }
          }
        }
      });

      // Gráfico de peso total
      new Chart(document.getElementById('pesoTotalChart'), {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: 'Peso total (kg)',
            data: data.pesoTotal,
            borderColor: 'blue',
            backgroundColor: 'orange',
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

      // Gráfico pizza: distribuição por refeição
      new Chart(document.getElementById('caloriasQueimadasChart'), {
        type: 'pie',
        data: {
          labels: Object.keys(data.distribuicaoRefeicao),
          datasets: [{
            label: 'Distribuição de Calorias',
            data: Object.values(data.distribuicaoRefeicao),
            backgroundColor: [
              'rgb(99, 102, 241)',
              'rgb(16, 185, 129)',
              'rgb(244, 63, 94)',
              'rgb(251, 191, 36)'
            ],
            hoverOffset: 10
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
              labels: { color: '#000' }
            },
            title: {
              display: true,
              text: 'Calorias por Refeição',
              color: '#000'
            }
          }
        }
      });

      
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
