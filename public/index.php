<?php
/*
 Author:AroRain(MoeLuoYu)
 This is free software,please abide by the MIT open source license when using it.
 Copyright (c) 2023-present Sodayo Beijing Co., Ltd (c) 2020-present MoeLuoYu
 $ id: FileShareSystem_index 2026-1-14 CST MoeLuoYu $
*/
// 引入设置
include "../config.php";

// 检查是否有download参数，如果有则执行下载逻辑
if (isset($_REQUEST['download'])) {
    // 清除输出缓冲区
    ob_clean();
    flush();

    function getFilePath(): string {
        global $fileDirectory;
        // 获取并返回文件目录的绝对路径
        return realpath("../" . $fileDirectory);
    }

    // 获取文件目录的绝对路径
    $baseDir = getFilePath();
    // 获取用户请求的路径
    $requestedPath = $_REQUEST['download'];
    // 规范化用户请求的路径
    $requestedPath = str_replace("\\", "/", $requestedPath);
    // 去除路径末尾可能的斜杠
    if ($requestedPath != "" && $requestedPath[strlen($requestedPath) - 1] == '/') {
        $requestedPath = substr($requestedPath, 0, strlen($requestedPath) - 1);
    }
    // 组合完整的文件路径
    $fullPath = $baseDir . '/' . $requestedPath;
    // 获取规范化后的绝对路径
    $realPath = realpath($fullPath);
    // 文件不存在返回信息
    global $indexPage;
        $errorMessage = "文件未找到, <a href=\"" . $indexPage . "\">点击返回主页</a>";

    // 检查路径是否合法
    if ($realPath === false || strpos($realPath, $baseDir) !== 0) {
        // 路径不合法或文件不存在，输出统一错误信息
        echo $errorMessage;
        exit;
    }

    if (file_exists($realPath)) {
        // 设置响应头，指示文件下载
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($realPath));
        header('Content-Length: ' . filesize($realPath));
        // 输出文件内容
        readfile($realPath);
        exit; // 下载完成后退出
    } else {
        // 文件不存在，输出统一错误信息
        echo $errorMessage;
        exit;
    }
}

// 支持显示图标的文件拓展名
$fileExtensionIcon = [ "c", "i", "s", "o", "out", "cxx", "cc", "c++", "C", "cpp", "inl", "hpp", "hxx", "h++", "h", "cs", "aspx", "resx", "json", "md", "py", "pyo", "pyw", "pyc", "pyd", "php", "phps", "lua", "go", "sln", "ttf", "otf", "woff", "woff2", "eot", "apk", "xapk", "css", "less", "js", "exe", "log", "doc", "docx", "docm", "dot", "dotx", "dotm", "ppsx", "ppsm", "ppa", "ppam", "zip", "xml", "ini", "cfg", "config", "conf", "propreties", "ipa", "plist", "applescript", "ps1", "bat", "sh", "bash", "html", "htm", "dll", "lib", "txt", "gitignore", "mcpack", "mcaddon", "mcworld", "cer", "p12", "p7b", "pfx", "sst", ];

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
        "fileDirectory" => "../" . $fileDirectory,
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
$search = $_REQUEST['search'] ?? "";
$searchSubfolders = isset($_REQUEST['search_subfolders']);
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
    <link rel="stylesheet" href="assets/css/navbar.css">

    <!-- 根据是否有搜索结果来控制显示/隐藏的CSS -->
    <style>
        <?php if (!empty($search)): ?>
        /* 如果有搜索结果，隐藏原始列表 */
        #list {
            display: none !important;
        }
        <?php endif; ?>
    </style>
</head>

