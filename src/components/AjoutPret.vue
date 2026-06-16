<template>
  <div class="ajout-container">
    <h2>Ajouter un nouveau prêt bancaire</h2>
    
    <form @submit.prevent="soumettreFormulaire" class="formulaire-pret">
      
      <div class="form-group">
        <label for="numero_compte">N° de compte :</label>
        <input type="text" id="numero_compte" v-model="formulaire.numero_compte" required />
      </div>

      <div class="form-group">
        <label for="nom_client">Nom du client :</label>
        <input type="text" id="nom_client" v-model="formulaire.nom_client" required />
      </div>

      <div class="form-group">
        <label for="nom_banque">Nom de la banque :</label>
        <input type="text" id="nom_banque" v-model="formulaire.nom_banque" required />
      </div>

      <div class="form-group">
        <label for="montant">Montant :</label>
        <input type="number" id="montant" step="0.01" v-model="formulaire.montant" required />
      </div>

      <div class="form-group">
        <label for="date_pret">Date du prêt :</label>
        <input type="date" id="date_pret" v-model="formulaire.date_pret" required />
      </div>

      <div class="form-group">
        <label for="taux_de_pret">Taux de prêt (ex: 0.05 pour 5%) :</label>
        <input type="number" id="taux_de_pret" step="0.001" v-model="formulaire.taux_de_pret" required />
      </div>

      <button type="submit" class="btn-submit" :disabled="enAttente">
        {{ enAttente ? 'Ajout en cours...' : 'Ajouter le prêt' }}
      </button>

    </form>

    <div v-if="messageServeur" :class="['message-retour', estSucces ? 'succes' : 'echec']">
      {{ messageServeur }}
    </div>

  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';

// 1. Initialisation de l'état réactif du formulaire
const formulaire = reactive({
  numero_compte: '',
  nom_client: '',
  nom_banque: '',
  montant: null,
  date_pret: '',
  taux_de_pret: null
});

// 2. Gestion de l'état de l'interface et des messages
const messageServeur = ref('');
const estSucces = ref(false);
const enAttente = ref(false);

// 3. Fonction d'envoi des données
const soumettreFormulaire = async () => {
  enAttente.value = true;
  messageServeur.value = ''; // Réinitialiser le message précédent

  try {
    // Utilisation de l'API Fetch pour envoyer les données au backend PHP
    // L'URL devra correspondre à un contrôleur PHP capable de traiter la requête (ex: api_ajout.php)
    const reponse = await fetch('http://localhost/pret_bancaire/backend/api_ajout.php?action=ajouter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(formulaire)
    });

    const resultat = await reponse.json();

    // Vérification de la réponse du serveur
    if (reponse.ok && resultat.succes) {
      // Le sujet exige que le serveur retourne un message spécifique
      messageServeur.value = resultat.message || "Insertion réussie";
      estSucces.value = true;
      
      // Vider le formulaire après un succès
      Object.assign(formulaire, {
        numero_compte: '',
        nom_client: '',
        nom_banque: '',
        montant: null,
        date_pret: '',
        taux_de_pret: null
      });
    } else {
      messageServeur.value = resultat.message || "Insertion échouée";
      estSucces.value = false;
    }
  } catch (erreur) {
    messageServeur.value = "Insertion échouée : Impossible de contacter le serveur.";
    estSucces.value = false;
  } finally {
    enAttente.value = false;
  }
};
</script>

<style scoped>
.ajout-container {
  max-width: 500px;
  margin: 0 auto;
  padding: 20px;
  background: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-group {
  margin-bottom: 15px;
  display: flex;
  flex-direction: column;
}

label {
  margin-bottom: 5px;
  font-weight: bold;
}

input {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.btn-submit {
  width: 100%;
  padding: 10px;
  background-color: #af604c;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  margin-top: 10px;
}

.btn-submit:disabled {
  background-color: #9e9e9e;
  cursor: not-allowed;
}

.message-retour {
  margin-top: 20px;
  padding: 15px;
  border-radius: 4px;
  text-align: center;
  font-weight: bold;
}

.succes {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.echec {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
</style>