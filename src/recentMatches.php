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
usort($matchesArr, "compare");

if(isset($_SESSION['num_cuts'])){
    $_SESSION['num_cuts']++;
    $start= ($_SESSION['num_cuts']*5);
    $extra_five = array_slice($matchesArr,$start , 5);
}


?>

<?php foreach ($extra_five as $match) : ?>
    <div class="recentClass" >
        <span><?= $match['id'] ?></span>
        <br>
        <span><?= $match['home']['id'] . " VS " . $match['away']['id'] ?> </span>
        <br>
        <span><?= $match['home']['score'] . " -- " . $match['away']['score'] ?></span>
        <br>
        <span><?= $match['date'] ?></span>
    </div>
<?php endforeach ?>