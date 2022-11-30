<?php

function formateDate($date){
    $date = explode('-', $date);
    $daytemp = explode(' ', $date[2]);
    $temp = [$daytemp[0], $date[1], $date[0]];
    $date = implode('/', $temp);
    return $date;
}

function getAllArticlesById($id){
    require 'db.php';
    $sql = "SELECT * FROM articles WHERE id_user = $id";
    $query = mysqli_query($sqli, $sql);
    if ($query) {
        $result = mysqli_fetch_assoc($query);
        return $result;
    }
}

function countArticles($id){
    require 'db.php';
    $countSQL = "SELECT COUNT(*) FROM articles WHERE id_user=$id";
    $countQuery = mysqli_query($sqli, $countSQL);
    $countResult = mysqli_fetch_array($countQuery);
    echo $countResult[0];
}

function getSessionState(){
    $sessionState = session_status();
    if ($sessionState == PHP_SESSION_ACTIVE) {
        return true;
    }else{
        return false;
    }
}

function disconnect(){
    if (!getSessionState()) {
        session_start();
    }
    session_destroy();
}

function checkConnection(){
    if (!getSessionState()) {
        session_start();
    }
    if (isset($_SESSION['user']) && !empty($_SESSION['user'])){
        return true;
    }else {
        return false;
    }
}

function drawContentHeader($action, $status, $username = '', $title = '', $date =''){
    if ($action == 'myarticles') {
        return $contentHeader = <<<HTML
            <div class="col-4">
                <p class="m-0"><span class="fw-bold">Titre : </span>$title</p>
            </div>
            <div class="col-3">
                <p class="m-0"><span class="fw-bold">Date : </span>$date</p>
            </div>
            <div class="col-2">
                <p class="m-0"><span class="fw-bold">Status : </span>$status</p>
            </div>
            <div class="col-1 offset-2">
                <div class="icon-triangle"></div>
            </div>
        HTML;
    } elseif ($action == 'modarticles') {
        return $contentHeader = <<<HTML
            <div class="col-3">
                <p class="m-0"><span class="fw-bold">User : </span>$username</p>
            </div>
            <div class="col-4">
                <p class="m-0"><span class="fw-bold">Titre : </span>$title</p>
            </div>
            <div class="col-2">
                <p class="m-0"><span class="fw-bold">Date : </span>$date</p>
            </div>
            <div class="col-2">
                <p class="m-0"><span class="fw-bold">Status : </span>$status</p>
            </div>
            <div class="col-1">
                <div class="icon-triangle"></div>
            </div>
        HTML;
    }
}

function drawSubContent($action, $id, $state = '', $btnText = '', $content = '', $title = '')
{
    return $subContent = <<<HTML
        <div class="row">
            <div class="col-9">
                <h5>Titre :</h5>
                <p>$title</p>
            </div>
        </div>
        <div class="row">
            <div class="col-9">
                <h5>Contenu :</h5>
                <p>$content</p>
            </div>
            <div class="col-2">
                <form action="panel.php?action=write&update=1" method="POST">
                    <button type="submit" name="id" value="$id" class="btn btn-warning text-light mx-1">Modifier</button>
                </form>
                <a href="checker.php?action=1&target=articles&state=$state&id=$id" class="btn btn-warning text-light mx-1">$btnText</a>
                <a href="checker.php?action=2&target=articles&id=$id" class="btn btn-danger text-light mx-1">Supprimer</a>
            </div>
        </div>
    HTML;
}

function drawContent($action, $result){
    if (isset($result['id']) && !empty($result['id'])) $id = $result['id'];
    if (isset($result['title']) && !empty($result['title'])) $title = $result['title'];
    if (isset($result['state']) && !empty($result['state'])) $state = $result['state'];
    if (isset($result['content']) && !empty($result['content'])) $content = $result['content'];
    if (isset($result['username']) && !empty($result['username'])) $username = $result['username'];
    if (isset($result['created_at']) && !empty($result['created_at'])) $date = formateDate($result['created_at']);

    if ($result['state'] == 1) {
        $status = 'Public';
        $btnText = 'Rendre privé';
    } elseif ($result['state'] == 2) {
        $status = 'Privé';
        $btnText = 'Rendre public';
    } else {
        $status = 'Caché';
        $btnText = 'Rendre public';
    }

    if($action == 'myarticles'){
        $contentHeader = drawContentHeader($action, $status, '', $title, $date);
    }else {
        $contentHeader = drawContentHeader($action, $status, $username, $title, $date);
    }
    $subContent = drawSubContent($action, $id, $state, $btnText, $content, $title);
    
    echo <<<HTML
        <div class="row w-100 item-container justify-content-center my-2 box-shadow-main bg-light px-2 py-3 rounded-3 border-secondary border-opacity-25 border">
            <div class="row h-fit-content align-items-center">
                $contentHeader
            </div>
            <div class="content-shower h-fit-content">
                <span class="separator border-primary border-bottom my-3"></span>
                $subContent
            </div>
        </div>
    HTML;
}

