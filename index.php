<?php
const ERROR_REQUIRED = 'Veuillez renseigner une todo';
const ERROR_TOO_SHORT = 'Veuillez entrer au moins 5 caractéres';

$filename = __DIR__."/data/todos.json";
$error = '';
$todo = '';
$todos = [];

// Verification de l'existance du fichier todos.json
if(file_exists($filename)) {
    $data = file_get_contents($filename);     // Récupération des données stocket dans le fichier todos.json
    $todos = json_decode($data, true) ?? [];  // true pour recupérer un tableau a la place d'un objet
} 

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // filtre l'input des caractéres speciaux et les remplace , empeche les attaques XSS
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // Récupération du todo
    $todo = $_POST['todo'] ?? "";

    // Gestion d'erreur
    if(!$todo) {
        $error = ERROR_REQUIRED;
    } elseif(mb_strlen($todo) < 5) {
        $error = ERROR_TOO_SHORT;
    }

    // Si aucune erreur, on ajoute la todo dans la var todos et on le place dans data.json en format json
    if(!$error) {
        $todos = [...$todos, [
            'name' => $todo,
            'done' => false,
            'id' => time() // time retourne le nombre de miliseconde depuis 1970
        ]];
        file_put_contents($filename, json_encode($todos));
    }

}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include_once './includes/head.php' ?>
    <title>Todo PHP</title>
</head>

<body>
    <div class="container">
        <?php require_once './includes/header.php' ?>
        <div class="content">
            <div class="todo-container">
                <h1>Ma Todo</h1>
                <form class="todo-form" action='/' method="post">
                    <input name='todo' value="<?= $todo ?>" type="text">
                    <button class="btn btn-primary" type="submit">Ajouter</button>
                </form>
                <?php if ($error) : ?>
                    <p class="text-danger"><?= $error ?></p>
                <?php endif; ?>
                <ul class="todo-list">
                    <?php foreach($todos as $t): ?>
                        <li class="todo-item <?= $t['done'] ? "low-opacity" : "" ?>">
                            <span class="todo-name"><?= $t['name'] ?></span>
                            <a href="/edit-todo.php?id=<?= $t['id'] ?>">
                                <button class="btn btn-primary btn-small">Valider</button>
                            </a>
                            <button class="btn btn-danger btn-small">Supprimer</button>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
        <?php include_once './includes/footer.php' ?>
    </div>
</body>
</html>