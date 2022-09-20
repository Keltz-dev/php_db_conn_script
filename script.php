<?php

include_once 'includes/dbhandler.inc.php';

$exit = false;

while ($exit == false) {
    $result = mysqli_query($conn, "SHOW TABLES;");
    $resultCheck = mysqli_num_rows($result);

    if ($resultCheck > 0) {

        $table_names = array_map('reset', mysqli_fetch_all($result));
        echo "\nOptions: \n  1: Read a table\n  2: Input your own SQL query\n  3: Add a new table\n  0: Exit\n\n";
        $input = readline("What would you like to do?  (give option number)  ");
        $option = intval( $input );
        echo "\n\n";
        switch ($option) {

            case 0:

                $exit = true;
                break;

            case 1:
                echo "    Option 1:\n\n        Available Tables: \n";
                foreach ($table_names as $tbl_name) {
                  echo "          {$tbl_name}\n";
                }
                $checked = false;
                do {
                    if ($checked) {
                        echo "\n'{$chosen_table_name}' is not an option.\n";
                    }
                    echo "\n";
                    $chosen_table_name = strtolower(readline("    Which table would you like to see?  "));
                    $checked = true;
                } while (!in_array($chosen_table_name, $table_names));

                $sql = "SELECT * FROM {$chosen_table_name};";
                $result = mysqli_query($conn, $sql);
                $resultCheck = mysqli_num_rows($result);

                if ($resultCheck > 0) {
                    echo "///////////////////////////////////////////////////////////////////////////\n";
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "_______________________________________________________________________\n";
                        foreach ($row as $key => $value) {
                            echo "{$key}:      {$value}\n............................................\n";
                        }
                        echo "_______________________________________________________________________\n";
                    }
                    echo "///////////////////////////////////////////////////////////////////////////\n";
                } else {
                    echo "\nThe table you have chosen is empty";
                }
                break;

            case 2:

                echo "\nInput your SQL, you may use multiple lines\n";
                $sql = '';
                $input_line = 'a';

                while ( trim( $input_line )[-1] != ';' ) {
                    $input_line = readline(  );
                    $sql .="{$input_line} ";
                }
                echo $sql;

                $result = mysqli_query($conn, $sql);
                printf("%s\n", mysqli_info($conn));
                $resultCheck = mysqli_num_rows($result);


                if (mysqli_warning_count($conn)) {
                    if ($result = mysqli_query($conn, "SHOW WARNINGS;")) {
                        $row = mysqli_fetch_row($result);
                        printf("%s (%d): %s\n", $row[0], $row[1], $row[2]);
                        mysqli_free_result($result);
                    }
                } else {
                    if ($resultCheck > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "______________________________________________________\n";
                            foreach ($row as $key => $value) {
                                echo "{$key}:      {$value}\n............................................\n";
                            }
                            echo "______________________________________________________\n";
                        }
                    } else {
                        echo "table is empty, the name is misspelled or does not exist - make sure to use the plural and all upper-/lowercase";
                    }
                }
                exit;

                break;
            case 3:

                // list tables
                echo "    Option 3:\n\n        Available Tables: \n";
                foreach ($table_names as $tbl_name) {
                  echo "          {$tbl_name}\n";
                }
                echo "\n";
                // Ask which table?
                // check if in table names
                $checked = false;
                do {
                    if ($checked) {
                        echo "\n    '{$chosen_table_name}' is not an option.\n";
                    }
                    echo "\n";
                    $chosen_table_name = strtolower(readline("    Which table would you like to make a new entry in?  "));
                    $checked = true;
                } while (!in_array($chosen_table_name, $table_names));

                // get column names
                $sql = "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$chosen_table_name}' AND COLUMN_KEY = '';";
                $result = mysqli_query($conn, $sql);
                $resultCheck = mysqli_num_rows($result);

                // iterate and save input in hash
                if ($resultCheck > 0) {
                  $column_names = array_map('reset', mysqli_fetch_all($result));
                  $new_entry_values = array();
                  foreach ($column_names as $clm_name) {
                      echo "\n";
                      array_push($new_entry_values, mysqli_real_escape_string($conn, readline("Assign {$clm_name}:  ")));
                      // $new_entry[$clm_name] = mysqli_real_escape_string(readline("Assign {$clm_name}:  "));
                  }
                }
                // escape input strings
                $columns_string = implode("', '", $column_names);
                $values_string = implode("', '", $new_entry_values);
                $sql = "INSERT INTO {$chosen_table_name} ('{$columns_string}') VALUES ('$values_string');";
                $result = mysqli_query($conn, $sql);

                if (!$result) {
                    printf("%s\n", mysqli_info($conn));
                    if (mysqli_warning_count($conn)) {
                        if ($result = mysqli_query($conn, "SHOW WARNINGS;")) {
                            while ($row = mysqli_fetch_row($result)) {
                                printf("%s (%d): %s\n", $row[0], $row[1], $row[2]);
                            }
                        }
                    }
                } else {
                  echo "Your Entry was successfully created!";
                }
                break;
        }
    } else {
        echo "Your database has no tables yet, add a first table:\n";

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
