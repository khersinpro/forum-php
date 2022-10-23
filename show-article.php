<?php 
    // filtre de le requette, recupération des données, recupération de l'id de la requete
    $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $filename =  __DIR__.'/data/articles.json';
    $articles = [];
    $id = $_GET["id"] ?? "";

    if(!$id) {
        header('Location: /');
    } else {
        if(file_exists($filename)){
            $articles = json_decode(file_get_contents($filename), true) ?? [];
            // récupération de la clé de l'article grâce a son ID
            $articleIndex = array_search($id, array_column($articles, 'id'));
            // Récupération des données de l'article grâce a la clés $articleIndex
            $article = $articles[$articleIndex];
        }
    }


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include_once './includes/head.php' ?>
    <link rel="stylesheet" href="./public/css/index.css">
    <link rel="stylesheet" href="./public/css/show-article.css">
    <title><?= $article['title'] ?></title>
</head>

<body>
    <div class="container">
        <?php require_once './includes/header.php' ?>

        <div class="content">
            <div class="article-container">
                <a class="article-back" href="/">Retour à la liste des articles</a>
                <div class="article-cover-img" style="background-image: url(<?= $article['image'] ?>);"></div>
                <h1 class="article-title"><?= $article['title'] ?></h1>
                <div class="separator"></div>
                <p class="article-content"><?= $article['content'] ?></p>
                <div class="action">
                    <a href="/delete-article.php?id=<?= $id ?>" class="btn btn-secondary">Supprimer</a>
                    <a href="/form-article.php?id=<?= $article['id'] ?>" class="btn btn-primary">
                        Editer l'article
                    </a>
                </div>
            </div>
        </div>
        
        <?php include_once './includes/footer.php' ?>
    </div>
</body>
</html>