<template>
  <div class="liste-pret-container">
    <h2>Liste & Mise à jour des Prêts</h2>

    <table class="pret-table">
      <thead>
        <tr>
          <th>Nom Client</th>
          <th>Nom Banque</th>
          <th>Montant du prêt</th>
          <th>Date Prêt</th>
          <th>Montant à Payer</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="pret in prets" :key="pret.id">
          <td>{{ pret.nom_client }}</td>
          <td>{{ pret.nom_banque }}</td>
          <td>{{ pret.montant }} €</td>
          <td>{{ pret.date_pret }}</td>
          <td class="montant-payer">
            {{ (pret.montant * (1 + parseFloat(pret.taux_de_pret))).toFixed(2) }} €
          </td>
          <td>
            <button @click="selectionnerPourModifier(pret)" class="btn-modifier">Modifier</button>
            <button @click="supprimerPret(pret.id)" class="btn-supprimer">Supprimer</button>
          </td>
        </tr>
        <tr v-if="prets.length === 0">
          <td colspan="6" style="text-align: center; color: #7f8c8d;">Aucun prêt enregistré dans la base de données.</td>
        </tr>
      </tbody>
    </table>

    <div v-if="message" :class="['message-box', messageType]">
      {{ message }}
    </div>

    <div v-if="pretEnCours" class="edit-form-card">
      <h3>Modifier le prêt de {{ pretEnCours.nom_client }}</h3>
      <form @submit.prevent="validerModification">
        <div class="form-group">
          <label>N° Compte :</label>
          <input type="text" v-model="pretEnCours.numero_compte" required />
        </div>
        <div class="form-group">
          <label>Nom Client :</label>
          <input type="text" v-model="pretEnCours.nom_client" required />
        </div>
        <div class="form-group">
          <label>Nom Banque :</label>
          <input type="text" v-model="pretEnCours.nom_banque" required />
        </div>
        <div class="form-group">
          <label>Montant :</label>
          <input type="number" step="0.01" v-model.number="pretEnCours.montant" required />
        </div>
        <div class="form-group">
          <label>Date Prêt :</label>
          <input type="date" v-model="pretEnCours.date_pret" required />
        </div>
        <div class="form-group">
          <label>Taux de prêt (ex: 0.05 pour 5%) :</label>
          <input type="number" step="0.001" v-model.number="pretEnCours.taux_de_pret" required />
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-save">Enregistrer les modifications</button>
          <button type="button" @click="pretEnCours = null" class="btn-cancel">Annuler</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const prets = ref([])
const message = ref('')
const messageType = ref('') // 'success' ou 'error'
const pretEnCours = ref(null)

// 1. Charger les données depuis le Backend PHP
const chargerPrets = async () => {
  try {
    const response = await fetch('http://localhost/pret_bancaire/backend/api_liste.php?action=liste')
    const data = await response.json()
    prets.value = data
  } catch (err) {
    gestionMessage('Erreur lors de la récupération des données', 'error')
  }
}

// 2. Sélectionner un prêt pour le cloner dans le formulaire de modification
const selectionnerPourModifier = (pret) => {
  pretEnCours.value = { ...pret } // Clone de l'objet pour ne pas modifier directement la ligne du tableau
}

// 3. Soumettre la modification à l'API
const validerModification = async () => {
  try {
    const response = await fetch('http://localhost/pret_bancaire/backend/api_liste.php?action=modifier', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(pretEnCours.value)
    })
    const res = await response.json()

    if (res.status === 'success') {
      gestionMessage('Modification réussie', 'success')
      pretEnCours.value = null // Fermer le formulaire
      chargerPrets() // Rafraîchir la liste
    } else {
      gestionMessage('Modification échouée', 'error')
    }
  } catch (err) {
    gestionMessage('Modification échouée', 'error')
  }
}

// 4. Supprimer un prêt via son ID
const supprimerPret = async (id) => {
  if (!confirm('Voulez-vous vraiment supprimer ce prêt bancaire ?')) return

  try {
    const response = await fetch('http://localhost/pret_bancaire/backend/api_liste.php?action=supprimer', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id })
    })
    const res = await response.json()

    if (res.status === 'success') {
      gestionMessage('Suppression réussie', 'success')
      chargerPrets() // Rafraîchir la liste
      if (pretEnCours.value && pretEnCours.value.id === id) {
        pretEnCours.value = null // Fermer le formulaire si on supprime l'élément en cours d'édition
      }
    } else {
      gestionMessage('Suppression échouée', 'error')
    }
  } catch (err) {
    gestionMessage('Suppression échouée', 'error')
  }
}

// Gestion dynamique de l'affichage temporaire du message en bas
const gestionMessage = (text, type) => {
  message.value = text
  messageType.value = type
  setTimeout(() => {
    message.value = ''
  }, 4000) // Le message disparaît après 4 secondes
}

// Lancement automatique au chargement de la page
onMounted(() => {
  chargerPrets()
})
</script>

<style scoped>
.liste-pret-container {
  background: white;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

h2 {
  color: #2c3e50;
  margin-bottom: 20px;
  border-bottom: 2px solid #ecf0f1;
  padding-bottom: 10px;
}

.pret-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

.pret-table th, .pret-table td {
  padding: 12px 15px;
  border: 1px solid #e2e8f0;
  text-align: left;
}

.pret-table th {
  background-color: #34495e;
  color: white;
}

.pret-table tr:nth-child(even) {
  background-color: #f8fafc;
}

.montant-payer {
  font-weight: bold;
  color: #27ae60;
}

.btn-modifier {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
  margin-right: 8px;
}

.btn-supprimer {
  background-color: #e74c3c;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
}

.btn-modifier:hover { background-color: #2980b9; }
.btn-supprimer:hover { background-color: #c0392b; }

/* Styles requis pour les messages en bas du tableau */
.message-box {
  margin-top: 20px;
  padding: 12px;
  border-radius: 4px;
  text-align: center;
  font-weight: bold;
}
.message-box.success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}
.message-box.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

/* Style de la zone d'édition */
.edit-form-card {
  margin-top: 30px;
  padding: 20px;
  border: 1px solid #cbd5e1;
  background-color: #f8fafc;
  border-radius: 6px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
}

.form-group input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

.form-actions {
  margin-top: 20px;
}

.btn-save {
  background-color: #2ecc71;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 4px;
  cursor: pointer;
  margin-right: 10px;
}

.btn-cancel {
  background-color: #95a5a6;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 4px;
  cursor: pointer;
}
</style>