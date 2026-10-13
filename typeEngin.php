<?php
require_once "connexion.php";

$sql = "SELECT idTypeEngin, LblTypeEngin, imageTypeEngin
        FROM TypeEngin
        ORDER BY LblTypeEngin ASC";

$stmt = $pdo->query($sql);
$typesEngins = $stmt->fetchAll();

function e($valeur) {
    return htmlspecialchars((string)$valeur, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Types d'engins</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🚒 CasPompier</a>
        <a href="index.php" class="btn btn-outline-light">Accueil</a>
    </div>
</nav>

<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1>Les véhicules de pompier | Types d'engins</h1>
            <p class="text-secondary mb-0">Consulter et gérer les types d'engins.</p>
                        <p class="text-secondary mb-0">Voici la liste des typers de véhicules que l'on peut trouver dans une caserrne de pompiers.</p>

        </div>
        <a href="ajouterTypeEngin.php" class="btn btn-danger">
            + Ajouter un type
        </a>
    </div>

    <?php if (count($typesEngins) === 0): ?>
        <div class="alert alert-info">Aucun type d'engin enregistré.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($typesEngins as $engin): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">

                        <?php if (!empty($engin['imageTypeEngin'])): ?>
                            <img
                                src="<?= e($engin['imageTypeEngin']) ?>"
                                class="card-img-top"
                                alt="<?= e($engin['LblTypeEngin']) ?>"
                                style="height:220px; object-fit:contain;"
                            >
                        <?php else: ?>
                            <div class="bg-secondary-subtle d-flex align-items-center justify-content-center"
                                 style="height:220px;">
                                <span class="fs-1">🚒</span>
                            </div>
                        <?php endif; ?>

                        <div class="card-body">
                            <h2 class="h5 card-title">
                                <?= e($engin['LblTypeEngin']) ?>
                            </h2>
                            <p class="text-secondary">
                                Identifiant : <?= e($engin['idTypeEngin']) ?>
                            </p>

                            <div class="d-flex gap-2">
                                <a
                                    class="btn btn-warning"
                                    href="modifierTypeEngin.php?id=<?= rawurlencode($engin['idTypeEngin']) ?>"
                                >
                                    Modifier
                                </a>

                                <form action="supprimerTypeEngin.php" method="post"
                                      onsubmit="return confirm('Confirmer la suppression de ce type d’engin ?');">
                                    <input type="hidden" name="id"
                                           value="<?= e($engin['idTypeEngin']) ?>">
                                    <button type="submit" class="btn btn-danger">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <p class="text-secondary mt-4">
        Nombre de types : <?= count($typesEngins) ?>
    </p>
</main>
</body>
</html>