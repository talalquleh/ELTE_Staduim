<?php

include('storage.php');
include('auth.php');
include('userStorage.php');

function redirect($page)
{
    header("Location: ${page}");
    exit();
}

// functions
function validate($post, &$data, &$errors)
{
    // username, password not empty
    if ($post['username'] == '') {
        $errors['username'] = "username needed";
    }
    if ($post['pass'] == '') {
        $errors['pass'] = "password needed";
    }
    // ...
    $data = $post;

    return count($errors) === 0;
}

// main
session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$data = [];
$errors = [];
if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
        $auth_user = $auth->authenticate($data['username'], $data['pass']);
        if (!$auth_user) {
            $errors['global'] = "Login error";
        } else {
            $auth->login($auth_user);
            redirect('index.php');
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="../res/logo.png" type="image/png">
    <link rel="stylesheet" href="../src/style/index.css">
</head>

<body>
    <div id="menuBar">
        <div>
            <a href="./index.php"> <button class="btnsClass">
                    HOME
                </button></a>
        </div>
        <?php if ($auth->is_authenticated()) : ?>
            <div>Hello <?= $auth->authenticated_user()['username'] ?> !</div>
            <div><a href="logout.php"> <button class="btnsClass">LOGOUT</button></a> </div>
        <?php endif ?>
    </div>
    <div id="loginSec">
        <div id="loginFrom">
        <form action="" method="POST" novalidate>
                <div>
                    <!-- <br> -->
                    <label for="">USERNAME :</label>
                    <br>
                    <input type="text" name="username" id="username" placeholder="Enter username" value="<?= $_POST['username'] ?? '' ?>" required>
                 
                </div>
                <?php if (isset($errors['username'])) : ?>
                    <span class=" errors"><?= $errors['username'] ?></span>
                <?php endif ?>
                <div>
                    <!-- <br> -->
                    <label for="">PASSWORD :</label>
                    <br>
                    <input type="password" name="pass" id="pass" placeholder="Enter password" value="<?= $_POST['pass'] ?? '' ?>" required>

                </div>
                <?php if (isset($errors['pass'])) : ?>
                        <span class=" errors"><?= $errors['pass'] ?></span>
                    <?php endif ?>
                <!-- <br> -->
                <div>
                    <?php if (isset($errors['global'])) : ?>
                        <span class=" errors"><?= $errors['global'] ?></span>
                    <?php endif ?>
                    <br>
                    <button type="submit" class="btnsClass">LOGIN</button>
                </div>
            </form>
        </div>

    </div>
    <hr>
    <div id="contactUs">
        <div>
            <a href="https://www.linkedin.com/in/talal-quleh-5137311a4/" target="_blank">CONTACT US</a>
        </div>
    </div>
</body>

</html>