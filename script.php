<?php

include_once 'includes/dbhandler.inc.php';
echo "\nWelcome {$dbUsername}!";

$exit = false;

while ($exit == false) {
    // check for user created tables
    $result = mysqli_query($conn, "SHOW TABLES;");
    $tableCheck = mysqli_num_rows($result);
    if (!$tableCheck > 0) {
        echo "There are no tables in your database yet - Add one";
        perform_sql_query($conn);
        break;
    }
    $tableNames = array_map('reset', mysqli_fetch_all($result));

    // main menu
    echo "\n\nOptions: \n  1: Read a table\n  2: Input your own SQL query\n  3: Make an Entry to a table\n  0: Exit\n\n";
    $input = readline("What would you like to do?  (give option number) ");
    $option = intval( $input );

    switch ($option) {

        case 0:
            echo "    exit\n\n    Goodbye!";
            $exit = true;
            break;

        case 1:
            read_db_table($conn, $tableNames);
            break;

        case 2:
            perform_sql_query($conn);
            break;

        case 3:
            make_new_entry($conn, $tableNames);
            break;
      }
}

function display($result)
{
  echo "/////////////////////////////////////////////////////////////////////////////////\n";
  while ($row = mysqli_fetch_assoc($result)) {
      echo "_________________________________________________________________________________\n";
      foreach ($row as $key => $value) {
          $length = 25 - strlen($key);
          $whitespace = "";
          for ($i = 1; $i <= $length; $i++) {
              $whitespace .= " ";
          }
          echo "{$key}:{$whitespace}{$value}\n..........................................................................\n";
      }
      echo "_________________________________________________________________________________\n";
  }
  echo "/////////////////////////////////////////////////////////////////////////////////\n";
}

function get_table_name_input($tableNames)
{
  // list tables
  echo "    Available Tables: \n";
  foreach ($tableNames as $i => $tblName) {
    ++$i;
    echo "      {$i}: {$tblName}\n";
  }
  echo "\n";

  // check if valid table choice
  $checked = false;
  do {
      if ($checked) {
          echo "        '{$input}' is not an option\n\n";
      }
      $input = readline("    Which table would you like to access?  ");
      if (is_numeric($input)) {
          $chosenTableName = $tableNames[intval($input)-1];
      } else {
          $chosenTableName = strtolower($input);
      }
      $checked = true;
  } while (!in_array($chosenTableName, $tableNames));
  return $chosenTableName;
}

function read_db_table($conn, $tableNames)
{
  echo "    Read Table\n\n";
  $chosenTableName = get_table_name_input($tableNames);
  $sql = "SELECT * FROM {$chosenTableName};";
  $result = mysqli_query($conn, $sql);
  $resultCheck = mysqli_num_rows($result);
  if ($resultCheck > 0) {
      display($result);
  } else {
      echo "\n    The table you have chosen is empty";
  }
}

function perform_sql_query($conn)
{
  echo "\n\n    Input your SQL, you may use multiple lines\n\n";
  $sql = '';
  $input_line = 'a';
  while ( trim( $input_line )[-1] != ';' ) {
      $input_line = readline(  );
      $sql .="{$input_line} ";
  }
  $result = mysqli_query($conn, $sql);
  if (!$result) {
      printf("\nERROR: %s\nYour query was NOT successfully performed", mysqli_error($conn));
  } else {
      echo "\n    Your query was successfully performed!\n\n";
      $resultCheck = mysqli_num_rows($result);
      if ($resultCheck > 0) {
          display($result);
      }
  }
}

function make_new_entry($conn, $tableNames)
{
  // Get table name
  echo "    Make a new entry\n\n";
  $chosenTableName = get_table_name_input($tableNames);

  // get column names
  $sql = "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$chosenTableName}' AND COLUMN_KEY = '';";
  $result = mysqli_query($conn, $sql);
  $resultCheck = mysqli_num_rows($result);

  // iterate and save input in hash
  if ($resultCheck > 0) {
      $columnNames = array_map('reset', mysqli_fetch_all($result));
      $newEntryValues = array();
      echo "        New entry for {$chosenTableName}\n\n        Assign values\n";
      foreach ($columnNames as $clmName) {
          array_push($newEntryValues, mysqli_real_escape_string($conn, readline("          {$clmName}: ")));
      }
      echo "\n";
  }

  // check back and confirm
  $confirm = strtolower(readline("        Are you sure? Press Y to confirm "));
  if (!$confirm == 'y') {
    echo "            Aborted";
    return;
  } else {
    echo "            Confirmed";
  }

  // escape input strings
  $columnsString = implode(", ", $columnNames);
  $valuesString = implode("', '", $newEntryValues);
  $sql = "INSERT INTO {$chosenTableName} ({$columnsString}) VALUES ('$valuesString');";

  // perform query
  if (!mysqli_query($conn, $sql)) {
      printf("\nERROR: %s\n\n            Your entry was NOT created", mysqli_error($conn));
  } else {
    echo "\n\n            Your entry was successfully created!";
  }
}
