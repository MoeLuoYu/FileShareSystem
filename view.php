<?php
/*
 Author:AroRain(MoeLuoYu)
 This is free software,do not use it for business.
 $ id: FileShareSystem_view 2023-2-28 CST MoeLuoYu $
*/
//import
require "./include/config.php";
$lang = LANG;
require "./lang/{$lang}.php";
$view = view;
$backdir = backdir;
$errtip = errtip;
$noreadprem = noreadprem;
$code = code;
$nom = nom;
$reopen = reopen;
$interview = interview;
$big = big;
$nullpoint = nullpoint;
//main
if (!isset($_GET['path'])) {
	header("Location: ./404.php");
	exit;
} elseif (($path = trim($_GET['path'])) == "") {
	header("Location: ./404.php");
	exit;
} elseif (!is_file($path)) {
	header("Location: ./404.php");
	exit;
} elseif (!is_readable($path)) {
	xhtml_head("{$view}");
	echo "<a href=\"./index.php?path=" . urlencode(dirname($path)) . "\"]>{$backdir}</a>\n";
	echo "<div class=\"like\">\n";
	echo "{$errtip}";
	echo "</div>\n";
	echo "<div class=\"rain\">\n";
	echo "{$noreadprem}";
	echo "</div>\n";
	xhtml_footer();
} else {
	xhtml_head("{$view}");
	echo "<a href=\"./index.php?path=" . urlencode(dirname($path)) . "\"]>{$backdir}</a>\n";
	echo "<div class=\"like\">\n";
	echo "{$code}\n";
	echo "</div>\n";
	echo "<div class=\"rain\">";
	echo "<form action=\"\" method=\"GET\">\n";
	echo "<select name=\"charset\">\n";
	if (!function_exists("mb_convert_encoding")) {
		echo "<option>{$nom}</option>\n";
	} else {
		$sencode = mb_list_encodings();
		usort($sencode, "___sortcmp");
		foreach ($sencode as $encode) {
			if ($encode == "pass") {
				continue;
			}
			if (function_exists("mb_encoding_aliases")) {
				$alias = mb_encoding_aliases($encode);
				echo "\n<optgroup label=\"$encode\">\n";
				echo "<option value=\"$encode\">$encode</option>\n";
				if (is_array($alias)) if (count($alias) >= 1) {
					usort($alias, "___sortcmp");
					foreach ($alias as $encodealias) {
						if ($encodealias == $encode) {
							continue;
						}
						echo "<option value=\"$encode\">$encodealias</option>\n";
					}
				}
				echo "</optgroup>\n";
			} else {
				echo "<option value=\"$encode\">$encode</option>\n";
			}
		}
	}
	echo "</select>\n";
	echo "<input type=\"hidden\" name=\"path\" value=\"$path\" />";
	echo "<input type=\"submit\" value=\"{$reopen}\" />\n";
	echo "</form>\n";
	echo "</div>\n";
	echo "<div class=\"like\">{$interview}</div>\n";
	if (filesize($path) > (2 * 1024 * 1024)) {
		echo "<div class=\"rain\">\n";
		echo "{$big}\n";
		echo "</div>\n";
	} else {
		echo "<div class=\"rain\">\n";
		if (!($data = file_get_contents($path))) {
			echo "{$nullpoint}\n";
		} else {
			echo "<pre><code class=\"auto\">";
			if (!isset($_GET['charset'])) {
				echo nl2br(___codepre(___convert($data, "UTF-8")));
			} elseif (($charset = trim($_GET['charset'])) == "") {
				echo nl2br(___codepre(___convert($data, "UTF-8")));
			} else {
				echo nl2br(___codepre(___convert($data, "UTF-8", $charset)));
			}
			echo "</code></pre>\n";
		}
		echo "</div>";
	}
	xhtml_footer();
}
?>
