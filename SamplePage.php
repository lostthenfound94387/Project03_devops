<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>Sample page</h1>
<?php

  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Ensure that the Employees table exists. */
  VerifyEmployeesTable($connection, DB_DATABASE); 

  /* If input fields are populated, add a row to the Employees table. */
  $employee_name = htmlentities($_POST['Name']);
  $employee_address = htmlentities($_POST['Address']);

  if (strlen($employee_name) || strlen($employee_address)) {
    if (isset($_POST['add'])) {
      AddEmployee($connection, $employee_name, $employee_address);
    }
    else if (isset($_POST['Query'])) {
      QueryData($connection, $employee_name, $employee_address);
    }
    else if (isset($_POST['ReviseData'])) {
      ReviseData($connection, $employee_name, $employee_address);
    }
    else if (isset($_POST['DelData'])) {
      DelData($connection, $employee_name, $employee_address);
    }

  }
  /* Remove all data */
  if (isset($_POST['DeleteALL'])) {
    $query = "DELETE FROM `Employees`";
	mysqli_query($connection,$query);
}
?>

<!-- Input form -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>Name</td>
      <td>Address</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="Name" maxlength="45" size="30" />
      </td>
      <td>
        <input type="text" name="Address" maxlength="90" size="60" />
      </td>
      <td>
        <input type="submit" name="add" value="Add Data" />
      </td>
	  <td>
		<input type="submit" name="Query" value="Query" />
	  </td>
<!--	  <td>
        <input type="submit" name="DelData" value="Delete Data" />
      </td>
	  <td>
        <input type="submit" name="DeleteALL" value="Delete All Data" />
      </td>
	  <td>
		<input type="submit" name="ReviseData" value="Revise Data" />
	  </td> -->
    </tr>
  </table>
</form>

<!-- Display table data. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>Name</td>
    <td>Address</td>
  </tr>

<?php

$result = mysqli_query($connection, "SELECT * FROM Employees"); 

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>";
  echo "</tr>";
}
?>

</table>

<!-- Clean up. -->
<?php

  mysqli_free_result($result);
  mysqli_close($connection);

?>

</body>
</html>


<?php

/* Add an employee to the table. */
function AddEmployee($connection, $name, $address) {
   $n = mysqli_real_escape_string($connection, $name);
   $a = mysqli_real_escape_string($connection, $address);

   $query = "INSERT INTO `Employees` (`Name`, `Address`) VALUES ('$n', '$a');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding employee data.</p>");
}

/* Check whether the table exists and, if not, create it. */
function VerifyEmployeesTable($connection, $dbName) {
  if(!TableExists("Employees", $connection, $dbName)) 
  { 
     $query = "CREATE TABLE `Employees` (
         `ID` int(11) NOT NULL AUTO_INCREMENT,
         `Name` varchar(45) DEFAULT NULL,
         `Address` varchar(90) DEFAULT NULL,
         PRIMARY KEY (`ID`),
         UNIQUE KEY `ID_UNIQUE` (`ID`)   
       ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}

/* Query data from table */
function QueryData($connection, $name, $address) {
	$n = mysqli_real_escape_string($connection, $name);
    $a = mysqli_real_escape_string($connection, $address);
    $query = "SELECT `Name`, `Address` FROM `Employees` WHERE Name=$n OR Address=$a";
    if(!mysqli_query($connection, $query)) echo("<p>Error Query table.</p>");
}
   

/* Revise the table's data */
function ReviseData($connection, $name,$address) {
	$n = mysqli_real_escape_string($connection, $name);
    $a = mysqli_real_escape_string($connection, $address);
	$query = "UPDATE `Employees` set `Address`='$a',`Name`='$n' WHERE Name='$n'";
	if(!mysqli_query($connection, $query)) echo("<p>Error revise table.</p>");
}

/* Remove the data */
function DelData($connection, $name, $address) {
	$n = mysqli_real_escape_string($connection, $name);
    $a = mysqli_real_escape_string($connection, $address);
	$query = "DELETE FROM `Employees` WHERE (`Name`, `Address`) = ('$n', '$a');";
	if(!mysqli_query($connection, $query)) echo("<p>Error delete table.</p>");
}



/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
    $t = mysqli_real_escape_string($connection, $tableName);
    $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection, 
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>
