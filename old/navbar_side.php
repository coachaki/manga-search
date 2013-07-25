<html>
	<head>
		<link rel="stylesheet" type="text/css" href="foldertree.css" />
	</head>
	<body>
		<?php
		include('mangadb_ui.php');
		$manga_db = mysqli_connect("localhost", "manga_search", "", "manga_db") or die("error connecting to manga database");
		$manga_series = get_all_db_series($manga_db);
		echo "<ul>";
		foreach($manga_series as $series) {
			echo "<li>"."<a href=\"{$series["path"]}\">{$series["title"]}</a></li>";
		}
		echo "</ul>";

		?>
	</body>
</html>
