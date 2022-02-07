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
    // username, password, fullname are not empty
    // ...
    if ($post['username'] == '') {
        $errors['username'] = "username can't be empty";
    }
    if ($post['email'] == '') {
        $errors['email'] = "email address can't be empty";
    }
    if ($post['password'] == '') {
        $errors['password'] = "password  can't be empty";
    }
    if ($post['passConfirm'] == '') {
        $errors['passConfirm'] = "password confirmation can't be empty";
    }
    if ($post['passConfirm'] != $post['password']) {
        $errors['passMatching'] = "Password and password confirmation are not matching.";
    }



    $data = $post;

    return count($errors) === 0;
}

// main
$user_storage = new UserStorage();
// print_r($user_storage->findAll());
$auth = new Auth($user_storage);


$errors = [];
$data = [];

if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
        if ($auth->user_exists($data['username'])) {
            $errors['global'] = "User already exists";
        } else {
            $auth->register($data);
            redirect('login.php');
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
    <title>Regestration</title>
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

    <div id="registSec">
        <div id="registFrom">
        <form action="" method="POST" novalidate>
                <div>
                    <label for="">USERNAME :</label>
                    <br>
                    <input type="text" name="username" id="username" placeholder="Enter username" required value="<?= $_POST['username'] ?? '' ?>">
                </div>
                <?php if (isset($errors['username'])) : ?>
                    <span class="errors"><?= $errors['username'] ?></span>
                <?php endif ?>
                <!-- <br> -->
                <div>
                    <label for="">EMAIL :</label>
                    <br>
                    <input type="email" name="email" id="email" placeholder="Enter email address" value="<?= $_POST['email'] ?? '' ?>" required>
                </div>
                <?php if (isset($errors['email'])) : ?>
                    <span class=" errors"><?= $errors['email'] ?></span>
                <?php endif ?>
                <div>
                    <label for="">PASSWORD :</label>
                    <br>
                    <input type="password" name="password" id="password" placeholder="Enter password" value="<?= $_POST['password'] ?? '' ?>" required>
                </div>
                <?php if (isset($errors['password'])) : ?>
                    <span class=" errors"><?= $errors['password'] ?></span>
                <?php endif ?>
                <div>
                    <label for="">CONFIRM PASSWORD :</label>
                    <br>
                    <input type="password" name="passConfirm" id="" placeholder="Confirm password" value="<?= $_POST['passConfirm'] ?? '' ?>" required>
                </div>
                <?php if (isset($errors['passConfirm'])) : ?>
                    <span class=" errors"><?= $errors['passConfirm'] ?></span>
                <?php endif ?>
                <?php if (isset($errors['passMatching'])) : ?>
                    <span class=" errors"><?= $errors['passMatching'] ?></span>
                <?php endif ?>
                <!-- <br> -->
                <div>
                    <?php if (isset($errors['global'])) : ?>
                        <span class=" errors"><?= $errors['global'] ?></span>
                    <?php endif ?>
                    <br>
                    <button type="submit" class="btnsClass">REGISTER</button>
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