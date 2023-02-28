<?php
/*
 Author:AroRain(MoeLuoYu)
 This is free software,do not use it for business.
 $ id: FileShareSystem_file 2023-2-28 CST MoeLuoYu $
*/
//import
require "./include/config.php";
$lang = LANG;
require "./lang/{$lang}.php";
$fileinfo = fileinfo;
$backdir = backdir;
$filename = filename;
$sizefile = sizefile;
$mime = mime;
$latest = latest;
$latestedit = latestedit;
$md5hash = md5hash;
$md5value = md5value;
$sha1value = sha1value;
//main
if (!isset($_GET['path'])) {
	header("Location: ./404.php");
	exit;
} elseif (is_dir($path = ___realpath(trim($_GET['path'])))) {
	header("Location: ./404.php");
	exit;
}
$fs = new filesystem($path);
xhtml_head(___shortpath($path));
if (!($data = $fs->getpath($path))) {
	echo "<div class=\"error\">\n";
	echo "[<a href=\"javascript:;\" onClick=\"javascript :history.back(-1);\">{$backdir}</a>]\n";
	echo "</div>\n";
} else {
	echo "<a href=\"javascript:;\" onClick=\"javascript :history.back(-1);\">{$backdir}</a>\n";
	echo "<div class=\"like\">\n";
	echo "{$fileinfo}\n";
	echo "</div>\n";
	echo "<div class=\"rain\">\n";
	echo "{$filename}：" . ___basename($path) . "<br />\n";
	echo "{$sizefile}：" . ___filesize($data['size']) . "<br />\n";
	if (function_exists("mime_content_type")) echo "{$mime}：" . mime_content_type($path) . "<br />\n";
	echo "{$latest}：" . gmdate("Y-m-d H:i:s", ($data['atime']) + TIME) . "<br />\n";
	echo "{$latestedit}：" . gmdate("Y-m-d H:i:s", ($data['mtime']) + TIME) . "<br />\n";
	echo "</div>\n";
	echo "<div class=\"like\">\n{$md5hash}\n</div>\n";
	echo "<div class=\"rain\">\n";
	echo "md5：";
	if (isset($_GET['md5'])) {
		echo "<br />" . md5_file($path);
	} else {
		echo "<a href=\"./file.php?{$_SERVER['QUERY_STRING']}&md5\">{$md5value}</a>\n";
	}
	echo "</div>\n";
	echo "<div class=\"rain\">\n";
	echo "sha1：";
	if (isset($_GET['sha1'])) {
		echo "<br />" . sha1_file($path);
	} else {
		echo "<a href=\"./file.php?{$_SERVER['QUERY_STRING']}&sha1\">{$sha1value}</a>\n";
	}
	echo "</div>\n";
}
xhtml_footer();
?>
