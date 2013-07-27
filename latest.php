<?php
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
echo "<table class=\"result\" id=\"resultlist\">
	<tr class=\"result label\"><td class=\"result series\">series</td><td class=\"result title\">files</td><td class=\"result size\">size</td></tr>";
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
			echo "<tr class=\"result even\">
			<td class=\"result series\"><a href=\"{$series_row["path"]}\">{$series_row["title"]}</a></td>
			<td class=\"result title\"><a href=\"{$file_row["filepath"]}\">{$file_row["filename"]}</a></td>
			<td class=\"result size\">",number_format($file_row["size"] / 1048576, 2)," MB</td></tr>";
		else
			echo "<tr class=\"result odd\">
			<td class=\"result series\"><a href=\"{$series_row["path"]}\">{$series_row["title"]}</a></td>
			<td class=\"result title\"><a href=\"{$file_row["filepath"]}\">{$file_row["filename"]}</a></td>
			<td class=\"result size\">",number_format($file_row["size"] / 1048576, 2)," MB</td></tr>";
		$i++;
	}
}
echo "</table>";
echo "<br>";

// echo "<pre>",print_r($recent_series),"</pre>";
// echo "<pre>",print_r($recent_files),"</pre>";
// echo "</pre>";
mysqli_close($manga_db);
?>
