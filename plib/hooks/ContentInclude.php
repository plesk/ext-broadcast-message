<?php
// Copyright 1999-2014. Parallels IP Holdings GmbH. All Rights Reserved.
class Modules_BroadcastMessage_ContentInclude extends pm_Hook_ContentInclude
{
    public function init()
    {
        if (pm_Session::isExist() && pm_Settings::get('enable') &&
            $message = pm_Settings::get('message')
        ) {
            $allowHtml = (bool)pm_Settings::get('allowHtml');

            if ('warning' == pm_Settings::get('type')) {
                pm_View_Status::addWarning($message, $allowHtml);
            } else {
                pm_View_Status::addInfo($message, $allowHtml);
            }
        }
    }

}
