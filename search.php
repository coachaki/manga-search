<?php
$searchterm = "";
date_default_timezone_set("America/Los_Angeles");
if (isset($_POST['submit']) and isset($_GET['title']) and strcmp($title = $_POST['title'],"") != 0) {
	if (isset($_GET['title'])) {
		$title = $_POST['title'];
		$manga_db = mysqli_connect("localhost", "manga_search","","manga_db") or die("error connecting to manga database");
		$searchterm = str_replace("%", "\\%", $title);
		$searchterm = str_replace("_", "\\_", $searchterm);
		$searchterm = str_replace("\"", "\\\"", $searchterm);
		$searchterm = str_replace("'", "\\'", $searchterm);

		$sql = "SELECT table_name, path, title
			FROM info_manga_table
			WHERE title LIKE \"%$searchterm%\"";
		$series_result = mysqli_query($manga_db, $sql);
		if (mysqli_num_rows($series_result) > 0) {
			echo "Your search result for: $title<br>";
			echo "<table id=\"resultlist\">
				<tr class=\"label\"><td class=\"series\">series</td><td class=\"title\">files</td><td class=\"size\">size</td></tr>";
			$i = 0;
			while ($series_row = mysqli_fetch_array($series_result)) {
				$sql = "SELECT *
					FROM {$series_row["table_name"]}";
				$file_result = mysqli_query($manga_db, $sql);
				while($file_row = mysqli_fetch_array($file_result)) {
					if ($i % 2 == 0)
						echo "<tr class=\"even\"><td class=\"series\"><a href=\"{$series_row["path"]}\">{$series_row["title"]}</a></td><td class=\"title\"><a href=\"{$file_row["filepath"]}\">{$file_row["filename"]}</a></td><td class=\"size\">",number_format($file_row["size"] / 1048576, 2)," MB</td></tr>";
					else
						echo "<tr class=\"odd\"><td class=\"series\"><a href=\"{$series_row["path"]}\">{$series_row["title"]}</a></td><td class=\"title\"><a href=\"{$file_row["filepath"]}\">{$file_row["filename"]}</a></td><td class=\"size\">",number_format($file_row["size"] / 1048576, 2)," MB</td></tr>";
					$i++;
				}
			}
			echo "</table>";
			echo "<br>";
		}
		else 
			echo "Your search for: $title did not return anything.<br>";
	}
}
?>