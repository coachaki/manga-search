<?php 

include('../sudo-root/database.php');
include('mangalist.php');
ini_set('max_execution_time', 0);


$area = get_manga("/manga/Area no Kishi");
$area["completed"] = "No";
$qed = get_manga("/manga/Q.E.D");

/*
echo mb_convert_encoding("雑誌<br>", "UTF-8", "auto");
echo "テスト";
 */

echo "<pre>", print_r($area), "</pre>";
echo "<pre>", print_r($qed), "</pre>";

/*
$manga_db = mysqli_connect("$host", "$user", "$pass", "manga_db") or die('Error connecting to MySQL server.');

mysqli_close($manga_db);
 */

echo "done";

?>
