<?php
/*
 Author:AroRain(MoeLuoYu)
 This is free software,please abide by the MIT open source license when using it.
 Copyright (c) 2023-present Sodayo Beijing Co., Ltd (c) 2020-present MoeLuoYu
 $ id: FileShareSystem_preview 2025-3-7 CST MoeLuoYu $
*/
include "config.php";

function getIconPath(): string
{
    global $head;
    return $head->icon;
}

function getTextOnTopLeft(): string
{
    global $textOnTopLeft;
    return $textOnTopLeft;
}

function getFileDir(): string
{
    global $fileDirectory;
    // 获取文件目录的绝对路径
    return realpath($fileDirectory);
}

function getFullHostName(): string
{
    $result = "http://";
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        $result = "https://";
    } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $result = "https://";
    } else if (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        $result = "https://";
    }
    return $result . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . '/';
}

$path = $_REQUEST["path"] ?? null;
// 规范化用户输入的路径
$path = str_replace("\\", "/", $path);
if ($path != "" && $path[strlen($path) - 1] == '/') {
    $path = substr($path, 0, strlen($path) - 1);
}

// 获取文件目录的绝对路径
$baseDir = getFileDir();
// 组合完整的文件路径
$fullPath = $baseDir . '/' . $path;
// 获取规范化后的绝对路径
$realPath = realpath($fullPath);

// 检查路径是否合法
$isPathInvalid = ($realPath === false || strpos($realPath, $baseDir) !== 0);

?>
<html lang="zh-CN">

<head>
    <!-- META -->
    <meta charset="UTF-8" />
    <meta name="description" content="Preview file" />
    <meta name="author" content="JasonZYT" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- TITLE -->
    <title>Preview</title>
    <!-- ICON -->
    <link rel="shortcut icon" href="<?php echo getIconPath(); ?>" />
    <link rel="bookmark" href="<?php echo getIconPath(); ?>" />
    <!-- LINK-CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font.css">
    <link rel="stylesheet" href="assets/css/style.min.css">
</head>

<body>
    <?php include "assets/svg/icon.svg"; ?>
    <nav id="navbar" style="display:block;">
        <div class="row">
            <div class="container">
                <div class="logo unit">
                    <span>
                        <a href="<?php echo $indexPage ?>" style="color: #000;text-decoration: none;"><?php echo $textOnTopLeft; ?></a>
                    </span>
                </div>
                <ul class="nav-menu">
                    <?php
                    // Path on the top
                    if ($path != "") {
                        if ($osType != 'WINNT'){
                            echo '<li><a href="/'. $indexPage .'">Index of/</a></li>';
                        } else {
                            echo '<li><a href="/'. $indexPage .'">主页<svg><use xlink:href="#AngleBracket-R" /></svg></a></li>';
                        }
                    } else {
                        if ($osType != 'WINNT'){
                            echo '<li><a style="margin-top:0.15em;color:#000;">Index of/</a></li>';
                        } else {
                            echo '<li><a style="margin-top:0.15em;color:#000;">主页</a></li>';
                        }
                    }
                    $dirs = explode("/", $path);
                    $curDir = $dirs[count($dirs) - 1];
                    array_pop($dirs);
                    $i = 0;
                    if ($curDir != "") {
                        foreach ($dirs as $d) {
                            if ($d == "") {
                                continue;
                            }
                            $i += strlen($d) + 1;
                            if ($osType != 'WINNT'){
                                echo '<li><a href="/index.php?dir=' . substr($path, 0, $i - 1) . '">' . $d . '/</use></a></li>';
                            } else {
                                echo '<li><a href="/index.php?dir=' . substr($path, 0, $i - 1) . '">' . $d . '<svg><use xlink:href="#AngleBracket-R"></use></svg></a></li>';
                            }
                        }
                        echo '<li><a style="margin-top:0.15em;color:#000;">' . $curDir . '</a></li>';
                    }
                    ?>
                </ul>
                <div class="download">
                    <a href="download.php?path=<?php echo $path; ?>">
                        <svg>
                            <use xlink:href="#Download" />
                        </svg><span>下载此文件</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <section class="services-section spad">
        <?php
        if ($isPathInvalid || $path == "" || !file_exists($realPath) || is_dir($realPath)) {
            $errorMessage = ($isPathInvalid) ? "非法路径访问" : "文件未找到";
            echo <<<EOT
        <div class="not-found">
            <svg><use xlink:href="#Warning"/></svg>
            <span>{$errorMessage}: <b>$path</b></span>
        </div>
EOT;
            goto footer;
        }
        ?>
        <iframe src="http://www.xdocin.com/xdoc?_func=to&_format=html&_cache=1&_xdoc=<?php echo getFullHostName() . $realPath; ?>&embedded=true" style="width:100%;height:100%;"></iframe>
    </section>
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
echo $config->footer->name;
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