<?php
if (isset($_POST['submit'])) {
	if (isset($_GET['title='])) {
		$title = $_POST['title'];
		$manga_db = mysqli_connect("localhost", "manga_search","","manga_db") or die("error connecting to manga database");

		$sql = "SELECT table_name
			FROM info_manga_table
			WHERE title LIKE \"%$title%\"";
		echo $sql;
		$result = mysqli_query($manga_db, $sql);
		while ($series_row = mysqli_fetch_array($result)) {
			$sql = "SELECT *
				FROM {$series_row["table_name"]}";
			$series_result = mysqli_query($manga_db, $sql);
			echo "<pre>", print_r($series_result), "</pre>";
		}
	}
}
?>
