<?php
require_once "connexion.php";

$erreurs = [];
$id = "";
$libelle = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = trim($_POST["idTypeEngin"] ?? "");
    $libelle = trim($_POST["LblTypeEngin"] ?? "");

    if ($id === "" || strlen($id) > 20) {
        $erreurs[] = "L'identifiant est obligatoire et ne doit pas dépasser 20 caractères.";
    }

    if ($libelle === "" || mb_strlen($libelle) > 200) {
        $erreurs[] = "Le libellé est obligatoire et ne doit pas dépasser 200 caractères.";
    }

    $imageChemin = null;

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
                        $imageChemin = "images/" . $nomFichier;
                    } else {
                        $erreurs[] = "Impossible d'enregistrer l'image.";
                    }
                }
            }
        }
    }

    if (!$erreurs) {
        try {
            $sql = "INSERT INTO TypeEngin
                    (idTypeEngin, LblTypeEngin, imageTypeEngin)
                    VALUES (:id, :libelle, :image)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":id" => $id,
                ":libelle" => $libelle,
                ":image" => $imageChemin
            ]);

            header("Location: typeEngin.php");
            exit;
        } catch (PDOException $e) {
            if ($imageChemin !== null && is_file(__DIR__ . "/" . $imageChemin)) {
                unlink(__DIR__ . "/" . $imageChemin);
            }

            if ($e->getCode() === "23000") {
                $erreurs[] = "Cet identifiant existe déjà.";
            } else {
                $erreurs[] = "Erreur lors de l'enregistrement en base de données.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter un type d'engin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5">
    <h1 class="mb-4">Ajouter un type d'engin</h1>

    <?php foreach ($erreurs as $erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data"
          class="card card-body shadow-sm">

        <div class="mb-3">
            <label for="idTypeEngin" class="form-label">Identifiant</label>
            <input type="text" class="form-control" id="idTypeEngin"
                   name="idTypeEngin" maxlength="20" required
                   value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="mb-3">
            <label for="LblTypeEngin" class="form-label">Libellé</label>
            <input type="text" class="form-control" id="LblTypeEngin"
                   name="LblTypeEngin" maxlength="200" required
                   value="<?= htmlspecialchars($libelle, ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Image (facultative)</label>
            <input type="file" class="form-control" id="image"
                   name="image" accept=".jpg,.jpeg,.png,.webp">
            <div class="form-text">JPG, PNG ou WEBP, 3 Mo maximum.</div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-danger">Enregistrer</button>
            <a href="typeEngin.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</main>
</body>
</html>