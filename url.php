<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        #balloon {
            background: #333;
            font: 13px asap,arial;
            color: #fff;
            padding: 8px;
            text-align: center;
            border-radius: 3px;
            white-space: nowrap;
            margin: 4px 0 4px 4px;
        }
    </style>
    <title>URL Shortener</title>
</head>
<body>
    <div class="container">
        <h1 class="mt-5">URL Shortened</h1>
        <?php
function is_valid_domain_name($domain_name)
{
    return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name)
            && preg_match("/^.{1,253}$/", $domain_name)
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   );
}

function generateRandomString($length = 2) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$^&*()';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['url'];
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $url = isset($_GET['q']) ? $_GET['q'] : '';
}

if ($url === '') {
    echo '<p style="color: red;">Error: URL required!</p>';
    echo '        <button class="btn btn-primary" onclick="history.go(-1);">
        <i class="fas fa-arrow-left"></i> Turn back
    </button>';
    exit;
}

if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
    $url = "http://" . $url;
}

$folderName = generateRandomString();
$folderPath = './' . $folderName;

if (!is_valid_domain_name(parse_url($url, PHP_URL_HOST))) {
    echo '<p style="color: red;">Error: Invalid domain!</p>';
    echo '        <button class="btn btn-primary" onclick="history.go(-1);">
        <i class="fas fa-arrow-left"></i> Turn back
    </button>';
    exit;
}

if (strpos($url, '.') === false) {
    echo '<p style="color: red;">Error: URL is wrong!</p>';
    echo '        <button class="btn btn-primary" onclick="history.go(-1);">
        <i class="fas fa-arrow-left"></i> Turn back
    </button>';
    exit;
}

if (strlen($url) === 3) {
    echo '<p style="color: red;">Error: URL must be longer than 3 characters!</p>';
    echo '        <button class="btn btn-primary" onclick="history.go(-1);">
        <i class="fas fa-arrow-left"></i> Turn back
    </button>';
    exit;
}

while (file_exists($folderPath)) {
    $folderName = generateRandomString();
    $folderPath = './' . $folderName;
}

mkdir($folderPath, 0777, true);

$indexFile = fopen($folderPath . '/index.html', 'w');
$htmlContent = '<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <meta http-equiv="refresh" content="0;url=' . $url . '">
</head>
<body>
    <p>Redirecting...</p>
</body>
</html>';
fwrite($indexFile, $htmlContent);
fclose($indexFile);

$shortUrl = 'https://6.rf.gd/' . $folderName;

echo '<p>Shortened URL: <a href="' . $folderName . '">' . $shortUrl . '</a></p>';
echo '<button class="btn btn-primary" data-clipboard-text="' . $shortUrl . '" onclick="copyUrl()">
        <i class="far fa-copy"></i> Copy URL
    </button>';
?>
        <button class="btn btn-primary" onclick="history.go(-1);">
            <i class="fas fa-arrow-left"></i> Turn back
        </button>
        <div id="balloon" style="display: none;">URL Copied</div>
    </div>
    <script>
        function copyUrl() {
            var clipboard = new ClipboardJS('.btn');
            clipboard.on('success', function (e) {
                e.clearSelection();
                var balloon = document.getElementById('balloon');
                balloon.style.display = 'table';
                setTimeout(function () {
                    balloon.style.display = 'none';
                }, 2000);
            });
        }
    </script>
</body>
</html>