<body style="overflow: visible;background:#f6f6f6;">
    <?php include "assets/svg/icon.svg" ?>
    <div class="main-content-wrapper">
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
                    echo '<li><a href="' . $indexPage . '">Index of/</a></li>';
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

                            echo '<li><a href="' . $indexPage . '?dir=' . substr($dir, 0, $i - 1) . '">' . $d . '/</use></a></li>';
                        }
                        echo '<li><a style="margin-top:0.15em;color:#000;">' . $curDir . '</a></li>';
                    }
                    ?>
                </ul>
                <div class="search-container">
                    <button class="search-toggle" id="searchToggle" style="padding: 5px 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; display: none;">
                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <form id="searchForm" method="GET" action="">
                        <input type="hidden" name="dir" value="<?php echo htmlspecialchars($dir); ?>">
                        <div style="position: relative; display: inline-block;">
                            <input type="text" name="search" id="searchInput" placeholder="搜索文件..." style="padding: 5px 110px 5px 5px; border: 1px solid #ccc; border-radius: 4px; width: 250px;" value="<?php echo htmlspecialchars($search); ?>">
                            <label style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 12px; background: white; padding: 0 5px; display: flex; align-items: center;">
                                <input type="checkbox" name="search_subfolders" id="searchSubfolders" <?php echo $searchSubfolders ? 'checked' : ''; ?> style="vertical-align: middle; margin-right: 4px;"> 搜索子文件夹
                            </label>
                        </div>
                        <button type="submit" style="width: 35px; height: 35px; margin-left: 5px; padding: 5px 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>
                    </form>
                </div>
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
                // 添加返回上一级按钮
                if (!empty($dir)) {
                    $parentDir = dirname($dir);
                    $parentHref = $parentDir === '.' ? $indexPage : "$indexPage?dir=$parentDir";
                    
                    // 计算上级目录的路径
                    $parentPath = getConfig()->fileDirectory . $parentDir;
                    if ($parentDir === '.') {
                        $parentPath = getConfig()->fileDirectory;
                    }
                    
                    // 获取上级目录的修改时间和大小
                    $lastEditTimeStamp = filemtime($parentPath);
                    $lastEditTime = date("Y-m-d H:i:s", $lastEditTimeStamp);
                    $size = getFileSizeStr(getFolderSize($parentPath));
                    
                    echo '<li class="parent-dir-item">';
                    echo '<a href="' . htmlspecialchars($parentHref) . '" class="clearfix">';
                    echo '<div class="row">';
                    echo '<span class="file-name col-md-7 col-sm-6 col-xs-9">';
                    echo '<svg><use xlink:href="#Folder"/></svg>';
                    echo ' ..';
                    echo '</span>';
                    echo '<span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">';
                    echo '..';
                    //echo $size;
                    echo '</span>';
                    echo '<span class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">';
                    echo $lastEditTime;
                    echo '</span>';
                    echo '</div>';
                    echo '</a>';
                    echo '</li>';
                }
                ?>
                <?php
                function directory($cur)
                {
                    global $fileExtensionIcon, $indexPage;
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
                        $href = "$indexPage?dir=" . ($cur == "" ? $cur : "$cur/") . $d;
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
                        $href = "$indexPage?download=$cur/$file";
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
    <?php
    // 搜索功能
    if (!empty($search)) {
        echo '<section id="search-results" class="services-section spad">';
        echo '<div class="container">';
        echo '<h3>搜索结果: "' . htmlspecialchars($search) . '" (在' . ($searchSubfolders ? '当前目录及子目录' : '当前目录') . '中)</h3>';
        echo '<div id="dir-list-header">';
        echo '<div class="row" style="font-family:Consolas,sans-serif">';
        echo '<div class="file-name col-md-7 col-sm-6 col-xs-9">名称</div>';
        echo '<div class="file-size col-md-2 col-sm-2 col-xs-3 text-right">大小</div>';
        echo '<div class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">修改时间</div>';
        echo '</div>';
        echo '</div>';
        echo '<ul id="dir-list" class="nav nav-pills nav-stacked">';

        if ($searchSubfolders) {
            // 在当前目录及其子目录中搜索
            $results = searchFilesRecursive(getConfig()->fileDirectory . $dir, $search);
        } else {
            // 只在当前目录中搜索
            $results = searchFilesInDir(getConfig()->fileDirectory . $dir, $search);
        }

        if (empty($results)) {
            echo '<li><div class="not-found"><svg><use xlink:href="#Warning"/></svg><span>没有找到匹配的文件或文件夹</span></div></li>';
        } else {
            foreach ($results as $item) {
                $realPath = $item['path'];
                $name = $item['name'];
                $isDir = $item['isDir'];

                $lastEditTimeStamp = filemtime($realPath);
                $lastEditTime = date("Y-m-d H:i:s", $lastEditTimeStamp);
                $size = $isDir ? getFileSizeStr(getFolderSize($realPath)) : getFileSizeStr(filesize($realPath));

                if ($isDir) {
                    $relativePath = substr($realPath, strlen(getConfig()->fileDirectory));
                    $href = "' . $indexPage . '?dir=" . $relativePath;
                    echo '<li data-name="' . htmlspecialchars($name) . '" data-href="' . htmlspecialchars($href) . '">';
                    echo '<a href="' . htmlspecialchars($href) . '" class="clearfix" data-name="' . htmlspecialchars($name) . '">';
                    echo '<div class="row">';
                    echo '<span class="file-name col-md-7 col-sm-6 col-xs-9">';
                    echo '<svg><use xlink:href="#Folder"/></svg>';
                    echo $name;
                    echo '</span>';
                    echo '<span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">';
                    echo $size;
                    echo '</span>';
                    echo '<span class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">';
                    echo $lastEditTime;
                    echo '</span>';
                    echo '</div>';
                    echo '</a>';
                    echo '</li>';
                } else {
                    // 计算相对于基础目录的路径
                    $relativePath = substr(dirname($realPath), strlen(getConfig()->fileDirectory));
                    $fileName = basename($realPath);
                    $href = $indexPage . "?download=" . $relativePath . "/" . urlencode($fileName);

                    $extOri = mb_substr(mb_strrchr($name, '.'), 1);
                    $ext = mb_strtolower($extOri);
                    $icon = ".$ext";

                    if (!in_array($ext, $fileExtensionIcon)) {
                        $icon = "Unknown";
                    }

                    echo '<li data-name="' . htmlspecialchars($name) . '" data-href="' . htmlspecialchars($href) . '">';
                    echo '<a href="' . htmlspecialchars($href) . '" class="clearfix" data-name="' . htmlspecialchars($name) . '">';
                    echo '<div class="row">';
                    echo '<span class="file-name col-md-7 col-sm-6 col-xs-9">';
                    echo '<svg><use xlink:href="#' . $icon . '"/></svg>';
                    echo $name;
                    echo '</span>';
                    echo '<span class="file-size col-md-2 col-sm-2 col-xs-3 text-right">';
                    echo $size;
                    echo '</span>';
                    echo '<span class="last-edit-time col-md-3 col-sm-4 hidden-xs text-right">';
                    echo $lastEditTime;
                    echo '</span>';
                    echo '</div>';
                    echo '</a>';
                    echo '</li>';
                }
            }
        }

        echo '</ul>';
        echo '</div>';
        echo '</section>';
    }
    ?>
      <script>
          // 现在由CSS处理显示/隐藏，无需JavaScript干预
      </script>
      <script type="text/javascript" src="assets/js/jquery-3.4.1.min.js"></script>
      <script type="text/javascript" src="assets/js/jquery.nav.js"></script>
    </div> <!-- end main-content-wrapper -->
</body>

<?php
// 自动获取公安备案url中的备案号
$origingongan = $config->footer->gongan;
$gongancode = substr($origingongan, 15);

// 搜索函数：在指定目录中搜索（不递归）
function searchFilesInDir($dir, $searchTerm) {
    $results = [];
    $items = @scandir($dir);

    if ($items === false) {
        return $results;
    }

    $items = array_diff($items, [".", ".."]);

    foreach ($items as $item) {
        if (stripos($item, $searchTerm) !== false) {  // 不区分大小写的搜索
            $fullPath = $dir . '/' . $item;
            $results[] = [
                'name' => $item,
                'path' => $fullPath,
                'isDir' => is_dir($fullPath)
            ];
        }
    }

    return $results;
}

// 搜索函数：递归搜索子目录
function searchFilesRecursive($dir, $searchTerm) {
    $results = [];
    $items = @scandir($dir);

    if ($items === false) {
        return $results;
    }

    $items = array_diff($items, [".", ".."]);

    foreach ($items as $item) {
        $fullPath = $dir . '/' . $item;
        if (stripos($item, $searchTerm) !== false) {  // 不区分大小写的搜索
            $results[] = [
                'name' => $item,
                'path' => $fullPath,
                'isDir' => is_dir($fullPath)
            ];
        }

        // 如果是目录且开启了递归搜索，则递归搜索
        if (is_dir($fullPath)) {
            $subResults = searchFilesRecursive($fullPath, $searchTerm);
            $results = array_merge($results, $subResults);
        }
    }

    return $results;
}

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
        <p>Powered by <a href="https://github.com/MoeLuoYu/FileShareSystem" target="_blank">FileShareSystem</a> 2.1</p>
    
EOTE;
if ($hitokoto != false) {
    echo <<<hitokoto
        <p id="hitokoto">一言加载中...</p>
        <script>
            // 先清空占位文本，然后加载一言
            document.addEventListener('DOMContentLoaded', function() {
                fetch('https://v1.hitokoto.cn')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('hitokoto').textContent = data.hitokoto + ' —— ' + (data.from || '');
                    })
                    .catch(error => {
                        document.getElementById('hitokoto').textContent = '';
                    });
            });
        </script>
        hitokoto;
}
echo "</footer>";
?>
<!-- LINK-JS -->
<script lang="javascript" src="assets/js/script.js"></script>
</html>