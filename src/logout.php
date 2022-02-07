<?php 

include('storage.php');
include('auth.php');
include('userStorage.php');
function redirect($page)
{
    header("Location: ${page}");
    exit();
}



session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);

$auth->logout();

redirect('index.php');

?>