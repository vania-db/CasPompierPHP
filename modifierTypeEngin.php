<?php
require_once "connexion.php";

function e($valeur) {
    return htmlspecialchars((string)$valeur, ENT_QUOTES, 'UTF-8');
}

$id = trim($_GET["id"] ?? "");
$erreurs = [];

if ($id === "" || strlen($id) > 20) {
    http_response_code(400);
    exit("Identifiant invalide.");
}

$stmt = $pdo->prepare(
    "SELECT idTypeEngin, LblTypeEngin, imageTypeEngin
     FROM TypeEngin
     WHERE idTypeEngin = :id"
);
$stmt->execute([":id" => $id]);
$engin = $stmt->fetch();

if (!$engin) {
    http_response_code(404);
    exit("Type d'engin introuvable.");
}

$libelle = $engin["LblTypeEngin"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $libelle = trim($_POST["LblTypeEngin"] ?? "");

    if ($libelle === "" || mb_strlen($libelle) > 200) {
        $erreurs[] = "Le libellé est obligatoire et ne doit pas dépasser 200 caractères.";
    }

    $nouvelleImage = null;

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
            $erreurs[] = "Le téléversement de l'image a échoué.";
        } elseif ($_FILES["image"]["size"] > 3 * 1024 * 1024) {
            $erreurs[] = "L'image ne doit pas dépasser 3 Mo.";
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES["image"]["tmp_name"]);

            $extensions = [
                "image/jpeg" => "jpg",
                "image/png"  => "png",
                "image/webp" => "webp"
            ];

            if (!isset($extensions[$mime])) {
                $erreurs[] = "Formats autorisés : JPG, PNG et WEBP.";
            } else {
                $dossier = __DIR__ . "/images/";

                if (!is_dir($dossier) && !mkdir($dossier, 0755, true)) {
                    $erreurs[] = "Impossible de créer le dossier images.";
                } else {
                    $nomFichier = bin2hex(random_bytes(16)) . "." . $extensions[$mime];

                    if (move_uploaded_file(
                        $_FILES["image"]["tmp_name"],
                        $dossier . $nomFichier
                    )) {
                        $nouvelleImage = "images/" . $nomFichier;
                    } else {
                        $erreurs[] = "Impossible d'enregistrer l'image.";
                    }
                }
            }
        }
    }

    if (!$erreurs) {
        try {
            if ($nouvelleImage !== null) {
                $sql = "UPDATE TypeEngin
                        SET LblTypeEngin = :libelle,
                            imageTypeEngin = :image
                        WHERE idTypeEngin = :id";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ":libelle" => $libelle,
                    ":image" => $nouvelleImage,
                    ":id" => $id
                ]);

                // Supprimer l'ancienne image après la mise à jour.
                $ancienneImage = $engin["imageTypeEngin"];

                if (
                    $ancienneImage &&
                    str_starts_with($ancienneImage, "images/") &&
                    is_file(__DIR__ . "/" . $ancienneImage)
                ) {
                    unlink(__DIR__ . "/" . $ancienneImage);
                }
            } else {
                $sql = "UPDATE TypeEngin
                        SET LblTypeEngin = :libelle
                        WHERE idTypeEngin = :id";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ":libelle" => $libelle,
                    ":id" => $id
                ]);
            }

            header("Location: typeEngin.php");
            exit;
        } catch (PDOException $e) {
            if ($nouvelleImage !== null && is_file(__DIR__ . "/" . $nouvelleImage)) {
                unlink(__DIR__ . "/" . $nouvelleImage);
            }

            $erreurs[] = "La modification a échoué.";
        }
    } elseif ($nouvelleImage !== null && is_file(__DIR__ . "/" . $nouvelleImage)) {
        unlink(__DIR__ . "/" . $nouvelleImage);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier un type d'engin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5">
    <h1 class="mb-4">Modifier un type d'engin</h1>

    <?php foreach ($erreurs as $erreur): ?>
        <div class="alert alert-danger"><?= e($erreur) ?></div>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data"
          class="card card-body shadow-sm">

        <div class="mb-3">
            <label class="form-label">Identifiant</label>
            <input type="text" class="form-control"
                   value="<?= e($engin['idTypeEngin']) ?>" disabled>
            <div class="form-text">L'identifiant n'est pas modifiable.</div>
        </div>

        <div class="mb-3">
            <label for="LblTypeEngin" class="form-label">Libellé</label>
            <input type="text" class="form-control" id="LblTypeEngin"
                   name="LblTypeEngin" maxlength="200" required
                   value="<?= e($libelle) ?>">
        </div>

        <?php if ($engin["imageTypeEngin"]): ?>
            <div class="mb-3">
                <p>Image actuelle :</p>
                <img src="<?= e($engin["imageTypeEngin"]) ?>"
                     alt="Image actuelle" style="max-width:250px; max-height:180px;">
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="image" class="form-label">Remplacer l'image (facultatif)</label>
            <input type="file" class="form-control" id="image"
                   name="image" accept=".jpg,.jpeg,.png,.webp">
            <div class="form-text">Laisser vide pour conserver l'image actuelle.</div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-danger">Enregistrer les modifications</button>
            <a href="typeEngin.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</main>
</body>
</html>