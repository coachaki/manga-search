<html>
	<head>
		<title>Manga Search</title>
		<link rel="stylesheet" type="text/css" href="searchui.css" />
	</head>
	<body onload="filelist.php">
		<nav id="navbar">
		<span class="navtext">
			<span class="right" style="z-index: 1;">
				<a href="/manga/">directory</a> |
				<a href="/manga/new/">latest</a>
			</span>
			<form id="search_form" name="manga_search" action="search.php" method="post">
				<input class="input_field" id="search_field" type="text" name="keyword" autofocus placeholder="keyword search">
				<input class="buttons" id="nav_submit" type="submit" value="search">
			</form>
		</span>
		</nav>
		<div id="search_result">
			<?php
			date_default_timezone_set("America/Los_Angeles");
			$i = 0;
			$tomorrow = date("Y-m-d", mktime(0,0,0,date("m"), date("d")+1, date("Y")));
			$two_weeks_ago = date("Y-m-d", mktime(0,0,0,date("m"), date("d")-7, date("Y")));
			echo "tomorrow: $tomorrow and 2 weeks ago: $two_weeks_ago";
			$manga_db = (mysqli_connect("localhost","manga_search","","manga_db")) or die("error connecting to manga database");
			$sql =
				"select *
				from info_manga_table
				where last_updated between '$two_weeks_ago' and '$tomorrow'"; //and last_updated <= $tomorrow";
			$series_result = mysqli_query($manga_db,$sql);
			$recent_series = array();
			$recent_files = array();
			// echo "<pre>";
			echo "<table id=\"resultlist\" class=\"width_expand\">
				<tr class=\"label\"><td class=\"title\">title</td><td class=\"size\">size</td></tr>";
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
						echo "<tr class=\"even\"><td class=\"title\"><a href=\"{$file_row["filepath"]}\">{$file_row["filename"]}</a></td><td class=\"size\">",number_format($file_row["size"] / 1048576, 2),"</td></tr>";
					else
						echo "<tr class=\"odd\"><td class=\"title\"><a href=\"{$file_row["filepath"]}\">{$file_row["filename"]}</a></td><td class=\"size\">",number_format($file_row["size"] / 1048576, 2),"</td></tr>";
					$i++;
				}
			}

			// echo "<pre>",print_r($recent_series),"</pre>";
			// echo "<pre>",print_r($recent_files),"</pre>";
			// echo "</pre>";
			mysqli_close($manga_db);
			?>
			</table>
<!--
				<tr class="evenrow"><td class="title">placeholder</td><td class="size">43 MB</td></tr>
				<tr class="oddrow"><td class="title">placeholder</td><td class="size">53 MB</td></tr>
				</table>
-->
			
		</div>
	</body>
</html>
