<?php
session_start();
if (!isset($_SESSION['unique_id'])) {
  header("location: login.php");
}
if (!isset($_GET['user_id']) && !empty($_GET['user_id'])) {
  header("location: users.php");
}

use Class\Auth;

require_once dirname(__FILE__) . './../../php/class/Auth.class.php';
$_auth = new Auth;

$user_id = $_GET['user_id'] ?? '';

$user = $_auth->getUserBySession($user_id);

if (count($user) > 0) {
  $user = $user[0];
} else {
  header("location: users.php");
}

include_once dirname(__FILE__) . "./../components/Header.php";

$page = 'chat';
?>

<section class="chat-area">
  <header>
    <a href="./users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
    <img src="./../../php/images/<?php echo $user['img']; ?>" alt="">
    <div class="details">
      <span><?php echo $user['fname'] . " " . $user['lname'] ?></span>
      <p><?php echo $user['status']; ?></p>
    </div>
  </header>
  <div class="chat-box">

  </div>
  <form class="typing-area" data-outgoing_id='<?php echo $_SESSION['unique_id']; ?>'>
    <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
    <input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off">
    <button class="active"><i class="fab fa-telegram-plane"></i></button>
  </form>
</section>

<?php include_once dirname(__FILE__) . "./../components/Footer.php"; ?>