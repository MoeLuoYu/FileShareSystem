<?php
/*
 Author:AroRain(MoeLuoYu)
 This is free software,do not use it for business.
 $ id: FileShareSystem_template 2022-4-11 CST MoeLuoYu $
*/
function xhtml_head()
{
    $name = NAME;
    $subname = SUBNAME;
    $icon = ICON;
    echo <<<XHTML
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>
			{$name} {$subname}
		</title>
		<link rel="shortcut icon" href="{$icon}" type="image/x-icon" />
		<!----(自定义CSS)---->
		<style>
			code { word-break: break-all; word-wrap: break-word; white-space: pre-wrap;
			width: auto; }
		</style>
		<link rel="stylesheet" href="./static/style/style.css" />
		<link rel="stylesheet" href="./static/highlight/styles/default.min.css">
		<script src="./static/highlight/highlight.min.js">
		</script>
		<script>
			hljs.highlightAll();
		</script>
		<!----(自定义头部HTML)---->
		<div>
		</div>
	</head>
	<body>
		<div id="header">
			{$name}
		</div>
	</body>
XHTML;
}

function xhtml_footer()
{
    $name = NAME;
    $urlsite = URLSITE;
    $cop = cop;
    $copyname = COPYNAME;
    $copyear = COPYEAR;
    $gitname = gitname;
    $opensrc = opensrc;
    $build = build;
    $author = author;
    echo <<<XHTML
	<footer>
		<div id="footer">
			&copy;{$copyear}
			<a href="{$urlsite}">
				{$copyname}
			</a>
			{$cop}
		</div>
		{$gitname} {$author} {$opensrc}
		<a href="https://github.com/MoeLuoYu/FileShareSystem/" target="_blank">
			FileShareSystem
		</a>
		{$build}
		<!----(自定义底部HTML)---->
		<div>
			<script type="text/javascript">
				var e = document.querySelectorAll("code");
var e_len = e.length;
var i;
for (i = 0; i < e_len; i++) {
    e[i].innerHTML = "<ul><li>" + e[i].innerHTML.replace(/\n/g, "\n</li><li>") + "\n</li></ul>";
}
			</script>
		</div>
	</footer>

</html>
XHTML;
}

?>
