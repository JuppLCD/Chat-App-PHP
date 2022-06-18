<?php
session_start();
if (isset($_SESSION['unique_id'])) {
  header("location: users");
}
include_once dirname(__FILE__) . "./../components/Header.php";

$page = 'signup';
?>

<section class="form signup">
  <header>Realtime Chat App</header>
  <form method="POST" enctype="multipart/form-data" autocomplete="off">
    <div class="error-text"></div>
    <div class="name-details">
      <div class="field input">
        <label>First Name</label>
        <input type="text" name="fname" placeholder="First name" require>
      </div>
      <div class="field input">
        <label>Last Name</label>
        <input type="text" name="lname" placeholder="Last name" require>
      </div>
    </div>
    <div class="field input">
      <label>Email Address</label>
      <input type="text" name="email" placeholder="Enter your email" require>
    </div>
    <div class="field input">
      <label>Password</label>
      <input type="password" name="password" placeholder="Enter new password" autocomplete="on" require>
      <i class="fas fa-eye"></i>
    </div>
    <div class="field image">
      <label>Select Image</label>
      <input type="file" id="inputFile" name="image" accept="image/x-png,image/gif,image/jpeg,image/jpg" require>
    </div>
    <div class="field button">
      <input type="submit" name="submit" value="Continue to Chat">
    </div>
  </form>
  <div class="link">Already signed up? <a href="./login">Login now</a></div>
</section>

<?php include_once dirname(__FILE__) . "./../components/Footer.php"; ?>