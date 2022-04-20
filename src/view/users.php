<?php
require_once __DIR__ . './../../vendor/autoload.php';

session_start();
if (!isset($_SESSION['unique_id'])) {
  header("location: login.php");
}
$unique_id = $_SESSION['unique_id'];

use Php\class\Auth;

// No me funciona el autoload
require_once __DIR__ . './../../php/class/Auth.class.php';


$_auth = new Auth;

$userData = $_auth->getUserBySession($unique_id);

$userInfo = [];
if (count($userData) > 0) {
  $userInfo = $userData[0];
} else {
  session_unset();
  session_destroy();
  header("location: login.php");
}

include_once dirname(__FILE__) . "./../components/Header.php";

$page = 'users';
?>

<section class="users">
  <header>
    <div class="content">
      <img src="./../../php/images/<?php echo $userInfo['img']; ?>" alt="perfil">
      <div class="details">
        <span><?php echo $userInfo['fname'] . " " . $userInfo['lname'] ?></span>
        <p><?php echo $userInfo['status']; ?></p>
      </div>
    </div>
    <a href="./../../php/logout.php?logout_id=<?php echo $userInfo['unique_id']; ?>" class="logout">Logout</a>
  </header>
  <div class="search">
    <span class="text">Select an user to start chat</span>
    <input type="text" placeholder="Enter name to search...">
    <button><i class="fas fa-search"></i></button>
  </div>
  <div class="users-list" data-unique_id='<?php echo $unique_id; ?>'>

  </div>
</section>

<?php include_once dirname(__FILE__) . "./../components/Footer.php"; ?>