<?php
require_once 'db.php';
require_once 'functions.php';

if ($_SERVER['HTTP_REFERER'] == 'http://adminpanel/login.php' && isset($_POST['login'])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $sql = "SELECT * FROM users WHERE `username`= '$username' AND `password`='$password'";
        $query = mysqli_query($sqli, $sql);
        $result = mysqli_fetch_assoc($query);
        
        if ($result != null) {
            session_start();
            $_SESSION['user'] = $result['username'];
            $_SESSION['role'] = $result['role'];
            $_SESSION['user-id'] = $result['id'];

            if ($result['role'] == 'user') {
                header('location: index.php');
                exit;
            } else {
                header('location: panel.php');
                exit;
            }
        }else {
            header('location: login.php?con=2');
            exit;
        }
    }else{
        header('location: login.php?error=0');
        exit;
    }
}

if (strpos($_SERVER['HTTP_REFERER'],'panel.php') ) {
    if (strpos($_SERVER['HTTP_REFERER'], 'action=write') && isset($_POST['post-article'])) {
        if (!empty($_POST['title']) && !empty($_POST['message'])) {
            if (!getSessionState()) {
                session_start();
            }
            $userId = intval($_SESSION['user-id']);
            $title = mysqli_escape_string($sqli, $_POST['title']);
            $message = mysqli_escape_string($sqli, $_POST['message']);
            $slug = implode('-', explode(' ', $title));
            $state = $_POST['status'] != 'on' ? 1 : 2;

            if (isset($_FILES) && !empty($_FILES)) {
                $tmpName = $_FILES['file']['tmp_name'];
                $name = $_FILES['file']['name'];
                $size = $_FILES['file']['size'];
                $error = $_FILES['file']['error'];
                var_dump($_FILES);
                $sql = "SELECT username FROM users WHERE id=$userId";
                $query = mysqli_query($sqli, $sql);
                if (mysqli_num_rows($query) > 0) {
                    $result = mysqli_fetch_assoc($query);
                    $username = $result['username'];
                    $dir = './upload/' . $username;
                    if (!file_exists($dir)) {
                        mkdir($dir);
                    }
                    $root = $dir . '/' . $name;
                    move_uploaded_file($tmpName, $root);
                }
                if (strpos($_SERVER['HTTP_REFERER'], 'update=1') && isset($_POST['post-article']) && isset($_GET['id']) && !empty($_GET['id'])) {
                    $id = $_GET['id'];
                    $sql = "UPDATE articles SET title='$title', content='$message', slug='$slug', state=$state, img='$root' WHERE id=$id";
                }else{
                    $sql = "INSERT INTO articles (title, content, slug, id_user, state, img) VALUES ('$title', '$message', '$slug', $userId, $state, '$root')";
                }
            }
            elseif (strpos($_SERVER['HTTP_REFERER'], 'update=1') && isset($_POST['post-article']) && isset($_GET['id']) && !empty($_GET['id'])){
                $id = $_GET['id'];
                $sql = "UPDATE articles SET title='$title', content='$message', slug='$slug', state=$state WHERE id=$id";
            }else {
                $sql = "INSERT INTO articles (title, content, slug, id_user, state) VALUES ('$title', '$message', '$slug', $userId, $state)";
            }
        }
        if (mysqli_query($sqli, $sql)) {
            header('location: panel.php?action=write&success=1');
        } else {
            header('location: panel.php?action=write&success=2');
        }
    } else {
        header('location: panel.php?action=write&success=3');
    }
    
    if (isset($_GET['action']) && isset($_GET['id']) && !empty($_GET['action']) && !empty($_GET['id'])) {
        $action = $_GET['action'];
        $id = $_GET['id'];
        if (isset($_GET['target']) && !empty($_GET['target'])){
            $table = $_GET['target'];
            if ($action == 1) {
                if (isset($_GET['state']) && !empty($_GET['state'])) {
                    $state = $_GET['state'];
                    $neededState = $state == 1 ? 2 : 1;
                    if ($table == 'users') {
                        $sql = "SELECT * FROM articles WHERE id_user = $id";
                        $query = mysqli_query($sqli, $sql);
                        if (mysqli_num_rows($query) > 0){
                            $sql = "UPDATE $table INNER JOIN articles ON articles.id_user = users.id SET users.state = $neededState, articles.state = users.state WHERE users.id = $id";
                        }else {
                            $sql = "UPDATE $table SET users.state = $neededState WHERE users.id = $id";
                        }
                    }else {
                        $sql = "UPDATE $table SET `state`='$neededState' WHERE `id`=$id";
                    }
                }else {
                    header('location:'. $_SERVER['HTTP_REFERER'].'?error=1');
                }
            }elseif ($action == 2) {
                if ($table == 'users') {
                    $sql = "SELECT * FROM articles WHERE id_user = $id";
                    $query = mysqli_query($sqli, $sql);
                    if (mysqli_num_rows($query) > 0) {
                        $sql = "UPDATE $table INNER JOIN articles ON articles.id_user = users.id SET users.state = 3, articles.state = users.state WHERE users.id = $id";
                    } else {
                        $sql = "UPDATE $table SET users.state = 3 WHERE users.id = $id";
                    }
                }else{
                    $sql = "UPDATE $table SET `state`= 3 WHERE `id`= $id";
                }
            }
        }
        if (mysqli_query($sqli, $sql)) {
            header('location:'. $_SERVER['HTTP_REFERER'] );
        }
        else {
            echo 'error';
        }
    } else {
        echo 'error2';
    }
} else {
    echo 'error3';
}

if (isset($_GET['disconnect']) && !empty($_GET['disconnect'])) {
    $disconnect = $_GET['disconnect'];
    if ($disconnect == 1) {
        disconnect();
        header('location: index.php');
        exit;
    }
}