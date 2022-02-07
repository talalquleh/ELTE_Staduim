<?php
include('storage.php');
include('auth.php');
include('userStorage.php');
include('teamStorage.php');
include('matchStorage.php');
function redirect($page)
{
    header("Location: ${page}");
    exit();
}

session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);

$teams_storage = new TeamStorage();
$teamsArr = $teams_storage->findAll();
// print_r($teamsArr);

$matches_storage = new MatchStorage();
$matchesArr = $matches_storage->findAll();

function compare($a, $b)
{
    return ($a['date'] < $b['date']);
}
$matchId = null;
if (count($_GET) !== 1) {
    redirect('index.php');
} else {
    if (!isset($_GET['mod'])) {
        redirect('index.php');
    }
    if (!isset($matchesArr[$_GET['mod']])) {
        redirect('index.php');
    }
    $matchId = $_GET['mod'];
}
$matchToBeModified = null;





function validate($post, &$data, &$errors)
{
    // username, password not empty

    if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $post['date'])) {
        $errors['date'] = "invalid date format";
    }
    if (is_numeric($post['homeScore'])) {
        if ($post['homeScore'] < 0) {
            $errors['homeScore'] = "invalid homeScore";
        }
    } else {
        $errors['homeScore'] = "invalid homeScore";
    }
    if (is_numeric($post['awayScore'])) {
        if ($post['awayScore'] < 0) {
            $errors['awayScore'] = "invalid awayScore";
        }
    } else {
        $errors['awayScore'] = "invalid awayScore";
    }

    // ...
    $data = $post;

    return count($errors) === 0;
}
$hasBeenUpdated = null;
if (isset($matchId)) {
    //here we will edit the team matches
    $matchToBeModified = $matches_storage->findById($matchId);

    $data = [];
    $errors = [];
    if (count($_POST) > 0) {
        if (validate($_POST, $data, $errors)) {
            //here we will update matches data
            if (isset($matchToBeModified)) {
                $matchToBeModified = [
                    "id" => $matchToBeModified['id'],
                    "home" => [
                        "id" => strval($matchToBeModified['home']['id']),
                        "score" => strval($data['homeScore'])
                    ],
                    "away" => [
                        "id" => strval($matchToBeModified['away']['id']),
                        "score" => strval($data['awayScore'])
                    ],
                    "date" => $data['date']
                ];
                $matches_storage->update($matchId, $matchToBeModified);
                $hasBeenUpdated = true;
            }
        }
    }
}



// print_r($_GET);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Match</title>
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
    <div>

        <div class="sepratorDiv" style="margin-top: 50px;"><?= $matchId ?></div>
        <hr>
        <div id="modifyFormSec">
            <div id="modifyForm">
                <form action="" method="post" novalidate>
                    <label for="">Date:</label>
                    <input type="text" name="date" id="date" placeholder="Enter a new date" value="<?= (isset($data['date'])) ? $data['date'] : $matchToBeModified['date'] ?>">
                <?php if (isset($errors['date'])) : ?>
                    <span class="errors"><?= $errors['date'] ?></span>
                <?php endif ?>
                <br>
                <label for=""><?= $matchToBeModified['home']['id'] ?> score:</label>
                <input type="text" name="homeScore" id="homeScore" placeholder="Enter new score for home team" value="<?= (isset($data['homeScore'])) ? $data['homeScore'] : $matchToBeModified['home']['score'] ?>">
                <?php if (isset($errors['homeScore'])) : ?>
                    <span class="errors"><?= $errors['homeScore'] ?></span>
                <?php endif ?>
                <br>
                <label for=""><?= $matchToBeModified['away']['id'] ?> score:</label>
                <input type="text" name="awayScore" id="awayScore" placeholder="Enter new score for away team" value="<?= (isset($data['awayScore'])) ? $data['awayScore'] : $matchToBeModified['away']['score'] ?>">
                <?php if (isset($errors['awayScore'])) : ?>
                    <span class="errors"><?= $errors['awayScore'] ?></span>
                <?php endif ?>
                <br>
                <button type="submit" class="btnsClass" style="margin-top:30px;">MODIFY</button>
                <br>
                <?php if (isset($hasBeenUpdated)) : ?>
                    <span class="success">Match has been successfully modified</span>
                    <?php endif ?>
                </form>
                
            </div>
            
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