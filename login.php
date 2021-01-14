<?php
  require_once 'lib/pdo.php';
  require_once 'lib/util.php';
  require_once 'lib/ref.php';

  session_start();

  cancelCheck();

  if (isset($_POST['email']) && isset($_POST['pass'])) {
    $check = hash('md5', 'XyZzy12*_'.$_POST['pass']);
    $stmt = $pdo->prepare(
      'SELECT user_id, name FROM '.$dbname.'.users WHERE email=:em AND password=:pw');
    $stmt->execute(array(':em' => $_POST['email'], ':pw' => $check));
    $row = $stmt->fetch();
    if ($row != false) {
      // login success
      $_SESSION['name'] = $row['name'];
      $_SESSION['user_id'] = $row['user_id'];
      header('Location: index.php');
      return;
    } else {
      // login fail
      setFailure('incorrect password', 'login.php');
      return;
    }
  }
 ?>


<!DOCTYPE html>
<html>
<head>
<title>Zezhong Zhang's Login Page</title>
</head>
<body>
  <div class="container">
    <h1>Please Log In</h1>
    <?php flashMessage(); ?>
    <form method="POST" action="login.php">
      <label for="email">Email</label>
      <input type="text" name="email" id="email"><br/>
      <label for="pw">Password</label>
      <input type="password" name="pass" id="pw"><br/>
      <input type="submit" onclick="return loginValidate('#email','#pw');" value="Log In">
      <input type="submit" name="cancel" value="Cancel">
      <p><a href="signup.php">Sign up</a></p>
    </form>
    <p>
      For a password hint, view source and find an account and password hint
      in the HTML comments.
      <!-- Hint:
      The account is umsi@umich.edu
      The password is the three character name of the
      programming language used in this class (all lower case)
      followed by 123. -->
    </p>

  </div>
</body>
