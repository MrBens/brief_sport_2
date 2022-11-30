<?php require_once 'header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-6 login-col img-bg-login"></div>
        <div class="col-6 login-col d-flex flex-column justify-content-center align-items-center">
            <h1 class="mb-5">Se connecter :</h1>
            <form action="checker.php" method="post" class="d-flex flex-column">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-primary text-light border border-primary border-opacity-50">Username</span>
                    <input id="input-username" class="rounded-end border border-primary p-2 border-opacity-50" type="text" name='username' placeholder="username">
                </div>
                <div class="input-group my-3">
                    <span class="input-group-text bg-primary text-light border border-primary border-opacity-50">Password</span>
                    <input id="inputPassword" class="rounded-end border border-primary p-2 border-opacity-50" type="text" name='password' placeholder="password">
                </div>
                <input type="submit" class="btn btn-primary align-self-end mt-3" name="login" value="Connexion">
            </form>
            <?php if (isset($_GET['con']) && !empty($_GET['con'])) :
                $con = $_GET['con']; ?>
                <?php if ($con == 2) : ?>
                    <div class="mt-5">
                        <p class="text-warning">Identifiant ou mot de passe incorrect</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>


<?php require_once 'footer.php' ?>