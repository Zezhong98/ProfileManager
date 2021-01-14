<?php
  //  print_r(PDO::getAvailableDrivers());
  $pdo = new PDO('mysql:dbname=Profilemng;host=localhost;port=3306', 'zzz', 'Zzz_123');
  // alter user '%'@'%' identified by 'iii'
  // set global validate_password.***=*;
  // show variables like 'validate_password%'; 
  $dbname = 'Profilemng';

  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 ?>
