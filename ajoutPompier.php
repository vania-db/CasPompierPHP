<?php

require_once "connexion.php";

/*
|--------------------------------------------------------------------------
| Récupération des grades
|--------------------------------------------------------------------------
*/

$sqlGrade = "
    SELECT idGrade, LblGrade
    FROM grade
    ORDER BY idGrade
";

$stmtGrade = $pdo->prepare($sqlGrade);
$stmtGrade->execute();

$grades = $stmtGrade->fetchAll();


/*
|--------------------------------------------------------------------------
| Récupération des casernes
|--------------------------------------------------------------------------
*/

$sqlCaserne = "
    SELECT idCaserne, NomCaserne
    FROM caserne
    ORDER BY NomCaserne
";

$stmtCaserne = $pdo->prepare($sqlCaserne);
$stmtCaserne->execute();

$casernes = $stmtCaserne->fetchAll();


/*
|--------------------------------------------------------------------------
| Récupération des employeurs
|--------------------------------------------------------------------------
*/

$sqlEmployeur = "
    SELECT
        idEmployeur,
        nomEmployeur,
        prenomEmployeur,
        Entreprise_Institution
    FROM employeur
    ORDER BY nomEmployeur, prenomEmployeur
";

$stmtEmployeur = $pdo->prepare($sqlEmployeur);
$stmtEmployeur->execute();

$employeurs = $stmtEmployeur->fetchAll();


/*
|--------------------------------------------------------------------------
| Conservation des valeurs après une erreur PHP
|--------------------------------------------------------------------------
*/

$ancienneValeur = $_SESSION ?? [];

?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Ajout d'un pompier</title>


    <!-- Bootstrap 5 -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <style>

        body {
            background-color: #f5f6f8;
        }

        .form-container {
            max-width: 1000px;
            margin: 40px auto;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background: #dc3545;
            color: white;
            border-radius: 12px 12px 0 0 !important;
        }

        .required::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }

        .form-control:focus,
        .form-select:focus {

            border-color: #dc3545;

            box-shadow:
                0 0 0 0.2rem rgba(220, 53, 69, 0.15);
        }

        .form-check-input:checked {

            background-color: #dc3545;

            border-color: #dc3545;
        }

        .section-title {

            font-size: 1.1rem;

            font-weight: 600;

            margin-bottom: 15px;

            padding-bottom: 8px;

            border-bottom: 1px solid #dee2e6;
        }

        #professionnelFields,
        #volontaireFields {

            display: none;

            background-color: #f8f9fa;

            border-radius: 8px;

            padding: 20px;

            margin-top: 15px;
        }

        .matricule-message {

            min-height: 24px;

            margin-top: 5px;

            font-size: 0.9rem;
        }

        .success-text {
            color: #198754;
        }

        .error-text {
            color: #dc3545;
        }

    </style>

</head>


<body>


