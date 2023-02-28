<?php
/*
 Author:AroRain(MoeLuoYu)
 This is free software,do not use it for business.
 $ id: FileShareSystem_template 2023-2-28 CST MoeLuoYu $
*/
function xhtml_head() {
$name = NAME;
$subname = SUBNAME;
$icon = ICON;
echo <<<XHTML
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  
  <head>
    <title>{$name} {$subname}</title>
    <link rel="shortcut icon" href="{$icon}" type="image/x-icon" />
    <!----(自定义CSS)---->
    <style>code { word-break: break-all; word-wrap: break-word; white-space: pre-wrap; width: auto; }</style>
    <link rel="stylesheet" href="./static/style/style.css" />
    <link rel="stylesheet" href="./static/highlight/styles/default.min.css">
    <script src="./static/highlight/highlight.min.js">
    </script>
    <script>hljs.highlightAll();</script>
    <!----(Custom Header HTML)---->
    <div>
    </div>
  </head>
  
  <body>
    <div id="header">{$name}</div>
  </body>
XHTML;
}
function xhtml_footer() {
$name = NAME;
$urlsite = URLSITE;
$host = $_SERVER['SERVER_NAME'];
$cop = cop;
$copyname = COPYNAME;
$copyear = COPYEAR;
$gitname = gitname;
$opensrc = opensrc;
$build = build;
$author = author;
$icp = icp;
$authortitle = authortitle;
$authorinfo = authorinfo;
echo <<<XHTML
<footer>
  <div id="footer">&copy;{$copyear}
    <a href="{$urlsite}">{$copyname}</a>{$cop}</div>{$gitname} {$author} {$opensrc}
  <a href="https://github.com/MoeLuoYu/FileShareSystem/" target="_blank">FileShareSystem</a>{$build}</br>
  <div style="width:150px;overflow: hidden;">
    <button onclick="onTileClick()">{$authortitle}</button>
    <div id="content" style="height:0px;background-color: trans;transition: height 0.2s;">{$authorinfo}
      <br>{$host}
      <br></div>
  </div>
  <script type="text/javascript">var content = document.getElementById("content");
    function onTileClick() {
      content.style.height = content.offsetHeight === 150 ? 0 + 'px': 150 + 'px';
    }</script>
  </br>
  </br>
  <a href="https://beian.miit.gov.cn/" target="_blank">{$icp}</a>
  <!----(Custom Footer HTML)---->
  <div>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6385529732033594" crossorigin="anonymous">
    </script>
    <script type="text/javascript">var e = document.querySelectorAll("code");
      var e_len = e.length;
      var i;
      for (i = 0; i < e_len; i++) {
        e[i].innerHTML = "<ul><li>" + e[i].innerHTML.replace(/\n/g, "\n</li><li>") + "\n</li></ul>";
      }</script>
  </div>
</footer>

</html>
XHTML;
}
?>
