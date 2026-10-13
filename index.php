<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des pompiers</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            🚒 CasPompier
        </a>
    </div>
</nav>

<main class="container py-5">
    <h1 class="fw-bold">Gestion des pompiers</h1>
    <p class="text-secondary mb-4">
        Tableau de bord de gestion de la caserne.
    </p>

    <div class="row g-4">

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h4">👨‍🚒 Pompiers</h2>
                    <p>Ajouter et gérer les pompiers.</p>
                    <a href="ajoutPompier.php" class="btn btn-danger">
                        Gérer les pompiers
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h4">📅 Planning</h2>
                    <p>Consulter et gérer le planning.</p>
                    <a href="#" class="btn btn-danger">Planning</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h4">🏢 Casernes</h2>
                    <p>Consulter les casernes.</p>
                    <a href="#" class="btn btn-danger">Casernes</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h4">📊 Statistiques</h2>
                    <p>Visualiser les données de la caserne.</p>
                    <a href="#" class="btn btn-danger">Statistiques</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h4">🔎 Recherche</h2>
                    <p>Rechercher des informations.</p>
                    <a href="#" class="btn btn-danger">Rechercher</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h4">🚒 Types d'engins</h2>
                    <p>Ajouter, modifier et supprimer des types d'engins.</p>
                    <a href="typeEngin.php" class="btn btn-danger">
                        Gérer les types d'engins
                    </a>
                </div>
            </div>
        </div>

    </div>
</main>

<footer class="bg-dark text-white text-center py-3">
    &copy; <?= date("Y") ?> CasPompier
</footer>

</body>
</html>