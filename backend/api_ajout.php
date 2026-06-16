<?php
// 1. Configurer les entêtes pour autoriser les requêtes CORS (indispensable en mode SPA)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/**
 * 2. ATTENTION : Votre fichier 'pret.php' contient un print_r($resultat) à la fin.
 * Cet affichage pollue la réponse JSON attendue par Vue.js.
 * On utilise ob_start() pour capturer et effacer cet affichage indésirable sans modifier pret.php.
 */
ob_start();
require_once 'pret.php';
ob_end_clean(); 

// 3. Déterminer l'action demandée (ex: api.php?action=ajouter)
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Récupérer le corps JSON de la requête envoyée par Vue
$json_input = file_get_contents("php://input");
$donnees = json_decode($json_input, true);

// Réponse par défaut
$reponse = [
    "succes" => false,
    "message" => "Action non reconnue ou méthode non autorisée."
];

switch ($action) {
    // ---------------------------------------------------------
    // MENU 1 : AJOUT
    // ---------------------------------------------------------
    case 'ajouter':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $donnees) {
            try {
                // Appel de la fonction insertPret de pret.php
                $id_insere = insertPret(
                    $pdo,
                    (string)$donnees['numero_compte'],
                    (string)$donnees['nom_client'],
                    (string)$donnees['nom_banque'],
                    (float)$donnees['montant'],
                    (string)$donnees['date_pret'],
                    (float)$donnees['taux_de_pret']
                );

                if ($id_insere > 0) {
                    $reponse['succes'] = true;
                    $reponse['message'] = "Insertion réussie"; // Libellé exigé par le Sujet 30
                } else {
                    $reponse['message'] = "Insertion échouée";
                }
            } catch (Exception $e) {
                $reponse['message'] = "Insertion échouée : " . $e->getMessage();
            }
        }
        break;

    // ---------------------------------------------------------
    // MENU 2 : LISTAGE (avec calcul du montant à payer)
    // ---------------------------------------------------------
    case 'lister':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $select = $pdo->query("SELECT * FROM pret_bancaire");
            $liste = $select->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcul demandé : Montant à payer = Montant * (1 + Taux)
            foreach ($liste as &$pret) {
                $pret['montant'] = (float)$pret['montant'];
                $pret['taux_de_pret'] = (float)$pret['taux_de_pret'];
                $pret['montant_a_payer'] = round($pret['montant'] * (1 + $pret['taux_de_pret']), 2);
            }
            
            echo json_encode($liste); // Renvoie directement le tableau d'objets pour Vue
            exit();
        }
        break;

    // ---------------------------------------------------------
    // MENU 2 : MODIFICATION
    // ---------------------------------------------------------
    case 'modifier':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($donnees['id'])) {
            $id = (int)$donnees['id'];
            unset($donnees['id']);
            
            try {
                updatePret($pdo, $id, $donnees);
                $reponse['succes'] = true;
                $reponse['message'] = "Modification réussie";
            } catch (Exception $e) {
                $reponse['message'] = "Modification échouée : " . $e->getMessage();
            }
        }
        break;

    // ---------------------------------------------------------
    // MENU 2 : SUPPRESSION
    // ---------------------------------------------------------
    case 'supprimer':
        $id = isset($donnees['id']) ? (int)$donnees['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : null);
        if ($id) {
            try {
                $lignes_supprimees = deletePret($pdo, $id);
                if ($lignes_supprimees > 0) {
                    $reponse['succes'] = true;
                    $reponse['message'] = "Suppression réussie";
                } else {
                    $reponse['message'] = "Suppression échouée";
                }
            } catch (Exception $e) {
                $reponse['message'] = "Suppression échouée : " . $e->getMessage();
            }
        }
        break;

    // ---------------------------------------------------------
    // MENU 3 : BILAN (Total, Minimal, Maximal)
    // ---------------------------------------------------------
    case 'bilan':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $select = $pdo->query("SELECT montant, taux_de_pret FROM pret_bancaire");
            $prets = $select->fetchAll(PDO::FETCH_ASSOC);
            
            $totaux_a_payer = [];
            foreach ($prets as $p) {
                $totaux_a_payer[] = (float)$p['montant'] * (1 + (float)$p['taux_de_pret']);
            }
            
            if (count($totaux_a_payer) > 0) {
                $reponse['succes'] = true;
                $reponse['bilan'] = [
                    "total" => round(array_sum($totaux_a_payer), 2),
                    "minimal" => round(min($totaux_a_payer), 2),
                    "maximal" => round(max($totaux_a_payer), 2)
                ];
                unset($reponse['message']);
            } else {
                $reponse['bilan'] = ["total" => 0, "minimal" => 0, "maximal" => 0];
            }
        }
        break;
}

// 4. Renvoyer la réponse formatée en JSON
echo json_encode($reponse);
?>