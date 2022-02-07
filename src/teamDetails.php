<?php


include('storage.php');
include('auth.php');
include('userStorage.php');
include("teamStorage.php");
include('matchStorage.php');
include('commentStorage.php');

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

$matches_storage = new MatchStorage();
$matchesArr = $matches_storage->findAll();

$comment_storage = new CommentStorage();
$commentsArr = $comment_storage->findAll();

$teamId = null;
$commDelId = null;
if (count($_GET) >= 1) {
    if (count($_GET) == 1) {
        if (!isset($_GET['id'])) {
            redirect('index.php');
        }
        if (!isset($teamsArr[$_GET['id']])) {
            redirect('index.php');
        }
        $teamId = $_GET['id'];
    }
    if (count($_GET) == 2) {
        if (!isset($_GET['id'])) {
            redirect('index.php');
        }
        if (!isset($teamsArr[$_GET['id']])) {
            redirect('index.php');
        }
        $teamId = $_GET['id'];
        if (!isset($_GET['del'])) {
            redirect('index.php');
        }
        if (!isset($commentsArr[$_GET['del']])) {
            redirect('index.php');
        }

        $commDelId = $_GET['del'];
        print_r($commDelId);
    }
} elseif (count($_GET) < 1) {
    redirect('index.php');
}


$teamMatches = [];
if (isset($teamId)) {
    // print_r($teamId);
    $teamMatches = array_filter($matchesArr, function ($match) use ($teamId) {
        return $match['home']['id'] === $teamId || $match['away']['id'] === $teamId;
    });
}

if (isset($commDelId)) {
    $comment_storage->delete($commDelId);
    redirect("teamDetails.php?id=${teamId}");
}

function hasWon($team, $opp)
{
    return $team['score'] > $opp['score'];
}
function hasLost($team, $opp)
{
    return $team['score'] < $opp['score'];
}

$todayDate = date('Y-m-d');
function notHappenToday($match)
{
    global $todayDate;
    return strtotime($match['date']) >= strtotime($todayDate);
}
$commentError = null;
$urComment = null;
if (isset($_POST['comment'])) {
    if ($_POST['comment'] == '') {
        $commentError = "comment can't be empty";
    } else {
        $urComment = $_POST['comment'];
    }
}
if (isset($urComment)) {
    //updating the comment with author and text
    $user = $auth->authenticated_user()['username'];
    $comment_storage->add(["author" => "${user}", "text" => "${urComment}", "teamid" => "${teamId}"]);
    redirect("teamDetails.php?id=${teamId}");
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Details</title>
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
    <div class="recentClass" style="padding:20px;height: 100px;position: fixed; right: 0;top: 10%; width: 10%;border-top: 4px solid black;height: fit-content;">
        <span><?= $teamsArr[$teamId]['name'] ?></span>
        <br>
        <span><?= $teamsArr[$teamId]['city'] ?></span>
    </div>
    <div class="sepratorDiv" style="margin-top:50px;">
        Matches
    </div>
    <hr>
    <div id="teamMatches">
        <?php foreach ($teamMatches as $match) : ?>
            <?php if (notHappenToday($match)) : ?>
                <div class="recentClass">
                <?php else : ?>
                    <?php
                    $homeTeam = ($match['home']['id'] === $teamId) ? $match['home'] : $match['away'];
                    $oppTeam = ($match['home']['id'] !== $teamId) ? $match['home'] : $match['away']
                    ?>
                    <?php if (hasWon($homeTeam, $oppTeam)) : ?>
                        <div class="recentClass winningBorder">
                        <?php elseif (hasLost($homeTeam, $oppTeam)) : ?>
                            <div class="recentClass losingBorder">
                            <?php else : ?>
                                <div class="recentClass drawBorder">
                                <?php endif ?>
                            <?php endif ?>
                            <span><?= $match['id'] ?></span>
                            <br>
                            <span><?= $match['home']['id'] . " VS " . $match['away']['id'] ?> </span>
                            <br>
                            <span><?= $match['home']['score'] . " -- " . $match['away']['score'] ?></span>
                            <br>
                            <span><?= $match['date'] ?></span>
                                </div>

                            <?php endforeach ?>

                            </div>
                            <div class="sepratorDiv" style="margin-top:50px;">
                                Comments
                            </div>
                            <hr>
                            <div id="commentsDiv">
                                <form action="" method="post">
                                    <div id="urCommentDiv" class="recentClass">
                                        <div>
                                            <?php if ($auth->is_authenticated()) : ?>
                                                <textarea name="comment" id="comment" cols="30" rows="10" placeholder="write your comment here" style="border-radius:10px; color:black;"></textarea>
                                            <?php else : ?>
                                                <textarea name="" id="" cols="30" rows="10" style="border-radius:10px; color:black;" placeholder="you need to login to write a comment" disabled></textarea>
                                            <?php endif ?>

                                            <?php if (isset($commentError)) : ?>
                                                <span class="errors"><?= $commentError ?></span>
                                            <?php endif ?>
                                        </div>
                                        <div>
                                            <input type="submit" value="PUBLISH" class="btnsClass">
                                        </div>
                                </form>
                            </div>
                            <?php foreach ($commentsArr as $commentid => $comment) : ?>
                                <?php if ($comment['teamid'] == $teamId) : ?>
                                    <div class="recentClass">
                                        <div style="outline: 3px solid #2d39a3; outline-offset:.25em; width: 70px;"><?= $comment['author'] ?></div>
                                        <div style="padding:20px;"><?= $comment['text'] ?></div>
                                        <form action="" method="post">
                                            <?php if ($auth->is_authenticated()) : ?>
                                                <?php if ($auth->authenticated_user()['username'] === 'admin') : ?>
                                                    <button type="submit" class="btnsClass" style="height:20px;margin-left:70%;"><a style="height: 20px; width:148px" href="teamDetails.php?id=<?= $teamId ?>&del=<?= $commentid ?>">DELETE</a></button>
                                                <?php endif ?>
                                            <?php endif ?>
                                        </form>
                                    </div>

                                <?php endif ?>
                            <?php endforeach ?>

                            <div class="recentClass"></div>
                            <div class="recentClass"></div>
                            <div class="recentClass"></div>

                        </div>

                        <hr>
                        <div id="contactUs">
                            <div>
                                <a href="https://www.linkedin.com/in/talal-quleh-5137311a4/" target="_blank">CONTACT US</a>
                            </div>
                        </div>

</body>

</html>