<?php require_once 'header.php';

if (!checkConnection()) {
    $root = explode('/', $_SERVER['SCRIPT_FILENAME']);
    header('location: login.php?redirect=' . end($root));
    exit;
}
?>
<section>
    <div class="container-fluid panel-container">
        <div class="row h-100">
            <div class="col-2 pt-3 bg-primary panel-col g-0">
                <h2 class="text-center mb-2 text-light"><?= $_SESSION['user'] ?>
                    <?php if ($_SESSION['role'] == 'mod') : ?>
                        <span>⚔️</span>
                    <?php elseif ($_SESSION['role'] == 'admin') : ?>
                        <span>👑</span>
                    <?php endif; ?>
                </h2>
                <span class="separator w-75 m-auto border-bottom"></span>
                <ul class="list-unstyled mt-2">
                    <li><a href="panel.php?action=myarticles" class="btn btn-primary rounded-0 panel-nav-btn w-100 text-light mt-2 fs-5">Mes Articles</a></li>
                    <li><a href="panel.php?action=write" class="btn btn-primary rounded-0 panel-nav-btn w-100 text-light mt-2 fs-5">Ecrire un article</a></li>
                </ul>
                <?php if ($_SESSION['role'] != 'user') : ?>
                    <h2 class="text-center mt-4 mb-2 text-light">Modération</h2>
                    <span class="separator w-75 m-auto border-bottom"></span>
                    <ul class="list-unstyled mt-2">
                        <li><a href="panel.php?action=modarticles" class="btn btn-primary rounded-0 panel-nav-btn w-100 text-light mt-2 fs-5">Articles</a></li>
                        <?php if ($_SESSION['role'] == 'admin') : ?>
                            <li><a href="panel.php?action=modusers" class="btn btn-primary rounded-0 panel-nav-btn w-100 text-light mt-2 fs-5">Utilisateurs</a></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="col-10">
                <?php if (isset($_GET['action']) && !empty($_GET['action'])) :
                    if ($_GET['action'] != 'write') :
                        $action = $_GET['action'] ?>
                        <form action="panel.php" method="GET">
                            <div class="justify-content-center input-group my-3 mx-auto">
                                <span class="input-group-text bg-primary text-light border border-primary border-opacity-50">Rechercher :</span>
                                <input type="text" placeholder="Rechercher" class="border border-primary p-2 border-opacity-50 w-75" name="search">
                                <button type="submit" class="btn-search position-relative px-3 border border-primary border-opacity-50 rounded-end">
                                    <span class="icon-search fa-solid fa-magnifying-glass"></span>
                                </button>
                            </div>
                        </form>
                        <span class="separator border-primary border-bottom"></span>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="container-fluid content-container">
                    <div class="row h-100">
                        <div class="col px-5 d-flex align-items-center flex-column">
                            <?php if (isset($_GET['action']) && !empty($_GET['action'])) :
                                require_once 'db.php';
                                $action = $_GET['action']; ?>
                                <?php if ($action == 'myarticles') :
                                    $userId = $_SESSION['user-id'];
                                    $sql = "SELECT * FROM articles WHERE `id_user`= $userId AND NOT `state`= 3";
                                    $query = mysqli_query($sqli, $sql);
                                    if (mysqli_num_rows($query) > 0) {
                                        while ($result = mysqli_fetch_assoc($query)) {
                                            drawContent($action, $result);
                                        }
                                    } else { ?>
                                        <div class="row p-2 w-100 h-100">
                                            <div class="col-12 d-flex flex-column align-items-center justify-content-center">
                                                <p class="m-0 text-secondary">Vous n'avez écris aucun article pour le moment </p>
                                                <a href="panel.php?action=write" class="btn btn-primary mt-2">écrire un article</a>
                                            </div>
                                        </div>
                                    <?php };
                                elseif ($action == 'modarticles' && $_SESSION['role'] != 'user') : ?>
                                    <?php if ($_SESSION['role'] != 'user') :
                                        $sql = "UPDATE users.state, articles.state SET state=3 FROM articles INNER JOIN users ON articles.id_user = users.id";
                                        $sql = "SELECT articles.*, username FROM articles INNER JOIN users ON articles.id_user = users.id";
                                        $query = mysqli_query($sqli, $sql); ?>
                                        <?php if (mysqli_num_rows($query) > 0) : ?>
                                            <?php while ($result = mysqli_fetch_assoc($query)) : ?>
                                                <?php drawContent($action, $result); ?>
                                            <?php endwhile ?>
                                        <?php else : ?>
                                            <div class="row p-2 w-100 h-100">
                                                <div class="col-12 d-flex flex-column align-items-center justify-content-center">
                                                    <p class="m-0 text-secondary">Il n'y a aucun article pour le moment </p>
                                                    <a href="panel.php?action=write" class="btn btn-primary mt-2">écrire un article</a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                <?php elseif ($action == 'write') : ?>
                                    <?php if (isset($_POST['id']) && !empty($_POST['id'])) :
                                        require_once 'db.php';
                                        $id = $_POST['id'];
                                        $sql = "SELECT * FROM articles WHERE id=$id";
                                        $query = mysqli_query($sqli, $sql);
                                        $result = mysqli_fetch_assoc($query);
                                        $link = 'checker.php?id=' . $id; ?>
                                    <?php else : ?>
                                        <?php $link = 'checker.php' ?>
                                    <?php endif;?>

                                    <form action="<?= $link ?>" method="post" class="w-100 h-75 d-flex flex-column px-5 align-items-center mt-4" enctype="multipart/form-data">
                                        <div class="input-group mx-auto mb-3 w-100">
                                            <span class="text-center input-group-text bg-primary text-light border border-primary border-opacity-50">Titre</span>
                                            <input type="text" name="title" class="w-75 rounded-end border border-primary p-2 border-opacity-50" value="<?php if (isset($_POST['id']) && !empty($_POST['id'])) : echo $result['title']; else : echo ''; endif?>">
                                        </div>
                                        <div class="form-floating mx-auto w-100 h-75 mb-3">
                                            <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea" name="message"><?php if (isset($_POST['id']) && !empty($_POST['id'])) : echo $result['content']; else : echo ''; endif ?></textarea>
                                            <label for="floatingTextarea">Contenu :</label>
                                        </div>
                                        <div class="input-group mx-auto mb-3 w-100">
                                            <span class="text-center input-group-text bg-primary text-light border border-primary border-opacity-50">Image :</span>
                                            <input type="file" name="file" class="w-75 rounded-end border border-primary p-2 border-opacity-50">
                                        </div>
                                        <div class="d-flex justify-content-between w-100">
                                            <div class="input-group">
                                                <span class="input-group-text bg-primary text-light">Privé ?</span>
                                                <?php if (isset($_POST['id']) && !empty($_POST['id']) && $result['state'] == 2) : ?>
                                                    <input checked type="checkbox" name="status" class="input-group-text bg-primary text-light">
                                                <?php else : ?>
                                                    <input type="checkbox" name="status" class="input-group-text bg-primary text-light">
                                                <?php endif; ?>
                                            </div>
                                            <input type="submit" class="btn btn-primary" name="post-article" value="Envoyer">
                                        </div>
                                    </form>

                                    <?php if (isset($_GET['success']) && !empty($_GET['success'])) :
                                        $success = $_GET['success'] ?>
                                        <?php if ($success == 3) : ?>
                                            <div>
                                                <p class="text-warning">Veuillez remplir tous les champs</p>
                                            </div>
                                        <?php elseif ($success == 2) : ?>
                                            <div>
                                                <p class="text-danger">ERROR</p>
                                            </div>
                                        <?php elseif ($success == 1) : ?>
                                            <div>
                                                <p class="text-success">Votre article a bien été enregistré</p>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                <?php elseif ($action == 'modusers' && $_SESSION['role'] == 'admin') : ?>
                                    <?php
                                    require 'db.php';
                                    $sql = "SELECT * FROM users";
                                    $query = mysqli_query($sqli, $sql);
                                    if (mysqli_num_rows($query) > 0) :
                                        while ($result = mysqli_fetch_assoc($query)) :
                                            $id = $result['id'];
                                            if ($result['state'] == 1) {
                                                $status = 'Public';
                                                $btnText = 'Rendre privé';
                                            } elseif ($result['state'] == 2) {
                                                $status = 'Privé';
                                                $btnText = 'Rendre public';
                                            } else {
                                                $status = 'Caché';
                                                $btnText = 'Rendre public';
                                            }?>
                                            <div class="row w-100 item-container justify-content-center my-2 box-shadow-main bg-light px-2 py-3 rounded-3 border-secondary border-opacity-25 border">
                                                <div class="row h-fit-content align-items-center">
                                                    <div class="col-3">
                                                        <p class="m-0"><span class="fw-bold">User : </span><?= $result['username'] ?></p>
                                                    </div>
                                                    <div class="col-3">Nombre d'articles :
                                                        <?php countArticles($id) ?>
                                                    </div>
                                                    <div class="col-3">
                                                        <a href="checker.php?action=1&target=users&state=<?= $result['state'] ?>&id=<?= $result['id'] ?>" class="btn btn-warning text-light mx-1"><?= $btnText ?></a>
                                                        <a href="checker.php?action=2&target=users&id=<?= $result['id'] ?>" class="btn btn-danger text-light mx-1">Supprimer</a>
                                                    </div>
                                                    <div class="col-2">
                                                        <p class="m-0"><span class="fw-bold">Status : </span><?= $status ?></p>
                                                    </div>
                                                    <div class="col-1">
                                                        <div class="icon-triangle"></div>
                                                    </div>
                                                </div>
                                                <div class="content-shower h-fit-content">
                                                    <span class="separator border-primary border-bottom my-3"></span>
                                                    <?php
                                                    require 'db.php';
                                                    $newsql = "SELECT * FROM articles WHERE id_user=$id";
                                                    $newquery = mysqli_query($sqli, $newsql); ?>
                                                    <?php if (mysqli_num_rows($newquery) > 0) : ?>
                                                        <?php while ($data = mysqli_fetch_assoc($newquery)) :
                                                            if ($data['state'] == 1) {
                                                                $status = 'Public';
                                                                $btnText = 'Rendre privé';
                                                            } elseif ($data['state'] == 2) {
                                                                $status = 'Privé';
                                                                $btnText = 'Rendre public';
                                                            } else {
                                                                $status = 'Caché';
                                                                $btnText = 'Rendre public';
                                                            }?>
                                                            <div class="row w-100 sub-item-container justify-content-center my-2 box-shadow-main bg-light px-2 py-3 rounded-3 border-secondary border-opacity-25 border">
                                                                <div class="row h-fit-content align-items-center">
                                                                    <div class="col-4">
                                                                        <p class="m-0"><span class="fw-bold">Title : </span><?= $data['title'] ?></p>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <p class="m-0"><span class="fw-bold">Date : </span><?= formateDate($data['created_at']) ?></p>
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <p class="m-0"><span class="fw-bold">Status : </span><?= $status ?></p>
                                                                    </div>
                                                                    <div class="col-1 offset-2">
                                                                        <div class="icon-triangle"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="content-shower h-fit-content">
                                                                    <span class="separator border-primary border-bottom my-3"></span>
                                                                    <div class="row">
                                                                        <div class="col-9">
                                                                            <h5>Titre :</h5>
                                                                            <p><?= $data['title'] ?></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-9">
                                                                            <h5>Contenu :</h5>
                                                                            <p><?= $data['content'] ?></p>
                                                                        </div>
                                                                        <div class="col-2">
                                                                            <form action="panel.php?action=write&update=1" method="POST">
                                                                                <button type="submit" name="id" value="<?= $data['id'] ?>" class="btn btn-warning text-light mx-1">Modifier</button>
                                                                            </form>
                                                                            <a href="checker.php?action=1&target=articles&state=<?= $data['state'] ?>&id=<?= $data['id'] ?>" class="btn btn-warning text-light mx-1"><?= $btnText ?></a>
                                                                            <a href="checker.php?action=2&target=articles&id=<?= $data['id'] ?>" class="btn btn-danger text-light mx-1">Supprimer</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        <?php endwhile; ?>
                                                    <?php else : ?>
                                                        <div class="row p-2 w-100 h-100">
                                                            <div class="col-12 d-flex flex-column align-items-center justify-content-center">
                                                                <p class="m-0 text-secondary">Cet utilisateur n'a écris aucun article pour le moment </p>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                <?php else :
                                    header('location: 404.php') ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'footer.php' ?>