<?php

include_once 'includes/dbhandler.inc.php';

$exit = false;

while ($exit == false) {
  $available_tables = mysqli_query($conn, "SHOW TABLES;");
  $resultCheck = mysqli_num_rows($available_tables);

  if ($resultCheck > 0) {
    echo "Options: \n1: Read a table\n2: Make a new entry\n3: Add a new table\n0: Exit\n";
    $input = readline("What would you like to do?  (give option number)  ");
    $option = intval( $input );
    switch ($option) {
      case 0:
        $exit = true;
        break;
      case 1:

          echo "available tables: \n";

          while ($tables = mysqli_fetch_array($available_tables)) {
            foreach ($tables as $table) {
              echo "{$table}\n";
            }
          }

          $table_name = readline("Which table would you like to see?  ");

          $sql = "SELECT * FROM {$table_name};";
          $result = mysqli_query($conn, $sql);
          $resultCheck = mysqli_num_rows($result);

          if ($resultCheck > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              echo "______________________________________________________\n";
              foreach ($row as $key => $value) {
                echo $key;
                echo ":  ";
                echo $value;
                echo "\n............................................\n";
              }
              echo "______________________________________________________\n";
            }
          } else {
            echo "table name misspelled or does not exist - make sure to use the plural and all upper-/lowercase";
          }
          break;
      case 2:
          #add_new_entry_function
          break;
      case 3:
          #add_table_function
          break;
    }
  } else {
    echo "Your database has no tables yet, add a first table:\n";
    #add_table_function
  }
}


#________________________________________________________________________________
#________________________________________________________________________________
#________________________________________________________________________________

#read_table_function:

// $table_name = readline("Which table would you like to see?  ");

// $sql = "SELECT * FROM {$table_name};";
// $result = mysqli_query($conn, $sql);
// $resultCheck = mysqli_num_rows($result);

// if ($resultCheck > 0) {
//   while ($row = mysqli_fetch_assoc($result)) {
//     echo "______________________________________________________\n";
//     foreach ($row as $key => $value) {
//       echo $key;
//       echo ":  ";
//       echo $value;
//       echo "\n............................................\n";
//     }
//     echo "______________________________________________________\n";
//   }
// } else {
//   echo "table name misspelled or does not exist - make sure to use the plural and all upper-/lowercase";
// }

#________________________________________________________________________________
#________________________________________________________________________________
#________________________________________________________________________________
