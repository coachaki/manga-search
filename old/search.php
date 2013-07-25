<html>
	<head>
		<link rel="stylesheet" type="text/css" href="searchui.css" />
	</head>
	<body>
		<div id="search_result">
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
						echo "Your search result for: $title did not return anything.<br>";
				}
			}
			else {
				$i = 0;
				$tomorrow = date("Y-m-d", mktime(0,0,0,date("m"), date("d")+1, date("Y")));
				$two_weeks_ago = date("Y-m-d", mktime(0,0,0,date("m"), date("d")-7, date("Y")));
				// echo "tomorrow: $tomorrow and 2 weeks ago: $two_weeks_ago";
				$manga_db = (mysqli_connect("localhost","manga_search","","manga_db")) or die("error connecting to manga database");
				$sql =
					"select *
					from info_manga_table
					where last_updated between '$two_weeks_ago' and '$tomorrow'
					order by last_updated desc"; //and last_updated <= $tomorrow";
				$series_result = mysqli_query($manga_db,$sql);
				echo "<table id=\"resultlist\">
					<tr class=\"label\"><td class=\"series\">series</td><td class=\"title\">files</td><td class=\"size\">size</td></tr>";
				while($series_row = mysqli_fetch_array($series_result)) {
					$sql = 
						"select *
						from {$series_row["table_name"]}
						where last_updated between '$two_weeks_ago' and '$tomorrow'";
					$file_result = mysqli_query($manga_db, $sql);
					// echo "<pre>",print_r($file_result),"</pre>";
					if ($file_result->num_rows > 0) {
						$recent_series[] = $series_row["title"];
					}
					while($file_row = mysqli_fetch_array($file_result)) {
						$recent_files[] = array_merge($file_row,array("series" => $series_row["title"]));
						if ($i % 2 == 0)
							echo "<tr class=\"even\"><td class=\"series\"><a href=\"{$series_row["path"]}\">{$series_row["title"]}</a></td><td class=\"title\"><a href=\"{$file_row["filepath"]}\">{$file_row["filename"]}</a></td><td class=\"size\">",number_format($file_row["size"] / 1048576, 2)," MB</td></tr>";
						else
							echo "<tr class=\"odd\"><td class=\"series\"><a href=\"{$series_row["path"]}\">{$series_row["title"]}</a></td><td class=\"title\"><a href=\"{$file_row["filepath"]}\">{$file_row["filename"]}</a></td><td class=\"size\">",number_format($file_row["size"] / 1048576, 2)," MB</td></tr>";
						$i++;
					}
				}
				echo "</table>";
				echo "<br>";

				// echo "<pre>",print_r($recent_series),"</pre>";
				// echo "<pre>",print_r($recent_files),"</pre>";
				// echo "</pre>";
				mysqli_close($manga_db);
			}
			?>
		</div>
	</body>
</html>

