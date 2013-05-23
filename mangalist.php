<?php

function list_dirs($dir) {

	$dirlist = array();

	if (substr($dir, -1) != "/") $dir .= "/";

	$d = @dir($dir) or die ("list_dirs: failed opening $dir for reading");

	while (false !== ($entry = $d->read())) {

		if ($entry[0] == ".") continue;

		if (is_dir("$dir$entry")) {
			$parent = dirname("$dir$entry");
			$dirlist[] = array(
				"title" => mb_convert_encoding("$entry", "UTF-8", "SJIS"),
				"path" => mb_convert_encoding("$parent/$entry/", "UTF-8", "SJIS"),
				"table_name" => str_replace(array("[","]","(",")","!","?","%","^","~"),"",str_replace(array(" ",".","'","&","-"),"_",mb_convert_encoding("$entry", "UTF-8", "SJIS"))),
				"lastmod" => filemtime("$dir$entry"),
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
					"name" => explode(".", "$entry_utf8")[0],
					"vol" => "NULL",
					// "type" => filetype("$dir$entry"),
					"size" => filesize("$dir$entry"),
					"lastmod" => filemtime("$dir$entry"),
				);
			}
			else {
				$filelist[] = array(
					"path" => mb_convert_encoding("$parent/$entry", "UTF-8", "SJIS"),
					"name" => explode(".", "$entry_utf8")[0],
					"vol" => int($match[0]),
					// "type" => filetype("$dir$entry"),
					"size" => filesize("$dir$entry"),
					"lastmod" => filemtime("$dir$entry"),
				);
			}
		}
	}

	$d->close();

	return $filelist;
}

function sql_insert_series($db, $series) {
	$dirdate = date("Y-m-d H:i:s", $series["lastmod"]);
	$sql = "INSERT INTO manga_index (title, completed, last_updated, table_name)
		VALUES ('{$series["title"]}', '{$series["completed"]}', '$dirdate', '{$series["table_name"]}')";
	if (!mysqli_query($db, $sql))
		echo "failed to do insertion: $sql<br>";
}

function sql_create_series($db, $series) {
	$sql = "CREATE TABLE {$series["table_name"]}(filename VARCHAR(256), volume VARCHAR(8), filepath TEXT, size INT, date_added DATETIME) COLLATE utf8_general_ci";
	if (!mysqli_query($db, $sql))
		echo "failed to do creation: $sql<br>";
}

function sql_insert_volume($db, $volume) {
		$date = date("Y-m-d H:i:s", $volume["lastmod"]);
		return "INSERT INTO {$series["table_name"]} (filename, volume, filepath, size, date_added)
			VALUES (\"{$volume["name"]}\", {$volume["vol"]}, \"{$volume["path"]}\", {$volume["size"]}, '$date')";
		if (!mysqli_query($db, $sql))
			echo "failed to do insertion: $sql<br>";
}

function get_manga($manga_path) {

	$d = @dir($manga_path) or die ("insert_manga: failed to open $manga_path for reading");

	$entry = basename("$manga_path");
	$parent = dirname("$manga_path");

	$manga = array(
		"title" => mb_convert_encoding("$entry", "UTF-8", "SJIS"),
		"path" => mb_convert_encoding("$parent/$entry/", "UTF-8", "SJIS"),
		"table_name" => str_replace(array("[","]","(",")","!","?","%","^"),"",str_replace(array(" ",".","'","&","-"),"_",mb_convert_encoding("$entry", "UTF-8", "SJIS"))),
		"lastmod" => filemtime("$manga_path"),
	);

	$manga["files"] = list_files($manga_path);

	return $manga;
}

?>
