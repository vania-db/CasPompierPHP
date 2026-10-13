<?php

require_once "connexion.php";


header("Content-Type: application/json; charset=utf-8");


/*
|--------------------------------------------------------------------------
| Récupération du matricule
|--------------------------------------------------------------------------
*/

$matricule = trim($_GET['matricule'] ?? '');


/*
|--------------------------------------------------------------------------
| Vérification du format
|--------------------------------------------------------------------------
*/

if (
    $matricule === ''
    ||
    !ctype_digit($matricule)
) {

    echo json_encode([
        "existe" => false
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Recherche en BDD
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT Matricule
    FROM pompier
    WHERE Matricule = :matricule
";


$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":matricule" => $matricule
]);


/*
|--------------------------------------------------------------------------
| Résultat
|--------------------------------------------------------------------------
*/

$existe = $stmt->fetch() !== false;


echo json_encode([
    "existe" => $existe
]);