<div class="container form-container">

    <div class="card">

        <!-- HEADER -->

        <div class="card-header p-4">

            <h1 class="h3 mb-1">
                Ajout d'un pompier
            </h1>

            <p class="mb-0">
                Les champs marqués d'un * sont obligatoires.
            </p>

        </div>


        <div class="card-body p-4">


            <form
                id="formPompier"
                action="traitementPompier.php"
                method="POST"
                novalidate
            >


                <!-- ================================================= -->
                <!-- IDENTITE -->
                <!-- ================================================= -->

                <div class="section-title">
                    Informations personnelles
                </div>


                <div class="row mb-4">


                    <!-- MATRICULE -->

                    <div class="col-md-6 mb-3 mb-md-0">

                        <label
                            for="matricule"
                            class="form-label required"
                        >
                            Matricule
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="matricule"
                            name="matricule"
                            maxlength="9"
                            inputmode="numeric"
                            pattern="[0-9]+"
                            autocomplete="off"
                            required
                        >

                        <div
                            id="matriculeMessage"
                            class="matricule-message"
                        ></div>

                        <div class="invalid-feedback">

                            Le matricule est obligatoire et doit
                            contenir uniquement des chiffres.

                        </div>

                    </div>


                    <!-- DATE NAISSANCE -->

                    <div class="col-md-6">

                        <label
                            for="dateNaissance"
                            class="form-label required"
                        >
                            Date de naissance
                        </label>

                        <input
                            type="date"
                            class="form-control"
                            id="dateNaissance"
                            name="dateNaissance"
                            required
                        >

                        <div class="invalid-feedback">

                            Veuillez renseigner une date de
                            naissance valide.

                        </div>

                    </div>

                </div>


                <!-- NOM + PRENOM -->

                <div class="row mb-4">


                    <div class="col-md-6 mb-3 mb-md-0">

                        <label
                            for="nom"
                            class="form-label required"
                        >
                            Nom
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="nom"
                            name="nom"
                            maxlength="50"
                            autocomplete="family-name"
                            pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s'-]+"
                            required
                        >

                        <div class="invalid-feedback">

                            Le nom est obligatoire et doit
                            contenir uniquement des lettres.

                        </div>

                    </div>


                    <div class="col-md-6">

                        <label
                            for="prenom"
                            class="form-label required"
                        >
                            Prénom
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="prenom"
                            name="prenom"
                            maxlength="50"
                            autocomplete="given-name"
                            pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s'-]+"
                            required
                        >

                        <div class="invalid-feedback">

                            Le prénom est obligatoire et doit
                            contenir uniquement des lettres.

                        </div>

                    </div>

                </div>


                <!-- SEXE -->

                <div class="row mb-4">

                    <div class="col-md-6">

                        <label class="form-label required">
                            Sexe
                        </label>

                        <div>


                            <div class="form-check form-check-inline">

                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="sexe"
                                    id="feminin"
                                    value="féminin"
                                    required
                                >

                                <label
                                    class="form-check-label"
                                    for="feminin"
                                >
                                    Féminin
                                </label>

                            </div>


                            <div class="form-check form-check-inline">

                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="sexe"
                                    id="masculin"
                                    value="masculin"
                                >

                                <label
                                    class="form-check-label"
                                    for="masculin"
                                >
                                    Masculin
                                </label>

                            </div>


                            <div class="form-check form-check-inline">

                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="sexe"
                                    id="autre"
                                    value="autre"
                                >

                                <label
                                    class="form-check-label"
                                    for="autre"
                                >
                                    Autre
                                </label>

                            </div>

                        </div>

                        <div class="invalid-feedback">

                            Veuillez sélectionner un sexe.

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- INFORMATIONS POMPIER -->
                <!-- ================================================= -->

                <div class="section-title">

                    Informations du pompier

                </div>


                <div class="row mb-4">


                    <!-- GRADE -->

                    <div class="col-md-6 mb-3 mb-md-0">

                        <label
                            for="grade"
                            class="form-label required"
                        >
                            Grade
                        </label>

                        <select
                            class="form-select"
                            id="grade"
                            name="grade"
                            required
                        >

                            <option
                                value=""
                                selected
                                disabled
                            >
                                Sélectionnez un grade
                            </option>


                            <?php foreach ($grades as $grade): ?>

                                <option
                                    value="<?= htmlspecialchars($grade['idGrade']) ?>"
                                >

                                    <?= htmlspecialchars($grade['LblGrade']) ?>

                                </option>

                            <?php endforeach; ?>


                        </select>

                        <div class="invalid-feedback">

                            Veuillez sélectionner un grade.

                        </div>

                    </div>


                    <!-- TELEPHONE -->

                    <div class="col-md-6">

                        <label
                            for="telephone"
                            class="form-label required"
                        >
                            Téléphone
                        </label>

                        <input
                            type="tel"
                            class="form-control"
                            id="telephone"
                            name="telephone"
                            maxlength="20"
                            inputmode="tel"
                            placeholder="0612345678"
                            autocomplete="tel"
                            required
                        >

                        <div class="invalid-feedback">

                            Le téléphone est obligatoire.

                        </div>

                    </div>

                </div>


                <!-- CASERNE -->

                <div class="row mb-4">

                    <div class="col-md-6">

                        <label
                            for="caserne"
                            class="form-label required"
                        >
                            Caserne
                        </label>

                        <select
                            class="form-select"
                            id="caserne"
                            name="caserne"
                            required
                        >

                            <option
                                value=""
                                selected
                                disabled
                            >
                                Sélectionnez une caserne
                            </option>


                            <?php foreach ($casernes as $caserne): ?>

                                <option
                                    value="<?= htmlspecialchars($caserne['idCaserne']) ?>"
                                >

                                    <?= htmlspecialchars($caserne['NomCaserne']) ?>

                                </option>

                            <?php endforeach; ?>


                        </select>

                        <div class="invalid-feedback">

                            Veuillez sélectionner une caserne.

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- TYPE -->
                <!-- ================================================= -->

                <div class="section-title">

                    Type de pompier

                </div>


                <div class="mb-3">

                    <div class="form-check form-check-inline">

                        <input
                            class="form-check-input"
                            type="radio"
                            name="typePompier"
                            id="professionnel"
                            value="Professionnel"
                            required
                        >

                        <label
                            class="form-check-label"
                            for="professionnel"
                        >
                            Professionnel
                        </label>

                    </div>


                    <div class="form-check form-check-inline">

                        <input
                            class="form-check-input"
                            type="radio"
                            name="typePompier"
                            id="volontaire"
                            value="Volontaire"
                        >

                        <label
                            class="form-check-label"
                            for="volontaire"
                        >
                            Volontaire
                        </label>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- PROFESSIONNEL -->
                <!-- ================================================= -->

                <div id="professionnelFields">

                    <h2 class="h5">
                        Informations du pompier professionnel
                    </h2>


                    <div class="row">


                        <div class="col-md-6">

                            <label
                                for="dateEmbauche"
                                class="form-label"
                            >
                                Date d'embauche
                            </label>

                            <input
                                type="date"
                                class="form-control"
                                id="dateEmbauche"
                                name="dateEmbauche"
                            >

                            <div class="invalid-feedback">

                                La date d'embauche est obligatoire.

                            </div>

                        </div>


                        <div class="col-md-6">

                            <label
                                for="indice"
                                class="form-label"
                            >
                                Indice
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="indice"
                                name="indice"
                                maxlength="30"
                            >

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- VOLONTAIRE -->
                <!-- ================================================= -->

                <div id="volontaireFields">

                    <h2 class="h5">
                        Informations du pompier volontaire
                    </h2>


                    <div class="row">


                        <!-- BIP -->

                        <div class="col-md-6 mb-3">

                            <label
                                for="bip"
                                class="form-label"
                            >
                                BIP
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                id="bip"
                                name="bip"
                                min="0"
                            >

                        </div>


                        <!-- EMPLOYEUR -->

                        <div class="col-md-6">

                            <label
                                for="employeur"
                                class="form-label required"
                            >
                                Employeur
                            </label>

                            <select
                                class="form-select"
                                id="employeur"
                                name="employeur"
                            >

                                <option
                                    value=""
                                    selected
                                    disabled
                                >
                                    Sélectionnez un employeur
                                </option>


                                <?php foreach ($employeurs as $employeur): ?>

                                    <option
                                        value="<?= htmlspecialchars($employeur['idEmployeur']) ?>"
                                    >

                                        <?= htmlspecialchars(
                                            $employeur['nomEmployeur']
                                            . ' '
                                            . $employeur['prenomEmployeur']
                                            . (
                                                !empty($employeur['Entreprise_Institution'])
                                                ? ' - ' . $employeur['Entreprise_Institution']
                                                : ''
                                            )
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>


                            </select>

                            <div class="invalid-feedback">

                                Veuillez sélectionner un employeur.

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- BOUTONS -->
                <!-- ================================================= -->

                <div class="d-flex gap-2 mt-4">

                    <button
                        type="submit"
                        class="btn btn-danger"
                        id="btnValider"
                    >
                        Valider
                    </button>


                    <button
                        type="reset"
                        class="btn btn-secondary"
                    >
                        Réinitialiser
                    </button>

                </div>


            </form>

        </div>

    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| FORMULAIRE
|--------------------------------------------------------------------------
*/

const form = document.getElementById("formPompier");


/*
|--------------------------------------------------------------------------
| CHAMPS TYPE POMPIER
|--------------------------------------------------------------------------
*/

const professionnel = document.getElementById("professionnel");
const volontaire = document.getElementById("volontaire");

const professionnelFields =
    document.getElementById("professionnelFields");

const volontaireFields =
    document.getElementById("volontaireFields");

const dateEmbauche =
    document.getElementById("dateEmbauche");

const employeur =
    document.getElementById("employeur");


function afficherChampsType() {

    if (professionnel.checked) {

        professionnelFields.style.display = "block";

        volontaireFields.style.display = "none";

        dateEmbauche.required = true;

        employeur.required = false;

    }


    else if (volontaire.checked) {

        professionnelFields.style.display = "none";

        volontaireFields.style.display = "block";

        dateEmbauche.required = false;

        employeur.required = true;

    }

}


professionnel.addEventListener(
    "change",
    afficherChampsType
);

volontaire.addEventListener(
    "change",
    afficherChampsType
);


/*
|--------------------------------------------------------------------------
| MATRICULE : uniquement chiffres
|--------------------------------------------------------------------------
*/

const matricule =
    document.getElementById("matricule");

const matriculeMessage =
    document.getElementById("matriculeMessage");


matricule.addEventListener("input", function() {

    this.value = this.value.replace(/[^0-9]/g, "");

    matriculeMessage.textContent = "";

    this.classList.remove("is-valid");
    this.classList.remove("is-invalid");

});


/*
|--------------------------------------------------------------------------
| VERIFICATION DU MATRICULE
|--------------------------------------------------------------------------
*/

let matriculeDisponible = false;


matricule.addEventListener("blur", function() {

    const valeur = this.value.trim();


    if (valeur === "") {

        matriculeDisponible = false;

        return;

    }


    fetch(
        "verifierMatricule.php?matricule="
        + encodeURIComponent(valeur)
    )

    .then(response => response.json())

    .then(data => {

        if (data.existe) {

            matriculeDisponible = false;

            matricule.classList.add("is-invalid");

            matriculeMessage.textContent =
                "❌ Ce matricule existe déjà. Veuillez en saisir un autre.";

            matriculeMessage.className =
                "matricule-message error-text";

        }

        else {

            matriculeDisponible = true;

            matricule.classList.remove("is-invalid");

            matricule.classList.add("is-valid");

            matriculeMessage.textContent =
                "✓ Ce matricule est disponible.";

            matriculeMessage.className =
                "matricule-message success-text";

        }

    })

    .catch(error => {

        matriculeDisponible = false;

        matriculeMessage.textContent =
            "Impossible de vérifier le matricule.";

        matriculeMessage.className =
            "matricule-message error-text";

    });

});


/*
|--------------------------------------------------------------------------
| NOM / PRENOM
|--------------------------------------------------------------------------
*/

["nom", "prenom"].forEach(function(id) {

    document
        .getElementById(id)
        .addEventListener("input", function() {

            this.value = this.value.replace(
                /[^A-Za-zÀ-ÖØ-öø-ÿ\s'-]/g,
                ""
            );

        });

});


/*
|--------------------------------------------------------------------------
| TELEPHONE
|--------------------------------------------------------------------------
*/

document
    .getElementById("telephone")
    .addEventListener("input", function() {

        this.value = this.value.replace(
            /[^0-9+ ]/g,
            ""
        );

    });


/*
|--------------------------------------------------------------------------
| DATE DE NAISSANCE
|--------------------------------------------------------------------------
*/

document
    .getElementById("dateNaissance")
    .addEventListener("change", function() {

        const dateNaissance =
            new Date(this.value);

        const aujourdHui =
            new Date();

        aujourdHui.setHours(0, 0, 0, 0);


        if (dateNaissance > aujourdHui) {

            this.setCustomValidity(
                "La date ne peut pas être dans le futur."
            );

        }

        else {

            this.setCustomValidity("");

        }

    });


/*
|--------------------------------------------------------------------------
| DATE D'EMBAUCHE
|--------------------------------------------------------------------------
*/

dateEmbauche.addEventListener(
    "change",
    function() {

        const date =
            new Date(this.value);

        const aujourdHui =
            new Date();

        aujourdHui.setHours(0, 0, 0, 0);


        if (date > aujourdHui) {

            this.setCustomValidity(
                "La date d'embauche ne peut pas être dans le futur."
            );

        }

        else {

            this.setCustomValidity("");

        }

    }
);


/*
|--------------------------------------------------------------------------
| VALIDATION FORMULAIRE
|--------------------------------------------------------------------------
*/

form.addEventListener(
    "submit",
    function(event) {

        /*
        |---------------------------------------------
        | Vérification matricule
        |---------------------------------------------
        */

        if (!matriculeDisponible) {

            event.preventDefault();

            matricule.classList.add("is-invalid");

            matriculeMessage.textContent =
                "❌ Veuillez saisir un matricule disponible.";

            matriculeMessage.className =
                "matricule-message error-text";

        }


        /*
        |---------------------------------------------
        | Validation Bootstrap
        |---------------------------------------------
        */

        if (!form.checkValidity()) {

            event.preventDefault();

            event.stopPropagation();

        }


        form.classList.add("was-validated");

    }
);


/*
|--------------------------------------------------------------------------
| VALIDATION EN TEMPS REEL
|--------------------------------------------------------------------------
*/

const champs =
    form.querySelectorAll(
        "input, select"
    );


champs.forEach(function(champ) {

    champ.addEventListener(
        "blur",
        function() {

            if (champ.checkValidity()) {

                champ.classList.remove("is-invalid");

                champ.classList.add("is-valid");

            }

            else {

                champ.classList.remove("is-valid");

                champ.classList.add("is-invalid");

            }

        }
    );

});


/*
|--------------------------------------------------------------------------
| RESET
|--------------------------------------------------------------------------
*/

form.addEventListener(
    "reset",
    function() {

        setTimeout(function() {

            professionnelFields.style.display = "none";

            volontaireFields.style.display = "none";

            dateEmbauche.required = false;

            employeur.required = false;

            matriculeDisponible = false;

            matriculeMessage.textContent = "";

            matricule.classList.remove("is-valid");
            matricule.classList.remove("is-invalid");

            form.classList.remove("was-validated");

        }, 0);

    }
);

</script>

</body>

</html>