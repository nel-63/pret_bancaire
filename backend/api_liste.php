<?php
// Autoriser le serveur Vue (localhost:8080) à interagir avec cette API
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Gestion des requêtes de vérification Cross-Origin (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=pret_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Erreur de connexion : " . $e->getMessage()]);
    exit;
}

// Récupération de l'action demandée (?action=...)
$action = $_GET['action'] ?? '';

// --- ACTION 1 : LISTER LES PRÊTS ---
if ($action === 'liste') {
    try {
        $stmt = $pdo->query("SELECT * FROM pret_bancaire ORDER BY id DESC");
        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($resultat);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Erreur de lecture"]);
    }
    exit;
}

// --- ACTION 2 : MODIFIER UN PRÊT ---
if ($action === 'modifier') {
    // Récupérer les données JSON envoyées par Vue.js
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['id'])) {
        $id = (int)$data['id'];
        $champsAutorise = ['numero_compte', 'nom_client', 'nom_banque', 'montant', 'date_pret', 'taux_de_pret'];
        $sets = [];
        $valeurs = [];
        
        foreach ($data as $champ => $valeur) {
            if (in_array($champ, $champsAutorise)) {
                $sets[] = "$champ = ?";
                $valeurs[] = $valeur;
            }
        }
        
        if (!empty($sets)) {
            try {
                $valeurs[] = $id;
                $sql = "UPDATE pret_bancaire SET " . implode(", ", $sets) . " WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($valeurs);
                echo json_encode(["status" => "success", "message" => "Modification réussie"]);
            } catch (PDOException $e) {
                echo json_encode(["status" => "error", "message" => "Modification échouée"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Modification échouée : aucun champ valide"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Modification échouée : ID manquant"]);
    }
    exit;
}

// --- ACTION 3 : SUPPRIMER UN PRÊT ---
if ($action === 'supprimer') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;
    
    if ($id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM pret_bancaire WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(["status" => "success", "message" => "Suppression réussie"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Suppression échouée : prêt introuvable"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => "Suppression échouée"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Suppression échouée : ID manquant"]);
    }
    exit;
}

// Si aucune action ne correspond
echo json_encode(["status" => "error", "message" => "Action non reconnue"]);