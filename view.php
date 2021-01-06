<!--
  access with get method with profile_id
  works when not logged in
  -->

<?php
  require_once 'lib/pdo.php';
  require_once 'lib/ref.php';
  require_once 'lib/util.php';

  session_start();

  getProfile_idCheck();
  
  $row = profileSelect($pdo, $_GET['profile_id']);

  if ($row == false) die('This profile does not exist');

  $posRowSet = positionsSelect($pdo, $_GET['profile_id']);
  $eduRowSet = educationSelect($pdo, $_GET['profile_id']);
 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Zezhong Zhang's Profile Detail</title>
  <body>
    <div class="container">
      <h1>Profile information</h1>
      <?php
        echo '<p>First Name: '.htmlentities($row['first_name'])."</p>\n";
        echo '<p>Last Name: '.htmlentities($row['last_name'])."</p>\n";
        echo '<p>Email: '.htmlentities($row['email'])."</p>\n";
        echo '<p>Headline: <br>'.htmlentities($row['headline'])."</p>\n";
        echo '<p>Summary: <br>'.htmlentities($row['summary'])."</p>\n";

        echo "<ul>Education:\n";
        foreach ($eduRowSet as $eduRow) {
          echo "<li>\n";
          echo "<p>".htmlentities($eduRow[0])." : ".htmlentities($eduRow[1])."</p>\n";
          echo "</li>\n";
        }
        echo "</ul>\n";

        echo "<ul>Positions:\n";
        foreach ($posRowSet as $posRow) {
          echo "<li>\n";
          echo "<p>".htmlentities($posRow['year'])." : ".htmlentities($posRow['description'])."</p>\n";
          echo "</li>\n";
        }
        echo "</ul>\n";

       ?>
       <p><a href="index.php">Done<p>
    </div>
  </body>
</html>
