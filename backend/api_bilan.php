<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

try {
    $pdo = new PDO('mysql:host=localhost;dbname=pret_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // On récupère tous les prêts
    $stmt = $pdo->query("SELECT nom_client, montant, taux_de_pret FROM pret_bancaire");
    $prets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = 0;
    $valeurs = [];
    $labels = [];
    $dataChart = [];

    foreach ($prets as $pret) {
        // Calcul du montant à payer : Montant * (1 + Taux)
        $a_payer = $pret['montant'] * (1 + $pret['taux_de_pret']);
        $valeurs[] = $a_payer;
        $total += $a_payer;

        // Préparation des données pour le graphique (Axe X = Client, Axe Y = Montant)
        $labels[] = $pret['nom_client'];
        $dataChart[] = round($a_payer, 2);
    }

    // Calcul du Min et Max
    $minimal = count($valeurs) > 0 ? min($valeurs) : 0;
    $maximal = count($valeurs) > 0 ? max($valeurs) : 0;

    echo json_encode([
        "status" => "success",
        "bilan" => [
            "total" => round($total, 2),
            "minimal" => round($minimal, 2),
            "maximal" => round($maximal, 2)
        ],
        "graphique" => [
            "labels" => $labels,
            "data" => $dataChart
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Erreur de connexion ou de lecture."]);
}
?>