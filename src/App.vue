<template>
  <div id="app">
    <header class="app-header">
      <h1>Gestion des Prêts Bancaires</h1>
    </header>
    
    <main class="app-content">
      <AjoutPret/>
    </main>
  </div>
</template>

<script setup>
// Correction de la trajectoire : On pointe vers 'ajout.vue' tout en minuscules
import AjoutPret from './components/AjoutPret.vue'
</script>

<style>
body {
  margin: 0;
  font-family: Arial, sans-serif;
  background-color: #f0f2f5;
}

#app {
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: 100vh;
}

.app-header {
  background-color: #2c3e50;
  color: white;
  width: 100%;
  text-align: center;
  padding: 10px 0;
  margin-bottom: 30px;
}

.app-content {
  width: 100%;
  max-width: 800px;
  padding: 0 20px;
}
</style>