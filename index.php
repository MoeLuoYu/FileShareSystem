<?php
/*
 Author:AroRain(MoeLuoYu)
 This is free software,please abide by the MIT open source license when using it.
 Copyright (c) 2023-present Sodayo Beijing Co., Ltd (c) 2020-present MoeLuoYu
 $ id: FileShareSystem_index 2025-3-7 CST MoeLuoYu $
*/
// 引入设置
include "config.php";
// 支持显示图标的文件拓展名
$fileExtensionIcon = [ "c", "i", "s", "o", "out", "cxx", "cc", "c++", "C", "cpp", "inl", "hpp", "hxx", "h++", "h", "cs", "aspx", "resx", "json", "md", "py", "pyo", "pyw", "pyc", "pyd", "php", "phps", "lua", "go", "sln", "ttf", "otf", "woff", "woff2", "eot", "apk", "xapk", "css", "less", "js", "exe", "log", "doc", "docx", "docm", "dot", "dotx", "dotm", "jpg", "png", "jpeg", "bmp", "gif", "tif", "pcx", "tga", "exif", "fpx", "ai", "raw", "webp", "pdf", "ppt", "pptx", "pptm", "potx", "potm", "pot", "ppsx", "ppsm", "ppa", "ppam", "zip", "xml", "ini", "cfg", "config", "conf", "propreties", "ipa", "plist", "applescript", "ps1", "bat", "sh", "bash", "html", "htm", "dll", "lib", "txt", "gitignore", "mcpack", "mcaddon", "mcworld", "cer", "p12", "p7b", "pfx", "sst", ];

function getConfig(): object
{
    global $head;
    global $footer;
    global $enableMarkdown;
    global $textOnTopLeft;
    global $fileDirectory;
    global $debug;
    return (object) [
        "head" => $head,
        "footer" => $footer,
        "enableMarkdown" => $enableMarkdown,
        "textOnTopLeft" => $textOnTopLeft,
        "fileDirectory" => $fileDirectory,
        "debug" => $debug
    ];
}


