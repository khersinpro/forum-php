<?php
    $filename = __DIR__.'/data/articles.json';
    $articles = [];
    $categories = [];

    // Logique de récupération du choix de catégorie d'article par l'utilisateur
    $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $selectedCat = $_GET['cat'] ?? '';

    if(file_exists($filename)) {
        $articles = json_decode(file_get_contents($filename), true);
        // récupération des toutes les catégorie des articles
        $cattmp = array_map(fn($a) => $a['category'], $articles);

        // Récupération du nb d'article par catégorie
        $categories = array_reduce($cattmp, function($acc, $cat) {
            if(isset($acc[$cat])) {
                $acc[$cat]++;
            } else {
                $acc[$cat] = 1;
            }
            return $acc;
        } ,[]);

        // Tri des articles par catégorie
        $articlesPerCategories = array_reduce($articles, function($acc, $article) {
            if(isset($acc[$article['category']])) {
                $acc[$article['category']] = [...$acc[$article['category']], $article];
            } else {
                $acc[$article['category']] = [$article];
            }
            return $acc;
        }, []);
 }

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include_once './includes/head.php' ?>
    <link rel="stylesheet" href="./public/css/index.css">
    <title>Blog PHP</title>
</head>

<body>
    <div class="container">
        <?php require_once './includes/header.php' ?>

        <div class="content">
            <div class="newsfeed-container">

                <ul class="category-container">
                    <li class="<?= $selectedCat ? "" : "cat-active" ?>">
                        <a href='/'>Tous les articles <span class="small">(<?= count($articles) ?>)</span></a>
                    </li>
                    <?php foreach($categories as $catNames => $catNum): ?>
                        <li class="<?= $selectedCat === $catNames ? 'cat-active' : '' ?>">
                            <a href="/?cat=<?= $catNames ?>"><?= $catNames ?> <span class="small">(<?=$catNum?>)</span></a>
                        </li>
                    <?php endforeach?>
                </ul>
                
                <div class="newsfeed-content">
                    <?php if(!$selectedCat) : ?>
                        <?php foreach($categories as $cat => $num): ?>
                            <h2><?= $cat ?></h2>
                            <div class="articles-container">
                                <?php foreach($articlesPerCategories[$cat] as $a): ?>
                                    <a href="/show-article.php?id=<?= $a['id'] ?>" class="article box">
                                        <div class="overflow">
                                            <div class="image-container" style="background-image:url(<?= $a['image'] ?>) ;"></div>
                                        </div>
                                        <h3><?= $a['title'] ?></h3>
                                    </a>
                                <?php endforeach ?>
                            </div>
                        <?php endforeach ?>
                    <?php else : ?>
                        <h2><?= $selectedCat ?></h2>
                        <div class="articles-container">
                            <?php foreach($articlesPerCategories[$selectedCat] as $a): ?>
                                <a href="/show-article.php?id=<?= $a['id'] ?>" class="article box">
                                    <div class="overflow">
                                        <div class="image-container" style="background-image:url(<?= $a['image'] ?>) ;"></div>
                                    </div>
                                    <h3><?= $a['title'] ?></h3>
                                </a>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>    
                </div>
                
            </div>
        </div>
        
        <?php include_once './includes/footer.php' ?>
    </div>
</body>
</html>