<?php

$caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%';

$motDePasse = '';

for ($i = 0; $i < 12; $i++) {
    $motDePasse .= $caracteres[random_int(0, strlen($caracteres) - 1)];
}

echo "Mot de passe généré : " . htmlspecialchars($motDePasse);