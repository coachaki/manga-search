<?php 

include('../sudo-root/database.php');
include('mangalist.php');
ini_set('max_execution_time', 0);


$real = get_manga("/manga/Real");
$neet = get_manga("/manga/Saijou no Meii ~The King of NEET~/");

/*
echo mb_convert_encoding("雑誌<br>", "UTF-8", "auto");
echo "テスト";
 */

echo "<pre>", print_r($real), "</pre>";
echo "<pre>", print_r($neet), "</pre>";

//$manga_db = mysqli_connect("$host", "$user", "$pass", "manga_db") or die('Error connecting to MySQL server.');

sql_insert_series(NULL, $neet);
/*

foreach($manga_list as $series) {
	$dirdate = date("Y-m-d H:i:s", $series["lastmod"]);
	$sql = "INSERT INTO manga_index (title, completed, last_updated, table_name)
		VALUES ('{$series["title"]}', '{$series["completed"]}', '$dirdate', '{$series["table_name"]}')";
	if (!mysqli_query($manga_db,$sql))
		echo "failed to do query $sql<br>";

	if (!mysqli_query($manga_db,$sql))
		echo "failed to do query $sql<br>";

	foreach($series["files"] as $volume) {
		if (!mysqli_query($manga_db,$sql))
			echo "failed to do query $sql<br>";
	}
}

 */
/*
while ($row = mysqli_fetch_array($result)) {
	$filesize = number_format(($row["size"] * .0009765625) * .0009765625, 2);
	echo "<a href=\"{$row["filepath"]}\">" . $row["filename"] . "</a> volume: " . $row["volume"] . "date modified: {$row["date_added"]}, size: $filesize MB<br>";
}
 */
//mysqli_close($manga_db);

echo "done";

?>
