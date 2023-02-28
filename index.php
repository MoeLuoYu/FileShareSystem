<?php
/*
 Author:AroRain(MoeLuoYu)
 This is free software,do not use it for business.
 $ id: FileShareSystem_index 2023-2-28 CST MoeLuoYu $
*/
//import
require "./include/config.php";
$lang = LANG;
require "./lang/{$lang}.php";
$notperm = notperm;
$no = no;
$disksize = disksize;
$freesize = freesize;
$up = up;
$viewdir = viewdir;
$cancel = cancel;
$all = all;
$dirlist = dirlist;
$viewfile = viewfile;
$download = download;
$latest = latest;
$latestedit = latestedit;
$updatetime = updatetime;
$backtop = backtop;
//main
$path = isset($_GET['path']) ? trim($_GET['path']) : OPEN;
if ($path == "" || !is_dir($path)) $path = OPEN;
$filesystem = new filesystem($path);
xhtml_head();
echo "<div class=\"rain\">\n";
if (function_exists("disk_total_space") && function_exists("disk_free_space")) {
	echo "{$disksize}:" . ___filesize(disk_total_space($path)) . "&ensp;&ensp;{$freesize}:" . ___filesize(disk_free_space($path)) . "<br />";
}
echo "\n</div>\n";
if (($data = $filesystem->getpath()) === false) {
	echo "<div class=\"error\">{&notperm}</div>\n";
} elseif ($data === null) {
	echo "<div class=\"error\">{$no}</div>\n";
	echo "<div class=\"rain\">\n";
	echo "</form>\n";
	echo "</div>\n";
} else {
	$select = isset($_GET['select']) ? "checked " : null;
	echo "<div class=\"rain\">\n";
	echo "(<a href=\"?path=" . urlencode($path) . "&select$multiple\">{$all}</a>|<a href=\"?path=" . urlencode($path) . "$multiple\">{$cancel}</a>)\n";
	echo "</div>\n";
	echo "\n<div class=\"like\">{$dirlist}</div>\n";
	if (count($data[0]) != 0) {
		foreach ($data[0] as $tmp) {
			$filesystem->chpath($tmp);
			echo "<div class=\"rain\">\n";
			echo "<input type=\"checkbox\" name=\"flist[]\" value=\"" . urlencode($tmp) . "\" $select/>\n";
			echo "<a href=\"?path=" . urlencode($tmp) . "$multiple\">" . ___basename($tmp) . "</a>\n";
			echo "</div>\n";
		}
	}
	$fs = new filesystem($path);
	if (count($data[1]) != 0) {
		foreach ($data[1] as $tmp) {
			$filesystem->chpath($tmp);
			$iget = $filesystem->getpath();
			echo "<div class=\"rain\">\n";
			echo "<input type=\"checkbox\" name=\"flist[]\" value=\"" . urlencode($tmp) . "\" $select/>\n";
			echo "<a href=\"./file.php?path=" . urlencode($tmp) . "\">" . ___basename($tmp) . "</a>(" . ___filesize($iget['size']) . ")\n";
			echo "&nbsp&nbsp&nbsp";
			echo "<a href=\"./include/download.php?path=" . urlencode($tmp) . "\">{$download}</a>|";
			echo "<a href=\"./view.php?path=" . urlencode($tmp) . "\">{$viewfile}</a>";
			echo "</div>\n";
		}
	}
	echo "</form>\n";
}
xhtml_footer();
?>
