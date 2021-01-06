<?php
  $pdo = new PDO('mysql:dbname=misc;host=localhost;port=3306', 'root', 'root');
  $dbname = 'misc';

  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 ?>
