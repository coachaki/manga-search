<?php
	function list_files($dir) {

		$filelist = array();

		if (substr($dir, -1) != "/") $dir .= "/";

		$d = @dir($dir) or die ("list_files: failed opening $dir for reading");

		while (false !== ($entry = $d->read())) {

			if ($entry[0] == ".") continue;
			if ($entry[1] == "!") continue;
			if ($entry == "!English!") continue;

			if (is_dir("$dir$entry")) {
				$filelist = array_merge($filelist, list_files("$dir$entry"));
			}
			elseif (is_readable("$dir$entry")) {
				$filelist[] = array(
					"name" => "$dir$entry",
					"type" => filetype("$dir$entry"),
					"size" => filesize("$dir$entry"),
					"lastmod" => filemtime("$dir$entry"),
				);
			}
		}

		$d->close();

		return $filelist;
	}
?>

<?php
	$files = list_files("/manga/");
	print_r($files);
?>
