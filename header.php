<?php require_once 'functions.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <script src="https://kit.fontawesome.com/811c866c3a.js" crossorigin="anonymous"></script>
    <script defer src="./assets/js/script.js"></script>
</head>

<body>
    <header class="bg-light box-shadow-main fixed-top">
        <nav class="d-flex w-75 mx-auto justify-content-between align-items-center py-2 fs-5">
            <ul class="d-flex list-unstyled m-0">
                <li class="px-2"><a href="index.php" class="text-black">Accueuil</a></li>
                <li class="px-2"><a href="panel.php" class="text-black">Panel</a></li>
            </ul>
            <div class="d-flex align-items-center justify-content-between">
                <?php if (checkConnection()) : ?>
                    <p class="m-0"><?= $_SESSION['user'] ?></p>
                    <a class="btn btn-danger ms-2" href="checker.php?disconnect=1">Déconnexion</a>
                <?php else : ?>
                    <a class="btn btn-success" href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>