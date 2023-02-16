<?php
/*
 Author:AroRain(MoeLuoYu)
 This is free software,do not use it for business.
 $ id: FileShareSystem_config 2022-4-11 CST MoeLuoYu $
*/
//服务器会话
session_start();
//原作者名称 WARN:为保证原作者的权益请不要删除下面一段文字
define("author", "MoeLuoYu(AroRain落雨)");
//错误的屏蔽
error_reporting(0);
//时间戳修正
define("TIME", 8 * 3600);
//网站title图标(请填写网站目录相对路径)
define("ICON", "./favicon.ico");
//网站标题
define("NAME", "FileShareSystem");
//网站副标题 TIP:目前只能显示在title上，其他地方还没想好放哪
define("SUBNAME", "- Powered By FileShareSystem");
//网站底部版权
define("COPYNAME", "FileShareSystem");
//网站底部版权年份
define("COPYEAR", "2022");
//网站版权指向URL
define("URLSITE", "http://".$_SERVER['SERVER_NAME']);
//ICP备案信息（可不填）
define("icp", "");
//网站根目录
define("ROOT", dirname(__FILE__));
//网站文件目录
define("OPEN", ROOT . "/../files");
//网站语言 WARN:请不要删除前面的相对路径，否则可能会导致网站空白
define("LANG", "./lang/zh_cn.php");
//载入对象库
require ROOT . "/base.php";
//载入模板库
require ROOT . "/../template.php";
//载入函数库
require ROOT . "/functions.php";
//强制的编码
header("Content-Type:text/html;charset=UTF-8");
?>
