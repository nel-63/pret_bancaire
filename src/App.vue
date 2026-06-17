<template>
  <div id="app">
    <header class="app-header">
      <h1>Gestion des Prêts Bancaires</h1>
      
      <nav class="tab-navigation">
        <button 
          v-for="onglet in ongletsDisponibles" 
          :key="onglet.id"
          :class="{ active: ongletActif === onglet.id }" 
          @click="changerOnglet(onglet.id)"
        >
          {{ onglet.icone }} {{ onglet.titre }}
        </button>
      </nav>
    </header>
    
    <main class="app-content">
      <KeepAlive>
        <component :is="composantActif" />
      </KeepAlive>
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// 1. Importation des composants existants
import AjoutPret from './components/AjoutPret.vue'
import ListePret from './components/Liste-Pret.vue'
import BilanPret from './components/BilanPret.vue'

// --- ZONE DE FLEXIBILITÉ (CONFIGURATION) ---
// Pour ajouter un menu plus tard, il suffira d'importer son fichier et de l'ajouter dans ce tableau !
const ongletsDisponibles = [
  { id: 'ajout', titre: 'Nouveau Prêt', icone: '➕', composant: AjoutPret },
  { id: 'liste', titre: 'Liste & Mise à jour', icone: '📋', composant: ListePret },
  { id: 'bilan', titre: 'Bilan Statistique', icone: '📊', composant: BilanPret }
]

// L'onglet actif par défaut (prend le premier onglet du tableau)
const ongletActif = ref(ongletsDisponibles[0].id)

// Fonction pour changer d'onglet
const changerOnglet = (idOnglet) => {
  ongletActif.value = idOnglet
}

// 2. Calcul automatique du composant à afficher à l'écran
const composantActif = computed(() => {
  const ongletTrouve = ongletsDisponibles.find(o => o.id === ongletActif.value)
  return ongletTrouve ? ongletTrouve.composant : AjoutPret
})
</script>

<style>
/* --- Styles globaux du site --- */
body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #f1f5f9;
  color: #334155;
}

#app {
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: 100vh;
}

/* --- Design du Header --- */
.app-header {
  background-color: #1e293b;
  color: white;
  width: 100%;
  text-align: center;
  padding: 20px 0 0 0;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  margin-bottom: 30px;
}

.app-header h1 {
  margin: 0 0 20px 0;
  font-size: 1.8rem;
  letter-spacing: 0.5px;
}

/* --- Style de la Barre d'Onglets --- */
.tab-navigation {
  display: flex;
  justify-content: center;
  gap: 10px;
}

.tab-navigation button {
  background: none;
  border: none;
  color: #94a3b8;
  padding: 12px 24px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  border-bottom: 3px solid transparent;
}

.tab-navigation button:hover {
  color: #f8fafc;
}

/* Style de l'onglet actuellement sélectionné */
.tab-navigation button.active {
  color: #38bdf8;
  border-bottom: 3px solid #38bdf8;
  background-color: rgba(255, 255, 255, 0.05);
  border-radius: 4px 4px 0 0;
}

/* --- Zone de Contenu --- */
.app-content {
  width: 100%;
  max-width: 950px;
  padding: 0 20px 40px 20px;
  box-sizing: border-box;
}
</style>