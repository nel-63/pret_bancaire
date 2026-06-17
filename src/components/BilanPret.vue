<template>
  <div class="bilan-container">
    <h2>Bilan Statistique des Prêts</h2>

    <div class="stats-grid">
      <div class="stat-card total">
        <h3>Total à Payer</h3>
        <p>{{ bilan.total }} €</p>
      </div>
      <div class="stat-card minimal">
        <h3>Montant Minimal</h3>
        <p>{{ bilan.minimal }} €</p>
      </div>
      <div class="stat-card maximal">
        <h3>Montant Maximal</h3>
        <p>{{ bilan.maximal }} €</p>
      </div>
    </div>

    <div class="chart-container">
      <canvas ref="chartCanvas"></canvas>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Chart from 'chart.js/auto'

// Variables réactives
const bilan = ref({ total: 0, minimal: 0, maximal: 0 })
const chartCanvas = ref(null)
let myChart = null // Variable pour stocker l'instance du graphique

// Récupération des données depuis l'API Bilan
const chargerBilan = async () => {
  try {
    const response = await fetch('http://localhost/pret_bancaire/backend/api_bilan.php')
    const data = await response.json()

    if (data.status === 'success') {
      bilan.value = data.bilan
      genererGraphique(data.graphique.labels, data.graphique.data)
    }
  } catch (error) {
    console.error('Erreur lors du chargement du bilan :', error)
  }
}

// Fonction pour dessiner l'histogramme avec Chart.js
const genererGraphique = (labels, dataPoints) => {
  // Détruire l'ancien graphique s'il existe pour éviter les superpositions
  if (myChart) {
    myChart.destroy()
  }

  // Création du nouveau graphique
  myChart = new Chart(chartCanvas.value, {
    type: 'bar', // ASTUCE : Changez 'bar' par 'pie' si vous préférez un camembert !
    data: {
      labels: labels, // Nom des clients
      datasets: [{
        label: 'Montant à payer (€)',
        data: dataPoints, // Montants correspondants
        backgroundColor: '#3498db',
        borderColor: '#2980b9',
        borderWidth: 1,
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        title: {
          display: true,
          text: 'Répartition des montants à payer par client',
          font: { size: 16 }
        }
      }
    }
  })
}

// Lancement au démarrage de l'onglet
onMounted(() => {
  chargerBilan()
})
</script>

<style scoped>
.bilan-container {
  background: white;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

h2 {
  color: #2c3e50;
  margin-bottom: 25px;
  border-bottom: 2px solid #ecf0f1;
  padding-bottom: 10px;
}

/* Grille pour les 3 cartes de statistiques */
.stats-grid {
  display: flex;
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  flex: 1;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
  color: white;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.stat-card h3 {
  margin: 0 0 10px 0;
  font-size: 1.1rem;
  opacity: 0.9;
}

.stat-card p {
  margin: 0;
  font-size: 1.8rem;
  font-weight: bold;
}

/* Couleurs spécifiques pour chaque carte */
.total { background: linear-gradient(135deg, #34495e, #2c3e50); }
.minimal { background: linear-gradient(135deg, #2ecc71, #27ae60); }
.maximal { background: linear-gradient(135deg, #e74c3c, #c0392b); }

/* Taille de la zone de dessin du graphique */
.chart-container {
  position: relative;
  height: 400px;
  width: 100%;
  margin-top: 20px;
}
</style>