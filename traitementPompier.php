<?php

require_once "connexion.php";


/*
|--------------------------------------------------------------------------
| Seules les requêtes POST sont acceptées
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    die("Méthode non autorisée.");

}


/*
|--------------------------------------------------------------------------
| Récupération des données
|--------------------------------------------------------------------------
*/

$matricule = trim($_POST["matricule"] ?? "");

$nom = trim($_POST["nom"] ?? "");

$prenom = trim($_POST["prenom"] ?? "");

$dateNaissance =
    trim($_POST["dateNaissance"] ?? "");

$telephone =
    trim($_POST["telephone"] ?? "");

$sexe =
    trim($_POST["sexe"] ?? "");

$grade =
    $_POST["grade"] ?? "";

$caserne =
    $_POST["caserne"] ?? "";

$typePompier =
    trim($_POST["typePompier"] ?? "");


/*
|--------------------------------------------------------------------------
| Données spécifiques
|--------------------------------------------------------------------------
*/

$dateEmbauche =
    trim($_POST["dateEmbauche"] ?? "");

$indice =
    trim($_POST["indice"] ?? "");

$bip =
    $_POST["bip"] ?? "";

$employeur =
    $_POST["employeur"] ?? "";


/*
|--------------------------------------------------------------------------
| Tableau des erreurs
|--------------------------------------------------------------------------
*/

$erreurs = [];


/*
|--------------------------------------------------------------------------
| MATRICULE
|--------------------------------------------------------------------------
*/

if ($matricule === "") {

    $erreurs[] =
        "Le matricule est obligatoire.";

}

elseif (!ctype_digit($matricule)) {

    $erreurs[] =
        "Le matricule doit contenir uniquement des chiffres.";

}

elseif ((int)$matricule < 1) {

    $erreurs[] =
        "Le matricule doit être supérieur à zéro.";

}


/*
|--------------------------------------------------------------------------
| NOM
|--------------------------------------------------------------------------
*/

if ($nom === "") {

    $erreurs[] =
        "Le nom est obligatoire.";

}

elseif (
    !preg_match(
        "/^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{1,50}$/u",
        $nom
    )
) {

    $erreurs[] =
        "Le nom contient des caractères invalides.";

}


/*
|--------------------------------------------------------------------------
| PRENOM
|--------------------------------------------------------------------------
*/

if ($prenom === "") {

    $erreurs[] =
        "Le prénom est obligatoire.";

}

elseif (
    !preg_match(
        "/^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{1,50}$/u",
        $prenom
    )
) {

    $erreurs[] =
        "Le prénom contient des caractères invalides.";

}


/*
|--------------------------------------------------------------------------
| DATE DE NAISSANCE
|--------------------------------------------------------------------------
*/

if ($dateNaissance === "") {

    $erreurs[] =
        "La date de naissance est obligatoire.";

}

else {

    $date = DateTime::createFromFormat(
        "Y-m-d",
        $dateNaissance
    );

    $erreursDate =
        DateTime::getLastErrors();

    if (
        !$date
        ||
        (
            $erreursDate !== false
            &&
            (
                $erreursDate["warning_count"] > 0
                ||
                $erreursDate["error_count"] > 0
            )
        )
    ) {

        $erreurs[] =
            "La date de naissance est invalide.";

    }

    elseif (
        $date > new DateTime()
    ) {

        $erreurs[] =
            "La date de naissance ne peut pas être dans le futur.";

    }

}


/*
|--------------------------------------------------------------------------
| TELEPHONE
|--------------------------------------------------------------------------
*/

if ($telephone === "") {

    $erreurs[] =
        "Le téléphone est obligatoire.";

}

elseif (
    !preg_match(
        "/^[0-9+ ]{8,20}$/",
        $telephone
    )
) {

    $erreurs[] =
        "Le numéro de téléphone est invalide.";

}


/*
|--------------------------------------------------------------------------
| SEXE
|--------------------------------------------------------------------------
*/

$sexesAutorises = [
    "féminin",
    "masculin",
    "autre"
];


if (
    !in_array(
        $sexe,
        $sexesAutorises,
        true
    )
) {

    $erreurs[] =
        "Le sexe sélectionné est invalide.";

}


/*
|--------------------------------------------------------------------------
| TYPE POMPIER
|--------------------------------------------------------------------------
*/

$typesAutorises = [
    "Professionnel",
    "Volontaire"
];


if (
    !in_array(
        $typePompier,
        $typesAutorises,
        true
    )
) {

    $erreurs[] =
        "Le type de pompier sélectionné est invalide.";

}


/*
|--------------------------------------------------------------------------
| GRADE
|--------------------------------------------------------------------------
*/

