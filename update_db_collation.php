<?php

/* Define Variables */

$mysqlhost 		= 'localhost';
$mysqluser 		= 'db_user';
$mysqlpass 		= 'db_pass';
$mysqldatabase 	= 'db_name';
$collation 		= 'utf8_general_ci';

function start_db($mysqlhost,$mysqldatabase, $mysqluser, $mysqlpass)
{
	global $conn;
	$conn = mysql_connect($mysqlhost, $mysqluser, $mysqlpass);
    if (!$conn)
	{
		die('Database error.');
	}	
	$select = mysql_select_db($mysqldatabase, $conn);
    if (!$select)
    {
		die('Database error.');
	}
}
function end_db ($conn)
{
	mysql_close($conn);
}

start_db($mysqlhost,$mysqldatabase, $mysqluser, $mysqlpass);
 //Start code from http://php.vrana.cz/ - Author - Jakub Vrana
function mysql_convert($query) {
    echo $query . '           OK \n';
    return mysql_query($query);
}
mysql_convert("ALTER DATABASE $mysqldatabase COLLATE $collation");
$result = mysql_query("SHOW TABLES");
while ($row = mysql_fetch_row($result)) {
    mysql_convert("ALTER TABLE $row[0] COLLATE $collation");
    $result1 = mysql_query("SHOW COLUMNS FROM $row[0]");
    while ($row1 = mysql_fetch_assoc($result1)) {
        if (preg_match('~char|text|enum|set~', $row1["Type"])) {
            mysql_convert("ALTER TABLE $row[0] MODIFY $row1[Field] $row1[Type] CHARACTER SET binary");
            mysql_convert("ALTER TABLE $row[0] MODIFY $row1[Field] $row1[Type] COLLATE $collation" . ($row1["Null"] ? "" : " NOT NULL") . ($row1["Default"] && $row1["Default"] != "NULL" ? " DEFAULT '$row1[Default]'" : ""));
        }
    }
}
mysql_free_result($result);
//End code from http://php.vrana.cz/ - Author - Jakub Vrana
end_db($conn);
echo 'Done';
?>