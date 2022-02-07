<?php
include('storage.php');
include('auth.php');
include('userStorage.php');
include('teamStorage.php');
include('matchStorage.php');

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
$num_cuts = 0;
usort($matchesArr, "compare");

$last5matches = array_slice($matchesArr, $num_cuts, 5);
$_SESSION['num_cuts'] = $num_cuts;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELTE STADIUM</title>
    <link rel="icon" href="../res/logo.png" type="image/png">
    <link rel="stylesheet" href="../src/style/index.css">
    </link>
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
    <?php if (!$auth->is_authenticated()) : ?>
        <div id="intro">
            <div id="playerNdBtns">
                <div id="playerSvg">
                    <img src="../res/playerLandingPage.png" alt="playerPhoto" id="playerPhoto">
                </div>

                <div id="btnsDiv">
                    <a href="./regestration.php">
                        <button class="btnsClass">
                            JOIN NOW
                        </button>
                    </a>
                    <a href="./login.php">
                        <button class="btnsClass">
                            LOGIN
                        </button>

                    </a>
                </div>

            </div>
            <div id="stadiumIntro">
                <div>
                    <h1>ELTE STADIUM</h1>
                    <div>
                        <h2>Enjoy the football with your favourite teams.</h2>
                    </div>
                </div>
                <div id="websiteIntro">
                    <h3>
                        Here you will see the recent matches played, and different
                        Teams.
                        If you would like to follow your favourite teams, feel
                        free to sign up.
                    </h3>
                </div>

            </div>
        </div>
    <?php endif ?>



    <div class="sepratorDiv">Recent Matches</div>
    <hr>
    <div id="recentMatches">
        <?php foreach ($last5matches as $match) : ?>
            <div class="recentClass">
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
    <div style="width: 100vw;">
        <form novalidate>
            <button type="submit" class="btnsClass" id="refresh" style="margin-top:-20px;margin-left:20px;">SHOW MORE</button>
    </div>
    </form>

    <br><br>

    <div class="sepratorDiv">All Matches</div>
    <hr>
    <div id="allMatches">
        <?php foreach ($matchesArr as  $match) : ?>
            <div class="recentClass" style="height: 150px;">
                <span><?= $match['id'] ?></span>
                <br>
                <span><?= $match['home']['id'] . " VS " . $match['away']['id'] ?> </span>
                <br>
                <span><?= $match['home']['score'] . " -- " . $match['away']['score'] ?></span>
                <br>
                <span><?= $match['date'] ?></span>
                <br>
                <?php if ($auth->is_authenticated()) : ?>
                    <?php if ($auth->authenticated_user()['username'] === 'admin') : ?>
                        <a href="modify.php?mod=<?= $match['id'] ?>"><button class="btnsClass" style="height:20px;margin-top:15px;"> MODIFY </button> </a>
                    <?php endif ?>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>
    <div class="sepratorDiv">Teams</div>
    <hr>
    <div id="teamsList">
        <?php foreach ($teamsArr as $teamid => $teamArr) : ?>
            <div class="teamClass"><a href="teamDetails.php?id=<?= $teamid ?>"><?= $teamArr['name'] ?></a>

            </div>
        <?php endforeach ?>

    </div>
    <hr>
    <div id="contactUs">
        <div>
            <a href="https://www.linkedin.com/in/talal-quleh-5137311a4/" target="_blank">CONTACT US</a>
        </div>
    </div>
    <script src="recentMatches.js"></script>
</body>

</html>