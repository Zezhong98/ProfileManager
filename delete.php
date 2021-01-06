<?php
  require_once 'lib/pdo.php';
  require_once 'lib/ref.php';
  require_once 'lib/util.php';
  session_start();
  unset($_SESSION['profile_id']);

  accountCheck();

  if (!isset($_GET['profile_id'])) {
    // check profile selection
    die('Profile_id is required.');
  }

  $stmt = $pdo->prepare('SELECT * FROM profile WHERE profile_id=:pi');
  $stmt->execute(array(':pi' => $_GET['profile_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($row == false) {
    // check profile existion
    die('This profile does not exist');
  }

  if ($row['user_id'] != $_SESSION['user_id']) {
    // check relation between profile and user
    die('Your account has no access to this profile');
  }

  $_SESSION['profile_id'] = $_GET['profile_id'];

  if (isset($_POST['cancel'])) {
    header('Location: index.php');
    unset($_SESSION['profile_id']);
    return;
  } elseif (isset($_POST['delete'])) {
    // all field are validated
    $stmt = $pdo->prepare('DELETE FROM profile WHERE profile_id=:pid');
    $stmt->execute(array(
        ':pid' => $_SESSION['profile_id'])
    );

    $_SESSION['success'] = 'Profile deleted';
    header('Location: index.php');
    unset($_SESSION['profile_id']);
    return;
  }

 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Zezhong Zhang's Profile Delete</title>
  <body>
    <div class="container">
      <h1>Deleteing Profile</h1>

      <form class="" method="post">
        <p>First Name: <?php echo htmlentities($row['first_name'], ENT_QUOTES, 'utf-8'); ?></p>
        <p>Last Name: <?php echo htmlentities($row['last_name'], ENT_QUOTES, 'utf-8'); ?></p>
        <input type="submit" name="delete" value="Delete">
        <input type="submit" name="cancel" value="Cancel">
      </form>
    </div>

  </body>
</html>
