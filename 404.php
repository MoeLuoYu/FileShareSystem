<?php
/*
 Author:AroRain(MoeLuoYu)
 This is free software,do not use it for business.
 $ id: FileShareSystem_404page 2023-2-28 CST MoeLuoYu $
*/
require "./include/config.php";
$lang = LANG;
require "./lang/{$lang}.php";
$notfound = notfound;
xhtml_head("404 Not Found");
echo "<div class=\"error\">{$notfound} - 404 Not Found</div>";
xhtml_footer();
?>
