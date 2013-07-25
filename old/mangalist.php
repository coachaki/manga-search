<?php

function list_dirs($dir) {

	$dirlist = array();

	if (substr($dir, -1) != "/") $dir .= "/";

	$d = @dir($dir) or die ("list_dirs: failed opening $dir for reading");

	while (false !== ($entry = $d->read())) {

		if ($entry[0] == ".") continue;

		if (is_dir("$dir$entry")) {
			$parent = dirname("$dir$entry");
			$table_name = str_replace(array("[","]","(",")","!","?","%","^","~"),"",str_replace(array(" ",".","'","&","-"),"_",mb_convert_encoding("$entry", "UTF-8", "SJIS")));
			$table_name = "manga_".$table_name;
			$dirlist[] = array(
				"title" => mb_convert_encoding("$entry", "UTF-8", "SJIS"),
				"path" => mb_convert_encoding("$parent/$entry/", "UTF-8", "SJIS"),
				"table_name" => $table_name,
				"last_updated" => filemtime("$dir$entry"),
			);
		}
	}

	$d->close();

	return $dirlist;
}

function int($s){return(int)preg_replace('/[^\-\d]*(\-?\d*).*/','$1',$s);}

function list_files($dir) {

	$filelist = array();

	if (substr($dir, -1) != "/") $dir .= "/";

	$d = @dir($dir) or die ("list_files: failed opening $dir for reading");

	while (false !== ($entry = $d->read())) {

		if ($entry[0] == ".") continue;

		if (is_readable("$dir$entry")) {
			$parent = dirname("$dir$entry");
			$entry_utf8 = mb_convert_encoding("$entry", "UTF-8", "SJIS");
			$matched = preg_match('/(v|第)[0-9]+/', "$entry_utf8", $match);
			if (!$matched) {
				$filelist[] = array(
					"path" => mb_convert_encoding("$parent/$entry", "UTF-8", "SJIS"),
					"name" => explode(".zip",$entry_utf8)[0],
					"vol" => "NULL",
					// "type" => filetype("$dir$entry"),
					"size" => filesize("$dir$entry"),
					"last_updated" => filemtime("$dir$entry"),
				);
			}
			else {
				$filelist[] = array(
					"path" => mb_convert_encoding("$parent/$entry", "UTF-8", "SJIS"),
					"name" => explode(".zip",$entry_utf8)[0],
					"vol" => int($match[0]),
					// "type" => filetype("$dir$entry"),
					"size" => filesize("$dir$entry"),
					"last_updated" => filemtime("$dir$entry"),
				);
			}
		}
	}

	$d->close();

	return $filelist;
}

function get_manga($manga_path) {

	$d = @dir($manga_path) or die ("get_manga: failed to open $manga_path for reading");

	$entry = basename("$manga_path");
	$parent = dirname("$manga_path");

	$table_name = str_replace(array("[","]","(",")","!","?","%","^","~"),"",str_replace(array(" ",".","'","&","-"),"_",mb_convert_encoding("$entry", "UTF-8", "SJIS")));
	$table_name = "manga_".$table_name;
	$manga = array(
		"title" => mb_convert_encoding("$entry", "UTF-8", "SJIS"),
		"path" => mb_convert_encoding("$parent/$entry/", "UTF-8", "SJIS"),
		"table_name" => $table_name,
		"last_updated" => filemtime("$manga_path"),
	);

	$manga["files"] = list_files($manga_path);

	return $manga;
}

function sql_create_manga_table($db) {
	$sql = "CREATE TABLE IF NOT EXISTS info_manga_table (
		title TEXT,
		author TEXT,
		path TEXT,
		completed ENUM('yes','no'),
		last_updated DATE,
		table_name VARCHAR(255) NOT NULL,
		PRIMARY KEY (table_name)
	) COLLATE utf8_general_ci";
}

function sql_insert_series($db, $series) {
	sql_create_manga_table($db);
	$dirdate = date("Y-m-d H:i:s", $series["last_updated"]);
	$sql = "INSERT INTO info_manga_table (title, path, completed, last_updated, table_name)
		VALUES (\"{$series["title"]}\", \"{$series["path"]}\", '{$series["completed"]}', '$dirdate', '{$series["table_name"]}')
		ON DUPLICATE KEY UPDATE title=\"{$series["title"]}\", path=\"{$series["path"]}\", completed='{$series["completed"]}'";
	if (!mysqli_query($db, $sql))
		echo "failed to do insertion: $sql<br>";

	sql_create_series($db, $series);
	foreach($series["files"] as $volume)
		sql_insert_volume($db, $volume, $series["table_name"]);
}

function sql_create_series($db, $series) {
	$sql = "CREATE TABLE IF NOT EXISTS {$series["table_name"]}(
		filename VARCHAR(255),
		volume INT AUTO_INCREMENT,
		filepath TEXT,
		size INT,
		last_updated DATETIME,
		PRIMARY KEY (volume)
	) COLLATE utf8_general_ci
	AUTO_INCREMENT = 1000";
	if (!mysqli_query($db, $sql))
		echo "failed to do creation: $sql<br>";
}

function sql_insert_volume($db, $volume, $table) {
		$date = date("Y-m-d H:i:s", $volume["last_updated"]);
		if (strcmp($volume["vol"],"NULL") == 0) {
			$sql = "SELECT volume FROM {$series["table_name"]} WHERE filename LIKE \"%{$volume["name"]}%\"";
			$result = mysqli_query($manga_db,$sql);
			$row = mysqli_fetch_array($result);
			if (mysqli_num_rows($result) > 0)
				$volume["vol"] = $row["volume"];
		}
		$sql = "INSERT INTO {$series["table_name"]}
			(filename, volume, filepath, size, last_updated)
			VALUES
			(\"{$volume["name"]}\", {$volume["vol"]}, \"{$volume["path"]}\", {$volume["size"]}, '$date')
			ON DUPLICATE KEY UPDATE filename=\"{$volume["name"]}\", filepath=\"{$volume["path"]}\", size={$volume["size"]}, last_updated='$date'";
		if (!mysqli_query($db, $sql))
			echo "failed to do insertion: $sql<br>";
}

?>
