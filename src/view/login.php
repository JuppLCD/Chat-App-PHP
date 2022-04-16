<?php
session_start();
if (isset($_SESSION['unique_id']) || !empty($_SESSION['unique_id'])) {
  header("location: users.php");
}
include_once dirname(__FILE__) . "./../components/Header.php";

$page = 'login';
?>

<section class="form login">
  <header>Realtime Chat App</header>
  <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
    <div class="error-text"></div>
    <div class="field input">
      <label>Email Address</label>
      <input type="text" name="email" placeholder="Enter your email" required>
    </div>
    <div class="field input">
      <label>Password</label>
      <input type="password" name="password" placeholder="Enter your password" autocomplete="on" required>
      <i class="fas fa-eye"></i>
    </div>
    <div class="field button">
      <input type="submit" name="submit" value="Continue to Chat">
    </div>
  </form>
  <div class="link">Not yet signed up? <a href="./index.php">Signup now</a></div>
</section>

<?php include_once dirname(__FILE__) . "./../components/Footer.php"; ?>