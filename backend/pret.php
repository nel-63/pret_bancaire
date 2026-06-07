<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=pret_db', 'root', '');
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$colone = "*";
$table = "pret_bancaire";

$select = $pdo->query("SELECT $colone FROM $table");
$resultat = $select->fetchAll(PDO::FETCH_ASSOC);


//ajout
function insertPret(
    PDO    $pdo,
    string $numero_compte,
    string $nom_client,
    string $nom_banque,
    float  $montant,
    string $date_pret, // format 'YYYY-MM-DD'
    float  $taux_de_pret
): int {
    $stmt = $pdo->prepare("
        INSERT INTO Pret_bancaire 
            (numero_compte, nom_client, nom_banque, montant, date_pret, taux_de_pret)
        VALUES 
            (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$numero_compte, $nom_client, $nom_banque, $montant, $date_pret, $taux_de_pret]);
    return (int) $pdo->lastInsertId();
}


//suppression
function deletePret(PDO $pdo, int $id): int {
    $stmt = $pdo->prepare("DELETE FROM Pret_bancaire WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->rowCount();
}


//mise à jour
function updatePret(PDO $pdo, int $id, array $champs): int {
    $champsAutorise = ['numero_compte', 'nom_client', 'nom_banque', 'montant', 'date_pret', 'taux_de_pret'];
    $sets   = [];
    $valeurs = [];
 
    foreach ($champs as $champ => $valeur) {
        if (in_array($champ, $champsAutorise)) {
            $sets[]   = "$champ = ?";
            $valeurs[] = $valeur;
        }
    }
 
    if (empty($sets)) return 0;
 
    $valeurs[] = $id;
    $sql = "UPDATE Pret_bancaire SET " . implode(", ", $sets) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($valeurs);
    return $stmt->rowCount();
}

//insertPret($pdo, '333', 'barbare', 'Bank of zazio ', 99999.00, '2000-01-01', 0.06);
//deletePret($pdo, 3);
echo "<pre>";
print_r($resultat);
echo "</pre>";


?>
