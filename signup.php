<?php
  require_once 'lib/pdo.php';
  require_once 'lib/util.php';
  require_once 'lib/ref.php';

  session_start();

  cancelCheck();

  if (isset($_POST['submit'])){
    var_dump($_POST);
    if (!(isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['name'])
        && strlen($_POST['email'])>0 && strlen($_POST['pass'])>0 && strlen($_POST['name'])>0)) {
          // fillness checking
          setFailure('all fields must be filled', 'signup.php');
          return;
        }
    else {
      // validation checking
      if (strpos($_POST['email'], '@') == false) {
        setFailure('invalid email address', 'signup.php');
        return;
      }

      if (strlen($_POST['pass']) < 6) {
        setFailure('password must be longer than 6 letters', 'signup.php');
        return;
      }

      // valid input data, create user
      $encoded = hash('md5', 'XyZzy12*_'.$_POST['pass']);
      $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:nm, :em, :pw)');
      $stmt->execute(array('nm' => $_POST['name'], ':em' => $_POST['email'], ':pw' => $encoded));

      $stmt = $pdo->prepare('SELECT user_id FROM users WHERE name=:nm AND email=:em');
      $stmt->execute(array(':nm' => $_POST['name'], 'em' => $_POST['email']));

      $_SESSION['name'] = $_POST['name'];
      $_SESSION['user_id'] = ($stmt->fetch())[0];
      header('Location: index.php');
      return;
    }
  }

 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Sign up page</title>
  </head>
  <body>
    <div class="container">
      <h1>Sign up your own account!</h1>
      <?php flashMessage(); ?>
      <form class="" method="post">
        <label for="name">Name</label>
        <input type="text" name="name" placeholder="name"><br>
        <label for="email">E-mail</label>
        <input type="text" name="email" placeholder="account@address"><br>
        <label for="pass">Password</label>
        <input type="password" name="pass"><br>
        <input type="submit" name="submit" value="Sign up">
        <input type="submit" name="cancel" value="Cancel">
      </form>
    </div>
  </body>
</html>
