<?php

require_once "connexion.php";

$login = "admin";

$motDePasse = "A7@kP2#xL9$m";

$hash = password_hash(
    $motDePasse,
    PASSWORD_DEFAULT
);

$sql = "INSERT INTO utilisateur (login, motDePasse)
        VALUES (:login, :motDePasse)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":login" => $login,
    ":motDePasse" => $hash
]);

echo "Utilisateur créé.";