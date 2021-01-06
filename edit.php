<?php
  require_once 'lib/pdo.php';
  require_once 'lib/util.php';
  require_once 'lib/ref.php';

  session_start();

  accountCheck();

  getProfile_idCheck();

  $row = profileSelect($pdo, $_GET['profile_id']);
  if ($row == false)  die('This profile does not exist');
  editPermissionCheck($row['user_id']);

  $posRowSet = positionsSelect($pdo, $_GET['profile_id']);
  $eduRowSet = educationSelect($pdo, $_GET['profile_id']);

  cancelCheck();

  if (isset($_POST['edit'])) {

    if (!profileValidate()) { return; }

    profileUpdate($pdo, $_SESSION['user_id'], $_GET['profile_id']);

    positionsUpdate($pdo, $_GET['profile_id']);
    educationsUpdate($pdo, $_GET['profile_id']);

    $_SESSION['success'] = 'Profile edition saved';
    header('Location: index.php');
    return;
  }

 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Zezhong Zhang's Profile Edit</title>
  </head>
  <body>
    <div class="container">
      <h1>Editing Profile for <?php echo htmlentities($_SESSION['name']) ?></h1>
      <?php flashMessage(); ?>
      <form class="profileForm" method="post">
        <label for="first_name" class="form-label">First Name: </label>
        <input class="form-control" id="first_name" type="text" name="first_name" value="<?php echo htmlentities($row['first_name']) ?>"><br>
        <label class="form-label" for="last_name">Last Name: </label>
        <input class="form-control" id="last_name" type="text" name="last_name" value="<?php echo htmlentities($row['last_name']) ?>"><br>
        <label class="form-label" for="email">Email: </label>
        <input class="form-control" id="email" type="text" name="email" value="<?php echo htmlentities($row['email']) ?>" size="20"><br>
        <label class="form-label" for="headline">Headline: </label><br>
        <input class="form-control" id="headline" type="text" name="headline" value="<?php echo htmlentities($row['headline']) ?>" size="40"><br>
        <label class="form-label" for="summary">Summary: </label><br>
        <textarea class="form-control" id="summary" type="text" name="summary" value="" rows="8" ><?php echo htmlentities($row['summary'], ENT_QUOTES, 'utf-8'); ?></textarea>
        <input type="submit" name="edit" value="Save">
        <input type="submit" name="cancel" value="Cancel">

        <div class="">Education: <button type="button" id="addEducationBtn">+</button></div>
        <div id="educationSpace">
          <input class="unshown" type="text" name="num_edu" value="<?php echo sizeof($eduRowSet) ?>">
          <input class="unshown" type="text" name="pre_num_edu" value="<?php echo sizeof($eduRowSet) ?>">
          <?php
            foreach ($eduRowSet as $eduRow) {
              // check educationSelect for index help
              echo "<div class=\"educationItem\" id=\"".$eduRow[2]."\">\n";
              echo "<p>Year:\n";
              echo "<input name=\"year_edu_".$eduRow[2]."\"  type=\"text\" size=\"10\" value=\"".htmlentities($eduRow[0])."\">\n";
              echo "<button type=\"button\" class=\"educationRemoveBtn\">-</button>\n</p>\n";
              echo "<input class=\"school\" name=\"school_edu_".$eduRow[2]."\" size=\"80\" value=\"".htmlentities($eduRow[1])."\"></input>\n</div>\n";
            }
           ?>
        </div>

        <div>Position: <button type="button" id="addPositionBtn">+</button></div>
        <div id="positionSpace">
          <input class="unshown" type="text" name="num_pos" value="<?php echo sizeof($posRowSet) ?>">
          <input class="unshown" type="text" name="pre_num_pos" value="<?php echo sizeof($posRowSet) ?>">
          <?php
            foreach ($posRowSet as $posRow) {
              echo "<div class=\"positionItem\" id=\"".$posRow['rank']."\">\n";
              echo "<p>Year:\n";
              echo "<input name=\"year_pos_".$posRow['rank']."\"  type=\"text\" size=\"10\" value=\"".htmlentities($posRow['year'])."\">\n";
              echo "<button type=\"button\" class=\"positionRemoveBtn\">-</button>\n</p>\n";
              echo "<textarea class=\"form-control\" name=\"detail_pos_".$posRow['rank']."\" rows=\"8\" value=\"\">".htmlentities($posRow['description'])."</textarea>\n</div>\n";
            }
           ?>
        </div>

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

    <script type="text/javascript" src="lib/js/edit.js"></script>

  </body>
</html>
