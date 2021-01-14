<?php

  // String functions
  function startsWith($str1, $str2) {
    $len = strlen($str2);
    return (substr($str1, 0, $len) === $str2);
  }


  // Status/Message Check functions
  function setFailure($msg, $des) {
    $_SESSION['failure'] = $msg;
    header('Location:'.$des);
  }

  function flashMessage() {
    if (isset($_SESSION['success'])) {
      echo '<p class="success">'.$_SESSION['success']."</p>\n";
      unset($_SESSION['success']);
    } elseif (isset($_SESSION['failure'])) {
      echo '<p class="failure">'.$_SESSION['failure']."</p>\n";
      unset($_SESSION['failure']);
    }
  }

  function loginPortal() {
    if (!isset($_SESSION['name'])) {
      echo '<p><a href="login.php">Please log in</a></p>'."\n";
    } else {
      echo '<p><a href="logout.php">Log out</a></p>'."\n";
    }
  }

  function accountCheck() {
    if (!isset($_SESSION['name'])) {
      // check logging status
      die('Please log in');
    }
  }

  function cancelCheck() {
    if (isset($_POST['cancel'])) {
      header('Location: index.php');
      return;
    }
  }

  function getProfile_idCheck() {
    if (!isset($_GET['profile_id'])) {
      // check profile selection
      die('Profile_id is required.');
    }
  }

  // Input Validation functions
  function positionValidate() {
    for ($i=0; $i < 10; $i++) {
      if (!isset($_POST['year_pos_'.$i])) {continue;}
      if (!isset($_POST['detail_pos_'.$i])) {continue;}

      if (strlen($_POST['year_pos_'.$i])==0 || strlen($_POST['detail_pos_'.$i])==0) {
        return "All fields are required";
      }

      if (!is_numeric($_POST['year_pos_'.$i])) {
        return "Year must be numeric";
      }
    }
    return "pass";
  }

  function educationValidate() {
    for ($i=0; $i < 10; $i++) {
      if (!isset($_POST['year_edu_'.$i])) {continue;}
      if (!isset($_POST['school_edu_'.$i])) {continue;}

      if (strlen($_POST['year_edu_'.$i])==0 || strlen($_POST['school_edu_'.$i])==0) {
        return "All fields are required";
      }

      if (!is_numeric($_POST['year_edu_'.$i])) {
        return "Year must be numeric";
      }
    }
    return "pass";
  }

  function profileValidate() {
    if (!(isset($_POST['first_name']) && isset($_POST['last_name'])
        && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']))
        || (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1
        || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1)) {
      $_SESSION['failure'] = 'All fields are required';
      header('Location: '.$_SERVER['REQUEST_URI']);
      return false;
    }
    elseif (strpos($_POST['email'], '@') == false) {
      $_SESSION['failure'] = 'Email address must contain @';
      header('Location: '.$_SERVER['REQUEST_URI']);
      return false;
    }
    elseif (positionValidate() != "pass") {
      $_SESSION['failure'] = positionValidate();
      header('Location: '.$_SERVER['REQUEST_URI']);
      return false;
    }
    elseif (educationValidate() != "pass") {
      $_SESSION['failure'] = educationValidate();
      header('Location: '.$_SERVER['REQUEST_URI']);
      return false;
    }
    return true;
  }


  // Insertion functions
  function profileInsert($user_id, $pdo) {
    $stmt = $pdo->prepare('INSERT INTO profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute(array(
        ':uid' => $user_id,
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    );
  }

  function positionsInsert($profile_id, $pdo) {
    for ($i=0; $i < 10; $i++) {
      if (!isset($_POST['year_pos_'.$i])) {continue;}
      if (!isset($_POST['detail_pos_'.$i])) {continue;}

      positionInsert($pdo, $profile_id, $i);
    }
    return;
  }

  function educationsInsert($profile_id, $pdo) {
    for ($i=0; $i < 10; $i++) {
      if (!isset($_POST['year_edu_'.$i])) {continue;}
      if (!isset($_POST['school_edu_'.$i])) {continue;}

      educationInsert($pdo, $profile_id, $i);
    }
    return;
  }

  function positionInsert($pdo, $profile_id, $rank) {
    $stmt = $pdo->prepare('INSERT INTO position
            (profile_id, ranking, year, summary)
        VALUES ( :pid, :rank, :year, :desc)');
    $stmt->execute(array(
        ':pid' => $profile_id,
        ':rank' => $rank,
        ':year' => $_POST['year_pos_'.$rank],
        ':desc' => $_POST['detail_pos_'.$rank])
    );
  }

  function educationInsert($pdo, $profile_id, $rank) {
    $stmt = $pdo->prepare('SELECT institution_id FROM institution WHERE name=:sn');
    $stmt->execute(array(':sn' => $_POST['school_edu_'.$rank]));
    $schoolID = $stmt->fetch(PDO::FETCH_NUM)[0];

    if ($schoolID == false) {
      // insert a new institution
      $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:sn)');
      $stmt->execute(array(':sn' => $_POST['school_edu_'.$rank]));
      $schoolID = $stmt->fetch(PDO::FETCH_NUM);
    }

    $stmt = $pdo->prepare('INSERT INTO education
            (profile_id, institution_id, ranking, year)
        VALUES ( :pid, :istid, :rank, :year)');
    $stmt->execute(array(
        ':pid' => $profile_id,
        ':rank' => $rank,
        ':year' => $_POST['year_edu_'.$rank],
        ':istid' => (int)$schoolID)
    );
  }


  //Selection functions
  function profileSelect($pdo, $profile_id) {
    $stmt = $pdo->prepare('SELECT * FROM profile WHERE profile_id=:pi');
    $stmt->execute(array(':pi' => $profile_id));
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    return $profile;
  }

  function positionsSelect($pdo, $profile_id) {
    $stmt = $pdo->prepare('SELECT * FROM position WHERE profile_id=:pi ORDER BY ranking');
    $stmt->execute(array(':pi' => $profile_id));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  function educationSelect($pdo, $profile_id) {
    $stmt = $pdo->prepare('SELECT education.year, institution.name, education.ranking
                           FROM education
                           INNER JOIN institution
                           ON education.institution_id = institution.institution_id
                           WHERE profile_id=:pi ORDER BY ranking');
    $stmt->execute(array(':pi' => $profile_id));
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }


  // Updating functions
  function profileUpdate($pdo, $user_id, $profile_id) {
    $stmt = $pdo->prepare('UPDATE profile SET
      first_name = :fn, last_name = :ln,
       email = :em, headline = :he, summary = :su
      WHERE profile_id = :pid AND user_id = :uid');
    $stmt->execute(array(
        ':uid' => $user_id,
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':pid' => $profile_id)
    );
    return $stmt;
  }

  function positionsUpdate($pdo, $profile_id) {
    if ($_POST['num_pos'] > $_POST['pre_num_pos']) {
      // new more than before
      for ($i=0; $i < $_POST['num_pos']; $i++) {
        if ($i < $_POST['pre_num_pos']) {
          positionUpdate($pdo, $profile_id, $i);
        } else {
          positionInsert($pdo, $profile_id, $i);
        }
      }
    } else {
      // before more than new or equal
      for ($i=0; $i < $_POST['pre_num_pos']; $i++) {
        if ($i < $_POST['num_pos']) {
          positionUpdate($pdo, $profile_id, $i);
        } else {
          positionDelete($pdo, $profile_id, $i);
        }
      }
    }
  }

  function educationsUpdate($pdo, $profile_id) {
    for ($i=0; $i < $_POST['pre_num_edu']; $i++)
      educationDelete($pdo, $profile_id, $i);
    for ($i=0; $i < $_POST['num_edu']; $i++)
      educationInsert($pdo, $profile_id, $i);
  }

  function positionUpdate($pdo, $profile_id, $rank) {
    $stmt = $pdo->prepare('UPDATE Position SET
                           ranking = :rank, year = :year, summary = :desc
                           WHERE profile_id = :pfid AND ranking = :rank');
    $stmt->execute(array(
        ':pfid' => $profile_id,
        ':rank' => $rank,
        ':year' => $_POST['year_pos_'.$rank],
        ':desc' => $_POST['detail_pos_'.$rank])
    );
  }

  function educationUpdate($pdo, $profile_id, $rank) {
    // i think i cannot de such a update
    // cuz unique promary key is (profile_id, institution_id)
    // in which institution_id can be changed
  }


  // Deletion functions
  function positionDelete($pdo, $profile_id, $rank) {
    $stmt = $pdo->prepare('DELETE FROM position
                           WHERE profile_id = :pid AND ranking = :r');
    $stmt->execute(array(
      ':pid' => $profile_id,
      ':r' => $rank
    ));
  }

  function educationDelete($pdo, $profile_id, $rank) {
    $stmt = $pdo->prepare('DELETE FROM education
                           WHERE profile_id = :pid AND ranking = :r');
    $stmt->execute(array(
      ':pid' => $profile_id,
      ':r' => $rank
    ));
  }



  // index.php functions
  function setPages($sql, $db, $rowsPerPage) {
    $countStmt = $db->prepare($sql);
    $countStmt->execute();
    $totalRows = $countStmt->fetch()['COUNT(profile_id)'];
    $_SESSION['totalPages'] = (($totalRows % $rowsPerPage) == 0) ? (int)($totalRows / $rowsPerPage) : (int)($totalRows / $rowsPerPage)+1;
    $_SESSION['currPage'] = 1;
  }


  // edit.php function
  function editPermissionCheck($get_user_id) {
    if ($get_user_id != $_SESSION['user_id']) {
      // check relation between profile and user
      die('Your account has no access to this profile');
    }
  }
