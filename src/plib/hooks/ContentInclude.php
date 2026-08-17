<?php
// Copyright 1999-2026. WebPros International GmbH.

use PleskExt\BroadcastMessage\Helper\Expiration;
use PleskExt\BroadcastMessage\Helper\Sanitizer;

class Modules_BroadcastMessage_ContentInclude extends pm_Hook_ContentInclude
{
    public function init()
    {
        if (pm_Session::isExist() && pm_Settings::get('enable') &&
            !Expiration::isExpired(pm_Settings::get('showUntil')) &&
            $message = pm_Settings::get('message')
        ) {
            $allowHtml = (bool)pm_Settings::get('allowHtml');
            if ($allowHtml) {
                $message = Sanitizer::sanitize($message);
            }

            if ('warning' == pm_Settings::get('type')) {
                pm_View_Status::addWarning($message, $allowHtml);
            } else {
                pm_View_Status::addInfo($message, $allowHtml);
            }
        }
    }

}
