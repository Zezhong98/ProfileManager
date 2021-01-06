<?php
  require_once 'lib/pdo.php';
  require_once 'lib/util.php';
  require_once 'lib/ref.php';
  session_start();

  accountCheck();

  // POST responding
  cancelCheck();

  if (isset($_POST['add'])) {

    if (profileValidate() == false) { return; }

    profileInsert($_SESSION['user_id'], $pdo);

    $profile_id = $pdo->lastInsertId();

    positionsInsert($profile_id, $pdo);
    educationsInsert($profile_id, $pdo);

    $_SESSION['success'] = 'Profile added';
    header('Location: index.php');
    return;
  }
 ?>



<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Zezhong Zhang's Profile Add</title>
  </head>
  <body>
    <div class="container">
      <h1>Add Profile for <?php echo htmlentities($_SESSION['name']) ?></h1>
      <?php flashMessage(); ?>
      <form class="profileForm" method="post">
        <label for="first_name" class="form-label">First Name: </label>
        <input class="form-control" id="first_name" type="text" name="first_name" value=""><br>
        <label class="form-label" for="last_name">Last Name: </label>
        <input class="form-control" id="last_name" type="text" name="last_name" value=""><br>
        <label class="form-label" for="email">Email: </label>
        <input class="form-control" id="email" type="text" name="email" value="" size="20" placeholder="***@***"><br>
        <label class="form-label" for="headline">Headline: </label><br>
        <input class="form-control" id="headline" type="text" name="headline" value="" size="40"><br>
        <label class="form-label" for="summary">Summary: </label><br>
        <textarea class="form-control" id="summary" type="text" name="summary" value="" rows="8" ></textarea>
        <input type="submit" name="add" value="Add">
        <input type="submit" name="cancel" value="Cancel">

        <div class="">Education: <button type="button" id="addEducationBtn">+</button></div>
        <div id="educationSpace"></div>

        <div>Position: <button type="button" id="addPositionBtn">+</button></div>
        <div id="positionSpace"></div>

        <template id="educationTemplate">
          <div class="educationItem" id="{{educationID}}">
            <p>Year:
              <input name="year_edu_{{educationID}}"  type="text" size="10">
              <button type="button" class="educationRemoveBtn">-</button>
            </p>
            <p>School:
              <input class="school" type="text" name="school_edu_{{educationID}}" size="80"></input>
            </p>
          </div>
        </template>

        <template id="positionTemplate">
          <div class="positionItem" id="{{positionID}}">
            <p>Year:
              <input name="year_pos_{{positionID}}"  type="text" size="10">
              <button type="button" class="positionRemoveBtn">-</button>
            </p>
            <textarea class="form-control " name="detail_pos_{{positionID}}" rows="8"></textarea>
          </div>
        </template>

      </form>
    </div>

  <script type="text/javascript" src="lib/js/add.js"></script>

  </body>

</html>
