<?php
// Messages d'erreur a afficher
const ERROR_REQUIRED = 'Veuillez renseigner ce champ';
const ERROR_TOO_SHORT = 'Le titre est trop court';
const ERROR_CONTENT_TOO_SHORT = 'L\'article est trop court';
const ERROR_IMAGE_URL = 'L\'image doit être une url valide';

$filename = __DIR__.'/data/articles.json';
// Tableau de gestion d'erreur
$errors = [
    'title' => '',
    'image' => '',
    'category' => '',
    'content' => ''
];
$articles = [];
$category = "";

// Sécurisation de l'url et récupération de l'id de larticle si c'est une modification d'article
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';

if(file_exists($filename)) {
    $articles = json_decode(file_get_contents($filename), true) ?? []; 
}

// Si la requete cocerne une modification d'article
if($id){
    // Récupération de l'index de l'article
    $articleIndex = array_search($id, array_column($articles, 'id'));
    // Récupération des infos de l'article grace a son index ($articleIndex)
    $article = $articles[$articleIndex];
    // Stockage des données de l'article dans les variables destinées a l'affichage das les inputs 
    $title = $article['title'];
    $image = $article['image'];
    $category = $article['category'];
    $content = $article['content'];
}

// Si la request method est un post , on applique la logique suivante :
if($_SERVER['REQUEST_METHOD'] === 'POST')  {

    $_POST = filter_input_array(INPUT_POST, [
        'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'image' => FILTER_SANITIZE_URL,
        'category' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'content' => [
            'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'flags' => FILTER_FLAG_NO_ENCODE_QUOTES
        ]
    ]);

    $title = $_POST['title'] ?? '';
    $image = $_POST['image'] ?? '';
    $category = $_POST['category'] ?? '';
    $content = $_POST['content'] ?? '';

    // Gestion d'erreurs pour tout le formulaire
    if(!$title) {
        $errors["title"] = ERROR_REQUIRED;
    } else if(mb_strlen($title) < 5) {
        $errors["title"] = ERROR_TOO_SHORT;
    }

    if(!$image) {
        $errors["image"] = ERROR_REQUIRED;
    } else if(!filter_var($image, FILTER_VALIDATE_URL)) {
        $errors["image"] = ERROR_IMAGE_URL;
    }
    
    if(!$category) {
        $errors["category"] = ERROR_REQUIRED;
    }
    
    if(!$content) {
        $errors["content"] = ERROR_REQUIRED;
    } else if(mb_strlen($content) < 50) {
        $errors["content"] = ERROR_TOO_SHORT;
    }

    // Vérifie si le tableau d'erreur est vide grace a empty()
    if(empty(array_filter($errors, fn($e) => $e !== ''))) {
        if($id) { // Si il y a un $id en param , on modifie
            $articles[$articleIndex]["title"] = $title;
            $articles[$articleIndex]["image"] = $image;
            $articles[$articleIndex]["category"] = $category;
            $articles[$articleIndex]["content"] = $content;
        } else { // Sinon on créer un article dans $articles
            $articles = [...$articles, [
                'title' => $title,
                'image' => $image,
                'category' => $category,
                'content' => $content,
                'id' => time()
            ]];
        }
        // Sauvegarde des articles puis redirection
        file_put_contents($filename, json_encode($articles));
        header('Location: /');
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include_once './includes/head.php' ?>
    <link rel="stylesheet" href="./public/css/form-article.css">
    <title><?= $id ? "Modifier" : "Écrire" ?> un article</title>
</head>

<body>
    <div class="container">
        <?php require_once './includes/header.php' ?>
        <div class="content">
            <div class="block p-20 form-container">
                <h1><?= $id ? "Modifier" : "Écrire" ?> un article</h1>
                <form action="/form-article.php<?= $id? "?id=$id" : "" ?>" method="POST">
                    <div class="form-control">
                        <label for="title">Titre</label>
                        <input type="text" name="title" id="title" value="<?= $title ?? "" ?>">
                        <?php if($errors['title']) : ?>
                            <p class="text-danger">
                                <?= $errors['title'] ?>
                            </p>
                        <?php endif ?>
                    </div>
                    <div class="form-control">
                        <label for="image">Image</label>
                        <input type="text" name="image" id="image" value="<?= $image ?? "" ?>">
                        <?php if($errors['image']) : ?>
                            <p class="text-danger">
                                <?= $errors['image'] ?>
                            </p>
                        <?php endif ?>
                    </div>
                    <div class="form-control">
                        <label for="category">Catégorie</label>
                        <select name="category" id="category">
                            <option <?= !$category|| $category === "technologie" ? "selected" : "" ?> value="technologie">Technologie</option>
                            <option <?= $category === "nature" ? "selected" : "" ?> value="nature">Nature</option>
                            <option <?= $category === "politique" ? "selected" : "" ?> value="politique">Politique</option>
                        </select>
                        <?php if($errors['category']) : ?>
                            <p class="text-danger">
                                <?= $errors['category'] ?>
                            </p>
                        <?php endif ?>
                    </div>
                    <div class="form-control">
                        <label for="content">Content</label>
                        <textarea name="content" id="content" ><?= $content ?? "" ?></textarea>
                        <?php if($errors['content']) : ?>
                            <p class="text-danger">
                                <?= $errors['content'] ?>
                            </p>
                        <?php endif ?>
                    </div>
                    <div class="form-action">
                        <a href="/" type="button" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary"><?= $id ? 'Modifier' : 'Sauvegarder' ?></button>
                    </div>
                </form>
            </div>
        </div>
        <?php include_once './includes/footer.php' ?>
    </div>
</body>
</html>