if (
    $grade === ""
    ||
    filter_var(
        $grade,
        FILTER_VALIDATE_INT
    ) === false
) {

    $erreurs[] =
        "Le grade sélectionné est invalide.";

}


/*
|--------------------------------------------------------------------------
| CASERNE
|--------------------------------------------------------------------------
*/

if (
    $caserne === ""
    ||
    filter_var(
        $caserne,
        FILTER_VALIDATE_INT
    ) === false
) {

    $erreurs[] =
        "La caserne sélectionnée est invalide.";

}


/*
|--------------------------------------------------------------------------
| PROFESSIONNEL
|--------------------------------------------------------------------------
*/

if ($typePompier === "Professionnel") {


    if ($dateEmbauche === "") {

        $erreurs[] =
            "La date d'embauche est obligatoire.";

    }

    else {

        $date = DateTime::createFromFormat(
            "Y-m-d",
            $dateEmbauche
        );

        if (!$date) {

            $erreurs[] =
                "La date d'embauche est invalide.";

        }

        elseif (
            $date > new DateTime()
        ) {

            $erreurs[] =
                "La date d'embauche ne peut pas être dans le futur.";

        }

    }


    if (strlen($indice) > 30) {

        $erreurs[] =
            "L'indice ne peut pas dépasser 30 caractères.";

    }

}


/*
|--------------------------------------------------------------------------
| VOLONTAIRE
|--------------------------------------------------------------------------
*/

if ($typePompier === "Volontaire") {


    /*
    |---------------------------------------------
    | BIP
    |---------------------------------------------
    */

    if (
        $bip !== ""
        &&
        (
            !ctype_digit((string)$bip)
            ||
            (int)$bip < 0
        )
    ) {

        $erreurs[] =
            "Le BIP doit être un nombre positif.";

    }


    /*
    |---------------------------------------------
    | Employeur
    |---------------------------------------------
    */

    if (
        $employeur === ""
        ||
        filter_var(
            $employeur,
            FILTER_VALIDATE_INT
        ) === false
    ) {

        $erreurs[] =
            "L'employeur est obligatoire.";

    }

}


/*
|--------------------------------------------------------------------------
| Vérification du matricule en BDD
|--------------------------------------------------------------------------
*/

if (
    empty($erreurs)
    &&
    ctype_digit($matricule)
) {

    $sql = "
        SELECT Matricule
        FROM pompier
        WHERE Matricule = :matricule
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":matricule" => $matricule
    ]);


    if ($stmt->fetch()) {

        $erreurs[] =
            "Ce matricule existe déjà. Veuillez en saisir un autre.";

    }

}


/*
|--------------------------------------------------------------------------
| Vérification du Grade en BDD
|--------------------------------------------------------------------------
*/

if (empty($erreurs)) {

    $sql = "
        SELECT idGrade
        FROM grade
        WHERE idGrade = :grade
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":grade" => $grade
    ]);


    if (!$stmt->fetch()) {

        $erreurs[] =
            "Le grade sélectionné n'existe pas.";

    }

}


/*
|--------------------------------------------------------------------------
| Vérification de la Caserne en BDD
|--------------------------------------------------------------------------
*/

if (empty($erreurs)) {

    $sql = "
        SELECT idCaserne
        FROM caserne
        WHERE idCaserne = :caserne
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":caserne" => $caserne
    ]);


    if (!$stmt->fetch()) {

        $erreurs[] =
            "La caserne sélectionnée n'existe pas.";

    }

}


/*
|--------------------------------------------------------------------------
| Vérification Employeur
|--------------------------------------------------------------------------
*/

if (
    empty($erreurs)
    &&
    $typePompier === "Volontaire"
) {

    $sql = "
        SELECT idEmployeur
        FROM employeur
        WHERE idEmployeur = :employeur
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":employeur" => $employeur
    ]);


    if (!$stmt->fetch()) {

        $erreurs[] =
            "L'employeur sélectionné n'existe pas.";

    }

}


/*
|--------------------------------------------------------------------------
| Affichage des erreurs
|--------------------------------------------------------------------------
*/

