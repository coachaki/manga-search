<?php
function get_all_db_series($db) {
	$result_array = array();

	$sql = "SELECT *
		FROM info_manga_table";
	$series_result = mysqli_query($db, $sql);
	while ($series_row = mysqli_fetch_array($series_result)) {
		$result_array[] = array(
			"title" => $series_row["title"],
			"author" => $series_row["author"],
			"path" => $series_row["path"],
			"completed" => $series_row["completed"],
			"last_updated" => $series_row["last_updated"],
			"table_name" => $series_row["table_name"]
		);
	}
	return $result_array;
}
?>
