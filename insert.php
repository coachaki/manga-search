<?php 

include('../sudo-root/database.php');
include('mangalist.php');
ini_set('max_execution_time', 0);


$real = get_manga("/manga/Real");
$neet = get_manga("/manga/Saijou no Meii ~The King of NEET~/");
$real["completed"] = "No";
$neet["completed"] = "No";

/*
echo mb_convert_encoding("雑誌<br>", "UTF-8", "auto");
echo "テスト";
 */

echo "<pre>", print_r($real), "</pre>";
echo "<pre>", print_r($neet), "</pre>";

/*
$manga_db = mysqli_connect("$host", "$user", "$pass", "manga_db") or die('Error connecting to MySQL server.');

mysqli_close($manga_db);
 */

echo "done";

?>
