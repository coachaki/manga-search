<?php 

include('/sudo-root/database.php');
include('mangalist.php');
ini_set('max_execution_time', 0);

$manga_list = list_dirs("/manga/");
for ($i = count($manga_list) - 1; $i >= 0; --$i) {
	if ($manga_list[$i]["title"][0] == "!") {
		if (($dir_name = $manga_list[$i]["title"]) == "!English!") {
			unset($manga_list[$i]);
		}
		elseif ($dir_name == "!!How_to_Read!!") {
			unset($manga_list[$i]);
		}
		elseif ($dir_name == "!COMPLETE!") {
			$complete_list = list_dirs($manga_list[$i]["path"]);
			for ($k = count($complete_list) - 1; $k >= 0; --$k) {
				$complete_list[$k]["completed"] = "Yes";
				$complete_list[$k]["files"] = list_files($complete_list[$k]["path"]);
			}
			unset($manga_list[$i]);
		}
	}
	elseif ($manga_list[$i]["title"] == "雑誌") unset($manga_list[$i]);
	else {
		$manga_list[$i]["completed"] = "No";
		$manga_list[$i]["files"] = list_files($manga_list[$i]["path"]);
	}
}

/*
echo mb_convert_encoding("雑誌<br>", "UTF-8", "auto");
echo "テスト";
 */

$manga_list = array_merge($manga_list, $complete_list);
//echo "<pre>", print_r($manga_list), "</pre>";
//echo count($manga_list);

//$manga_db = mysqli_connect("$host", "$user", "$pass", "manga_db") or die('Error connecting to MySQL server.');

foreach($manga_list as $series) {
	$dirdate = date("Y-m-d H:i:s", $series["lastmod"]);
	$sql = "INSERT INTO manga_index (title, completed, last_updated, table_name)
		VALUES ('{$series["title"]}', '{$series["completed"]}', '$dirdate', '{$series["table_name"]}')";
	if (!mysqli_query($manga_db,$sql))
		echo "failed to do query $sql<br>";

	$sql = "CREATE TABLE {$series["table_name"]}(filename VARCHAR(256), volume VARCHAR(8), filepath TEXT, size INT, date_added DATETIME) COLLATE utf8_general_ci";
	if (!mysqli_query($manga_db,$sql))
		echo "failed to do query $sql<br>";

	foreach($series["files"] as $volume) {
		$date = date("Y-m-d H:i:s", $volume["lastmod"]);
		$sql = "INSERT INTO {$series["table_name"]} (filename, volume, filepath, size, date_added)
			VALUES (\"{$volume["name"]}\", {$volume["vol"]}, \"{$volume["path"]}\", {$volume["size"]}, '$date')";
		if (!mysqli_query($manga_db,$sql))
			echo "failed to do query $sql<br>";
	}
}

/*
while ($row = mysqli_fetch_array($result)) {
	$filesize = number_format(($row["size"] * .0009765625) * .0009765625, 2);
	echo "<a href=\"{$row["filepath"]}\">" . $row["filename"] . "</a> volume: " . $row["volume"] . "date modified: {$row["date_added"]}, size: $filesize MB<br>";
}
 */
// mysqli_close($manga_db);

echo "done";



?>
