<?php
$searchterm = "";
date_default_timezone_set("America/Los_Angeles");
if (strcmp($title = $_POST['title'],"") != 0) {
		$title = $_POST['title'];
		$manga_db = mysqli_connect("localhost", "manga_search","","manga_db") or die("error connecting to manga database");
		$searchterm = str_replace("%", "\\%", $title);
		$searchterm = str_replace("_", "\\_", $searchterm);
		$searchterm = str_replace("\"", "\\\"", $searchterm);
		$searchterm = str_replace("'", "\\'", $searchterm);
		$searchterm = str_replace("\\", "\\\\", $searchterm);

		$sql = "SELECT table_name, path, title
			FROM info_manga_table
			WHERE title LIKE \"%$searchterm%\"";
		$series_result = mysqli_query($manga_db, $sql);
		if (mysqli_num_rows($series_result) > 0) {
			echo "<table class=\"result\" id=\"resultlist\">
				<tr class=\"result label\"><td class=\"result series\">series</td><td class=\"result title\">files</td><td class=\"result size\">size</td></tr>";
			$i = 0;
			while ($series_row = mysqli_fetch_array($series_result)) {
				$sql = "SELECT *
					FROM {$series_row["table_name"]}";
				$file_result = mysqli_query($manga_db, $sql);
				while($file_row = mysqli_fetch_array($file_result)) {
					if ($i % 2 == 0)
						echo "<tr class=\"result even\"><td class=\"result series\"><a href=\"{$series_row["path"]}\">{$series_row["title"]}</a></td><td class=\"result title\"><a href=\"{$file_row["filepath"]}\">{$file_row["filename"]}</a></td><td class=\"result size\">",number_format($file_row["size"] / 1048576, 2)," MB</td></tr>";
					else
						echo "<tr class=\"result odd\"><td class=\"result series\"><a href=\"{$series_row["path"]}\">{$series_row["title"]}</a></td><td class=\"result title\"><a href=\"{$file_row["filepath"]}\">{$file_row["filename"]}</a></td><td class=\"result size\">",number_format($file_row["size"] / 1048576, 2)," MB</td></tr>";
					$i++;
				}
			}
			echo "</table>";
			echo "<br>";
		}
		else 
			echo "<p>Your search for $title did not return anything.</p>";
}
?>
