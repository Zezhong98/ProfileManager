<?php
  require_once 'lib/pdo.php';
  require_once 'lib/ref.php';
  require_once 'lib/util.php';
  session_start();
  $ROWSPERPAGE = 5;
  $PAGESETTING = false;

  // ***** POST REQUEST DEALING *****
  if (isset($_POST['search'])) {
    // search button clicked
    if (!isset($_POST['searchType'])) {
      $_SESSION['failure'] = 'please choose search area between name and content';
      header("Location: index.php");
      return;
    }
    if (!isset($_POST['searchKey']) || strlen($_POST['searchKey']) < 1) {
      $_SESSION['failure'] = 'enter key words for searching';
      header("Location: index.php");
      return;
    }
    // search with get
    unset($_SESSION['currPage']);
    unset($_SESSION['totalPage']);
    header('Location: index.php?searchKey='.$_POST['searchKey'].'&searchType='.$_POST['searchType']);
    return;
  }

  if (isset($_POST['showAll'])) {
    unset($_SESSION['currPage']);
    unset($_SESSION['totalPage']);
    header('Location: index.php');
    return;
  }

  if (isset($_POST['back'])) {
    // back clicked
    if ($_SESSION['currPage'] > 1) {
      // if not first page, move back and reread
      $_SESSION['currPage'] -= 1;
    }
    header('Location: '.$_SERVER['REQUEST_URI']);
    return;
  }

  if (isset($_POST['next'])) {
    // next clicked
    if ($_SESSION['currPage'] < $_SESSION['totalPages']) {
      // if not first page, move back and reread
      $_SESSION['currPage'] += 1;
    }
    header('Location: '.$_SERVER['REQUEST_URI']);
    return;
  }
  // ***** END POST REQUEST DEALING *****

  if (isset($_GET['searchKey']) && isset($_GET['searchType'])) {
    // search results
    if ($_GET['searchType'] == 'name') {
      if (!isset($_SESSION['currPage'])) {
        // first time read these data, calculate page number
        setPages('SELECT COUNT(profile_id) FROM profile WHERE first_name
          LIKE "%'.$_GET['searchKey'].'%" OR last_name LIKE "%'.$_GET['searchKey'].'%"',
          $pdo, $ROWSPERPAGE);
      }
      $stmt = $pdo->prepare('SELECT * FROM profile WHERE first_name LIKE "%'.$_GET['searchKey'].'%"
                             OR last_name LIKE "%'.$_GET['searchKey'].'%" ORDER BY last_name
                             LIMIT '.(($_SESSION['currPage']-1)*$ROWSPERPAGE).', '.$ROWSPERPAGE);
    } elseif ($_GET['searchType'] == 'content') {
      // content search
      if (!isset($_SESSION['currPage'])) {
        setPages('SELECT COUNT(profile_id) FROM profile WHERE headline
          LIKE "%'.$_GET['searchKey'].'%" OR summary LIKE "%'.$_GET['searchKey'].'%"',
          $pdo, $ROWSPERPAGE);
      }
      $stmt = $pdo->prepare('SELECT * FROM profile WHERE headline LIKE "%'.$_GET['searchKey'].'%"
                            OR summary LIKE "%'.$_GET['searchKey'].'%" ORDER BY last_name
                            LIMIT '.(($_SESSION['currPage']-1)*$ROWSPERPAGE).', '.$ROWSPERPAGE);
    } else {
      // search type invalid
    }
    $stmt->execute();
  }

  else {
    // results without searching
    if ($PAGESETTING === false) {
      $stmt = $pdo->prepare('SELECT * FROM '.$dbname.'.profile');
      $stmt->execute();
    } else {
      if (!isset($_SESSION['currPage'])) {
        // first time read these data, calculate page number
        setPages('SELECT COUNT(profile_id) FROM profile', $pdo, $ROWSPERPAGE);
      }

      $stmt = $pdo->prepare('SELECT * FROM '.$dbname.'.profile
       ORDER BY last_name LIMIT '.(($_SESSION['currPage']-1)*$ROWSPERPAGE).', '.$ROWSPERPAGE);
      $stmt->execute();
    }

  }
?>
<!-- control code is up -->



<!-- view code is below -->
<!DOCTYPE html>
<html>
<head>
<title>Zezhong Zhang's Resume Registry</title>
</head>
<body>
<div class="container">
<h1>Zezhong Zhang's Resume Registry</h1>

<?php

  flashMessage();
  loginPortal();

  if ($stmt->rowCount() > 0) {
    // profiles exist
    // search dialog
    echo '<form method="post">'."\n";
    echo '<input type="text" name="searchKey">'."\n";
    echo '<input type="submit" name="search" value="Search">'."\n";
    echo '<input type="submit" name="showAll" value="Show All"><br>'."\n";
    echo '<input type="radio" name="searchType" value="name"><label for="name">Name</label>'."\n";
    echo '<input type="radio" name="searchType" value="content"><label for="content">Content</label>'."\n";
    echo '</form>'."\n";

    echo '<table class="table">'."\n".'<tr>'."\n".'<th>Name</th>'."\n".'<th>Headline</th>';
    if (isset($_SESSION['name'])) {
        echo '<th>Action</th>'."\n".'';
    }
    echo "</tr>\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      echo "<tr>\n";
      echo '<td>'."\n".'<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name'])."</td>\n";
      echo '<td>'."\n".''.htmlentities($row['headline']).'</td>'."\n".'';
      if (isset($_SESSION['name']) && $row['user_id'] == $_SESSION['user_id']) {
        echo '<td>'."\n".'<p><a href="edit.php?profile_id='.$row['profile_id'].'">Edit</p>'.' '
              .'<p><a href="delete.php?profile_id='.$row['profile_id'].'">Delete</p></td>';
      }
      echo "</tr>";
    }
    echo "</table>";
  }

  if ($PAGESETTING) {
    echo "<div id=\"pageMover\">\n
      <form class=\"\" method=\"post\">\n
        <input type=\"submit\" name=\"back\" value=\"Back\">\n
        <span><?php echo ".$_SESSION['currPage']."' / '".$_SESSION['totalPages']."; ?></span>\n
        <input type=\"submit\" name=\"next\" value=\"Next\">\n
      </form>\n
    </div>\n";
  }

  if (isset($_SESSION['name'])) {
    echo '<a href="add.php"><button type="button" class="btn btn-primary">Add New Entry</button></a>';
  }
 ?>

<p>
<b>Note:</b> Your implementation should retain data across multiple
logout/login sessions.  This sample implementation clears all its
data periodically - which you should not do in your implementation.
</p>
</div>
</body>