function getWebsiteTitle($uri): ?string
{
    $h = curl_init();
    curl_setopt($h, CURLOPT_URL, $uri);
    curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($h, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($h, CURLOPT_MAXREDIRS, 10);
    curl_setopt($h, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($h, CURLOPT_TIMEOUT, 10);
    curl_setopt($h, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36");
    curl_setopt($h, CURLOPT_SSL_VERIFYPEER, false);
    $content = curl_exec($h);
    curl_close($h);
    if (mb_strpos($content, "<title>") !== false) {
        $title = mb_substr($content, mb_strpos($content, "<title>") + 7);
        return mb_substr($title, 0, mb_strpos($title, "</title>"));
    }
    return null;
}

function getLine($file, $line, $length = 4096)
{
    $returnTxt = null;
    $i = 1;
    $handle = @fopen($file, "r");
    if ($handle) {
        while (!feof($handle)) {
            $buffer = fgets($handle, $length);
            if ($line == $i) $returnTxt = $buffer;
            $i++;
        }
        fclose($handle);
    }
    return $returnTxt;
}

function getFileSizeStr($fileSize): string
{
    if ($fileSize >= 1024 && $fileSize < 1048576) {
        return round($fileSize / 1024, 2) . "KB";
    } else if ($fileSize >= 1048576 && $fileSize < 1073741824) {
        return round($fileSize / 1048576, 2) . "MB";
    } else if ($fileSize >= 1073741824  && $fileSize < 1099511627776) {
        return round($fileSize / 1073741824, 2) . "GB";
    } else {
        return $fileSize . "B";
    }
    if ($fileSize = null) {
        return "N/A";
    }
}

function getFolderSize($path): int
{
    $result = 0;
    $files = glob($path . "/*");
    foreach ($files as $file) {
        if (is_dir($file)) {
            $result += getFolderSize($file);
        } else {
            $result += filesize($file);
        }
    }
    return $result;
}

// ##########################  主脚本  ########################## //
// 设置时区
date_default_timezone_set("Asia/Shanghai");
// 获取配置
$config = getConfig();
// 请求参数
$dir = $_REQUEST['dir'] ?? "";
$dir = str_replace("\\", "/", $dir);
if ($dir != "" && $dir[mb_strlen($dir) - 1] == '/') {
    $dir = mb_substr($dir, 0, mb_strlen($dir) - 1);
}

// 规范化路径并检查是否在允许的目录范围内
$baseDir = realpath(getConfig()->fileDirectory);
$requestedDir = realpath($baseDir . '/' . $dir);
if ($requestedDir === false || strpos($requestedDir, $baseDir) !== 0) {
    $dirNotFound = true;
} else {
    $dir = substr($requestedDir, strlen($baseDir) + 1);
    $dirNotFound = false;
}
?>
<html lang="zh-CN">

<head>
    <!-- META -->
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo $config->head->description; ?>" />
    <meta name="keywords" content="<?php echo $config->head->keywords; ?>" />
    <meta name="author" content="MoeLuoYu" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- TITLE -->
    <title>
        <?php echo $config->head->title; ?>
    </title>
    <!-- ICON -->
    <link rel="shortcut icon" href="<?php echo $config->head->icon; ?>" />
    <link rel="bookmark" href="<?php echo $config->head->icon; ?>" />
    <!-- LINK-CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font.css">
    <link rel="stylesheet" href="assets/css/style.min.css">
</head>

<body style="overflow: visible;background:#f6f6f6;">
    <?php include "assets/svg/icon.svg" ?>
    <nav id="navbar" style="display:block;">
        <div class="row">
            <div class="container">
                <div class="logo unit">
                    <span>
                        <a href="<?php echo $indexPage ?>" style="color: #000;text-decoration: none;"><?php echo $config->textOnTopLeft; ?></a>
                    </span>
                </div>
                <ul class="nav-menu">
                    <?php
                    // Path on the top
                    if ($dir != "") {
                        if ($osType != 'WINNT') {
                            echo '<li><a href="/' . $indexPage . '">Index of/</a></li>';
                        } else {
                            echo '<li><a href="/' . $indexPage . '">主页<svg><use xlink:href="#AngleBracket-R" /></svg></a></li>';
                        }
                    } else {
                        if ($osType != 'WINNT') {
                            echo '<li><a style="margin-top:0.15em;color:#000;">Index of/</a></li>';
                        } else {
                            echo '<li><a style="margin-top:0.15em;color:#000;">主页</a></li>';
                        }
                    }
                    $dirs = explode("/", $dir);
                    $curDir = $dirs[count($dirs) - 1];
                    array_pop($dirs);
                    $i = 0;
                    if ($curDir != "") {
                        foreach ($dirs as $d) {
                            if ($d == "") {
                                continue;
                            }
                            $i += strlen($d) + 1;

                            if ($osType != 'WINNT') {
                                echo '<li><a href="/index.php?dir=' . substr($dir, 0, $i - 1) . '">' . $d . '/</use></a></li>';
                            } else {
                                echo '<li><a href="/index.php?dir=' . substr($dir, 0, $i - 1) . '">' . $d . '<svg><use xlink:href="#AngleBracket-R"></use></svg></a></li>';
                            }
                        }
                        echo '<li><a style="margin-top:0.15em;color:#000;">' . $curDir . '</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
    <section id="list" class="services-section spad">
        <?php
        $path = getConfig()->fileDirectory . $dir;
        if ($dirNotFound || ($dir != "" && (!file_exists($path) || !is_dir($path)))) {
            if ($dirNotFound) {
            echo <<<EOT
            <div class="not-found">
                <svg><use xlink:href="#Warning"/></svg>
                <span>
                404 Not Found!  
                </span>
            </div>
            EOT;
            }
            goto footer;
        }
        ?>
        <div class="container">
            <div id="dir-list-header">
                <div class="row" style="font-family:Consolas,sans-serif">
                    <div class="file-name col-md-7 col-sm-6 col-xs-9">名称</div>
                    <div class="file-size col-md-2 col-sm-2 col-xs-3 text-right">大小</div>
                    <div class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">修改时间</div>
                </div>
            </div>
            <ul id="dir-list" class="nav nav-pills nav-stacked">
                <?php
                function directory($cur)
                {
                    global $fileExtensionIcon;
                    $path = getConfig()->fileDirectory . $cur;
                    // 尝试调用 scandir 函数
                    $all = @scandir($path);
                    // 检查 scandir 是否返回 false
                    if ($all === false) {
                        echo <<<EOT
                            <div class="not-found">
                                <svg><use xlink:href="#Warning"/></svg>
                                <span>
                                    无法访问该目录，可能是权限不足!
                                </span>
                            </div>
                        EOT;
                        return;
                    }
                    // 排除 . 和 ..
                    $all = array_diff($all, [".", ".."]);
                    // 添加以下代码，检查文件夹是否为空
                    if (empty($all)) {
                    echo <<<EOT
                        <div class="not-found">
                            <svg><use xlink:href="#Warning"/></svg>
                            <span>
                                该文件夹为空!
                            </span>
                        </div>
                    EOT;
                        return;
                    }
                    $dirs = [];
                    $files = [];
                    foreach ($all as $file) {
                        $filePath = $path . "/" . $file;
                        if (is_dir($filePath)) {
                            $dirs[] = $file;
                        } else {
                            $files[] = $file;
                        }
                    }
                    foreach ($dirs as $d) {
                        $realPath = $path . '/' . $d;
                        $lastEditTimeStamp = filemtime($realPath);
                        $lastEditTime = date("Y-m-d H:i:s", $lastEditTimeStamp);
                        $size = getFileSizeStr(getFolderSize($realPath));
                        $href = "index.php?dir=" . ($cur == "" ? $cur : "$cur/") . $d;
                        echo <<<EOT
                            <li data-name="$d" data-href="$href">
                                <a href="$href" class="clearfix" data-name="$d">
                                    <div class="row">
                                        <span class="file-name col-md-7 col-sm-6 col-xs-9">
                                            <svg><use xlink:href="#Folder"/></svg>
                                            $d
                                        </span>
                                        <span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">
                                            $size
                                        </span>
                                        <span class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">
                                            $lastEditTime
                                        </span>
                                    </div>
                                </a>
                            </li>
                        EOT;
                    }
                    foreach ($files as $file) {
                        $realPath = $path . '/' . $file;
                        $lastEditTimeStamp = filemtime($realPath);
                        $lastEditTime = date("Y-m-d H:i:s", $lastEditTimeStamp);
                        $size = getFileSizeStr(filesize($realPath));
                        $extOri = mb_substr(mb_strrchr($file, '.'), 1);
                        $ext = mb_strtolower($extOri);
                        $name = $file;
                        $href = "download.php?path=$cur/$file";
                        $icon = ".$ext";
                        if ($ext == "url") {
                            $uri = getLine($realPath, 1);
                            $displayName = getLine($realPath, 2);
                            if ($displayName == null) {
                                $uriArray = parse_url($uri);
                                $title = getWebsiteTitle($uri);
                                $displayUri = $uriArray["host"] . $uriArray["path"];
                                if ($title) {
                                    $displayUri .= ':' . $title;
                                }
                                $name = $displayUri;
                            } else {
                                $name = $displayName;
                            }
                            $href = $uri;
                        }

                        if (!in_array($ext, $fileExtensionIcon)) {
                            $icon = "Unknown";
                        }
                        echo <<<EOT
                            <li data-name="$name" data-href="$href">
                                <a href="$href" class="clearfix" data-name="$name">
                                    <div class="row">
                                        <span class="file-name col-md-7 col-sm-6 col-xs-9">
                                            <svg><use xlink:href="#$icon"/></svg>
                                            $name
                                        </span>
                                        <span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">
                                            $size
                                        </span>
                                        <span class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">
                                            $lastEditTime
                                        </span>
                                    </div>
                                </a>
                            </li>
                        EOT;
                    }
                }
                directory($dir);
                ?>
            </ul>
        </div>
    </section>
    <script type="text/javascript" src="assets/js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.nav.js"></script>

</body>

<?php
// 自动获取公安备案url中的备案号
$origingongan = $config->footer->gongan;
$gongancode = substr($origingongan, 15);

footer:
echo <<<EOT
    <footer>
EOT;
echo $config->footer->copyright;
if ($config->footer->beian != null) {
    echo '<p><a href="https://beian.miit.gov.cn/" target="_blank">' . $config->footer->beian . '</a></p>';
}
if ($config->footer->gongan != null) {
    echo '<p><img src="https://beian.mps.gov.cn//web/assets/logo01.6189a29f.png" height="16" width="16"><a href="https://beian.mps.gov.cn/#/query/webSearch?code=' . $gongancode . '" rel="noreferrer" target="_blank">' . $config->footer->gongan . '</a></p>';
}
echo '<p>' . $config->footer->name . '</p>';
echo <<<EOTE
        <p>Powered by <a href="https://github.com/MoeLuoYu/FileShareSystem" target="_blank">FileShareSystem</a> 2.0</p>
    
EOTE;
if ($hitokoto != false) {
    echo <<<hitokoto
        <p id="hitokoto"></p>
        <script src="https://v1.hitokoto.cn/?encode=js&amp;select=%23hitokoto" defer=""></script>
        hitokoto;
}
echo "</footer>";
?>

</html>
