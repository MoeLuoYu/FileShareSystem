<?php
/*
 Author:AroRain(MoeLuoYu)
 This is free software,please abide by the MIT open source license when using it.
 Copyright (c) 2023-present Sodayo Beijing Co., Ltd (c) 2020-present MoeLuoYu
 $ id: FileShareSystem_download 2025-5-9 CST MoeLuoYu $
*/
include "config.php";

// 清除输出缓冲区
ob_clean();
flush();

function getFilePath(): string {
    global $fileDirectory;
    // 获取并返回文件目录的绝对路径
    return realpath($fileDirectory);
}

// 获取文件目录的绝对路径
$baseDir = getFilePath();
// 获取用户请求的路径
$requestedPath = $_REQUEST['path'];
// 规范化用户请求的路径
$requestedPath = str_replace("\\", "/", $requestedPath);
// 去除路径末尾可能的斜杠
if ($requestedPath!= "" && $requestedPath[strlen($requestedPath) - 1] == '/') {
    $requestedPath = substr($requestedPath, 0, strlen($requestedPath) - 1);
}
// 组合完整的文件路径
$fullPath = $baseDir. '/'. $requestedPath;
// 获取规范化后的绝对路径
$realPath = realpath($fullPath);
// 文件不存在返回信息
$errorMessage = "文件未找到, <a href=\"index.php\">点击返回主页</a>";

// 检查路径是否合法
if ($realPath === false || strpos($realPath, $baseDir)!== 0) {
    // 路径不合法或文件不存在，输出统一错误信息
    echo $errorMessage;
    exit;
}

if (file_exists($realPath)) {
    // 设置响应头，指示文件下载
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='. basename($realPath));
    header('Content-Length: '. filesize($realPath));
    // 输出文件内容
    readfile($realPath);
} else {
    // 文件不存在，输出统一错误信息
    echo $errorMessage;
}
?>
