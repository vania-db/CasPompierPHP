<?php
require_once "connexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Méthode non autorisée.");
}

$id = trim($_POST["id"] ?? "");

if ($id === "" || strlen($id) > 20) {
    http_response_code(400);
    exit("Identifiant invalide.");
}

$stmt = $pdo->prepare(
    "SELECT imageTypeEngin
     FROM TypeEngin
     WHERE idTypeEngin = :id"
);
$stmt->execute([":id" => $id]);
$engin = $stmt->fetch();

if (!$engin) {
    http_response_code(404);
    exit("Type d'engin introuvable.");
}

try {
    $stmt = $pdo->prepare(
        "DELETE FROM TypeEngin WHERE idTypeEngin = :id"
    );
    $stmt->execute([":id" => $id]);

    $image = $engin["imageTypeEngin"];

    if (
        $image &&
        str_starts_with($image, "images/") &&
        is_file(__DIR__ . "/" . $image)
    ) {
        unlink(__DIR__ . "/" . $image);
    }

    header("Location: typeEngin.php");
    exit;

} catch (PDOException $e) {
    http_response_code(409);
    echo "Impossible de supprimer ce type d'engin. "
       . "Il est peut-être utilisé par d'autres données.";
}