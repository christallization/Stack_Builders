<?php

// Code Challenge
// Author: Christopher Obigho
// 2/2/2018

/***************************************************************************************************************************************************************/
// CHALLENGE 1:

// All 3 flavors work:

function card_check($number) {
	settype($number, 'string');
	$sumTable = array(
		array(0,1,2,3,4,5,6,7,8,9),
		array(0,2,4,6,8,1,3,5,7,9)
	);
	$sum = 0;
	$flip = 0;
	for ($i = strlen($number) - 1; $i >= 0; $i--) {
		$sum += $sumTable[$flip++ & 0x1][$number[$i]];
	}
	if ( $sum % 10 === 0 ){
		return "Valid";
	} else {
		return "In-valid";
	};
};

function card_check2($number) {
	$card_number_checksum = '';
    foreach (str_split(strrev((string) $number)) as $i => $d) {
        $card_number_checksum .= $i %2 !== 0 ? $d * 2 : $d;
    }
	if ( array_sum(str_split($card_number_checksum)) % 10 === 0 ){
		return "Valid";
	} else {
		return "In-valid";
	};
};

function card_check3($number) {
    if ( empty($number) ) {
        return false;
	};
    $_j = 0;
    $_base = str_split($number);
    $_sum = array_pop($_base);
    while (($_actual = array_pop($_base)) !== null) {
        if ($_j % 2 == 0) {
            $_actual *= 2;
            if ($_actual > 9)
                $_actual -= 9;
        }
        $_j++;
        $_sum += $_actual;
    }
	if ( $_sum % 10 === 0 ){
		return "Valid";
	} else {
		return "In-valid";
	};
};


/***************************************************************************************************************************************************************/
// CHALLENGE 2:

// This is very old. Needs to be MYSQLI much secure.
//mysql_connect('localhost','accounting','ffdbR4fsa');
//mysql_select_db('accounting');

//Setup DB connection variables.
$server = "localhost";		// DB Server Host
$DBusername = "accounting";	// Database Username
$DBpassword = "ffdbR4fsa";	// Database Password
$database = "accounting";	// Database Name

//Connect to Db Server using an object
$mysqli = new mysqli($server, $DBusername, $DBpassword, $database);
if (mysqli_connect_errno()) { header('HTTP/1.1 500 Error: Could not connect to db!'); exit(); };  // Check for errors connecting if so exit and spit out a notice. If not all good.

session_start(); // Adding a session start even thoguh I am not sure why it is being used here.
$user = $_SESSION['user']; // I am not entirely sure what this session stored user is being used for below. But it might be a namespace pollution candidate, even if it can be over-rules below. But I do not see it used anywhere.

//$users = mysql_query("select * from users"); // You do not need to select all (*) it is bad practise and can be a slow query, just select the columns needed and based on the use the below is better. Also use MYQSLI instead.
$users = mysqli_query("SELECT id, name, is_admin FROM users");

echo "<h1>User Report</h1>";
echo "<h2>All users</h2>";

//foreach ($users as $user){ // A While loop is better than this and it needs to loop through an array of records. Use Associative or Array mysqli_fetch_assoc or mysqli_fetch_array Fixed below.
while ($user = mysqli_fetch_array($users)) { 
	$id = $user['id']; // Used single quotes instead
	$name = $user['name'];  // Used single quotes instead
	$is_admin = $user['is_admin']; // This was missing a semi-colon and single quotes are better for literals. Fixed below.
	
	//echo "$id - $name - "; // This will not work as you are adding variables and strings to be echoed without escaping variables.
	echo $id." - ".$name." - ";
	
	//if ($is_admin = True) { // You are not validating a string here you are besically assigning value. Fixed below.
	if ($is_admin == "True") {
		//echo "Administrator" // Missing semi-colon. Fixed below.
		echo "Administrator";
	} else {
		//echo "User" // Missing semi-colon. Fixed below.
		echo "User";
	}
	//echo "<br />" // Missing semi-colon. Fixed below.
	echo "<br />";

	
	echo "<h1>Current user</h1>";
	echo $name;
	echo " - ";
	//if ($user["is_admin"] = True) { // $is_admin is already set in the loop no need to recall the actual DB field, plus this is assigning not comparing values. Fixed below.
	// Also this section looks to be a duplucate of what is being looped above.
	if ($is_admin == "True") {
		echo "Administrator";
	} else {
		echo "User";
	};
	
};

// This entire block seems to be outside the loop + It is using private scoped variables that only exist within the loop. Moved it back into the loop and also noticed it is a duplicate of the current stuff in the loop as well.
/*echo "<h1>Current user</h1>";
echo $user["name"];
echo " - ";
if ($user["is_admin"] = True) {
echo "Administrator"
} else {
echo "User"
}*/


/***************************************************************************************************************************************************************/
// CHALLENGE 3:


// Same as Challenge 2 this is old.
//mysql_connect('localhost','accounting','ffdbR4fsa');
//mysql_select_db('accounting');

//Setup DB connection variables.
$server = "localhost";		// DB Server Host
$DBusername = "accounting";	// Database Username
$DBpassword = "ffdbR4fsa";	// Database Password
$database = "accounting";	// Database Name

//Connect to Db Server using an object
$mysqli = new mysqli($server, $DBusername, $DBpassword, $database);
if (mysqli_connect_errno()) { header('HTTP/1.1 500 Error: Could not connect to db!'); exit(); };  // Check for errors connecting if so exit and spit out a notice. If not all good.

?>
<!-- This form needs some client side validation as well. E.G. Cannot be empty, maybe Username requries a special format e.t.c. -->
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post"> <!-- Added a self submitting form action -->
    <table width="50%" border="0">
        <tr>
        	<td><h3>Admin Log In</h3></td>
        </tr>
        <tr>
        	<td><input type="text" name="username"></td>
        </tr>
        <tr>
        	<td><input type="password" name="password"> <!-- Type attribute should most likely be password for privacy --> </td>
        </tr>
    </table>
    <input type="submit" value="Log In" name="s"> <!-- We do not realy need a name attribute here. But it seems the name is used to track POST. Other way to do this than this way. -->
</form>

<?php

session_start(); //Declaring a session start since we are using sessions below.
//if ($_POST['s']){ // Best to check if it is SET as opposed to see if it exists
if ( isset($_POST['s']) ) {
	$user = $_POST['user'];
	$pass = $_POST['password'];
	//$result = mysql_query("select * from admin_list where user_name = '$user' and password = '$pass'"); // You don't really need to select all from this table, plus you can just use COUNT() on a field too. Fixed Below.
	$result = $mysqli->query("SELECT id FROM admin_list WHERE user_name = '$user' AND password = '$pass'");
	$result_count = $mysqli->affected_rows; // Lets count the affected rows, although I could have used the LIMIT 1 clause as there should only be one user with these unique login details
	//if ( mysql_num_rows($result) == 1 ) {
	if ( $result_count >= 1 ){
		//$_SESSION['is_admin'] = True; // Needs to be a string.
		$_SESSION['is_admin'] = "True";
		$_SESSION['user'] = $user;
		//echo "Logged in as $user"; // Mixing variables with strings in an echo.
		echo "Logged in as ".$user;
	} else {
		//$_SESSION['is_admin'] = False; // Needs to be a string.
		$_SESSION['is_admin'] = "False"; // Needs to be a string.
		//$_SESSION['user'] = NULL; // Needs to be a string.
		$_SESSION['user'] = "NULL"; // Needs to be a string.
		//echo 'Invalid login or password'; // Double quotes for string.
		echo "Invalid login or password";
	};
};
?>