if (!empty($erreurs)) {

    ?>

    <!DOCTYPE html>

    <html lang="fr">

    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >

        <title>Erreur</title>

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
        >

    </head>


    <body>

    <div class="container mt-5">

        <div class="alert alert-danger">

            <h1 class="h4">
                Le formulaire contient des erreurs
            </h1>


            <ul class="mb-3">

                <?php foreach ($erreurs as $erreur): ?>

                    <li>

                        <?= htmlspecialchars(
                            $erreur,
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>

                    </li>

                <?php endforeach; ?>

            </ul>


            <a
                href="ajoutPompier.php"
                class="btn btn-danger"
            >
                Retour au formulaire
            </a>

        </div>

    </div>

    </body>

    </html>

    <?php

    exit;

}


/*
|--------------------------------------------------------------------------
| TRANSACTION
|--------------------------------------------------------------------------
*/

try {

    /*
    |---------------------------------------------
    | BEGIN
    |---------------------------------------------
    */

    $pdo->beginTransaction();


    /*
    |---------------------------------------------
    | INSERT POMPIER
    |---------------------------------------------
    */

    $sql = "
        INSERT INTO pompier
        (
            Matricule,
            PrenomPompier,
            NomPompier,
            DateNaissPompier,
            TelPompier,
            SexePompier,
            idGrade
        )
        VALUES
        (
            :matricule,
            :prenom,
            :nom,
            :dateNaissance,
            :telephone,
            :sexe,
            :grade
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([

        ":matricule" =>
            (int)$matricule,

        ":prenom" =>
            $prenom,

        ":nom" =>
            $nom,

        ":dateNaissance" =>
            $dateNaissance,

        ":telephone" =>
            $telephone,

        ":sexe" =>
            $sexe,

        ":grade" =>
            (int)$grade

    ]);


    /*
    |---------------------------------------------
    | PROFESSIONNEL
    |---------------------------------------------
    */

    if ($typePompier === "Professionnel") {

        $sql = "
            INSERT INTO professionnel
            (
                Matricule,
                DateEmbauche,
                Indice
            )
            VALUES
            (
                :matricule,
                :dateEmbauche,
                :indice
            )
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([

            ":matricule" =>
                (int)$matricule,

            ":dateEmbauche" =>
                $dateEmbauche,

            ":indice" =>
                $indice !== ""
                    ? $indice
                    : null

        ]);

    }


    /*
    |---------------------------------------------
    | VOLONTAIRE
    |---------------------------------------------
    */

    elseif ($typePompier === "Volontaire") {

        $sql = "
            INSERT INTO volontaire
            (
                Matricule,
                BIP,
                idEmployeur
            )
            VALUES
            (
                :matricule,
                :bip,
                :employeur
            )
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([

            ":matricule" =>
                (int)$matricule,

            ":bip" =>
                $bip !== ""
                    ? (int)$bip
                    : null,

            ":employeur" =>
                (int)$employeur

        ]);

    }


    /*
    |---------------------------------------------
    | AFFECTATION
    |---------------------------------------------
    |
    | Date = date du jour
    |
    */

    $sql = "
        INSERT INTO affectation
        (
            Matricule,
            Date,
            idCaserne
        )
        VALUES
        (
            :matricule,
            :date,
            :caserne
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([

        ":matricule" =>
            (int)$matricule,

        ":date" =>
            date("Y-m-d"),

        ":caserne" =>
            (int)$caserne

    ]);


    /*
    |---------------------------------------------
    | TOUT EST BON
    |---------------------------------------------
    */

    $pdo->commit();


} catch (PDOException $e) {


    /*
    |---------------------------------------------
    | ANNULATION
    |---------------------------------------------
    */

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }


    /*
    |---------------------------------------------
    | Message utilisateur
    |---------------------------------------------
    */

    ?>

    <!DOCTYPE html>

    <html lang="fr">

    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >

        <title>Erreur</title>

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
        >

    </head>


    <body>

    <div class="container mt-5">

        <div class="alert alert-danger">

            <h1 class="h4">
                Une erreur est survenue
            </h1>

            <p>
                Le pompier n'a pas été ajouté.
            </p>

            <a
                href="ajoutPompier.php"
                class="btn btn-danger"
            >
                Retour au formulaire
            </a>

        </div>

    </div>

    </body>

    </html>

    <?php

    exit;

}


/*
|--------------------------------------------------------------------------
| SUCCÈS
|--------------------------------------------------------------------------
*/

?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pompier ajouté</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>


<body>

<div class="container mt-5">

    <div class="alert alert-success">

        <h1 class="h4">
            OK
        </h1>

        <p>
            Le pompier
            <strong>
                <?= htmlspecialchars(
                    $prenom . " " . $nom,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </strong>
            a été ajouté avec succès.
        </p>

        <p class="mb-3">

            Le pompier a été enregistré dans :

        </p>

        <ul>

            <li>Pompier</li>

            <?php if ($typePompier === "Professionnel"): ?>

                <li>Professionnel</li>

            <?php else: ?>

                <li>Volontaire</li>

            <?php endif; ?>

            <li>Affectation</li>

        </ul>


        <a
            href="ajoutPompier.php"
            class="btn btn-success"
        >
            Ajouter un autre pompier
        </a>

    </div>

</div>

</body>

</html>