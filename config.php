
<?php
/*
 Author:AroRain(MoeLuoYu)
 This is free software,please abide by the MIT open source license when using it.
 Copyright (c) 2023-present Sodayo Beijing Co., Ltd (c) 2020-present MoeLuoYu
 $ id: FileShareSystem_config 2025-3-7 CST MoeLuoYu $
*/
// 系统信息
$osType = PHP_OS;  
// Navbar标题
$textOnTopLeft = 'FileShareSystem';
// 默认首页
$indexPage = 'index.php';
// head内容
$head = (object) [
    // 网站标题
    "title" => 'FileShareSystem 2.0',
    // 网站图标
    "icon" => 'assets/img/icon.ico',
    // SEO描述
    "description" => 'FileShareSystem',
    // SEO关键词
    "keywords" => 'FileShareSystem,文件分享系统,文件分享,文件下载'
];
// footer
$footer = (object) [
    // 版权文本
    "copyright" => '<p>Copyright&copy;' . date("Y") . ' <a href="https://github.com/MoeLuoYu/FileShareSystem" target="_blank">FileShareSystem</a> All Rights Reserved.</p>',
    // 工信部备案号 没有不用填
    "beian" => '',
    // 公安部备案号 没有不用填
    "gongan" => '',
    // 底部展示系统名称
    "name" => 'File Share System'
];
// MD格式支持
$enableMarkdown = true;
// 文件根目录
$fileDirectory = 'Filedir/';
// hitokoto一言开关
$hitokoto = true;
