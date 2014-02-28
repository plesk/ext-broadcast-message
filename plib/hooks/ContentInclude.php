<?php

class Modules_BroadcastMessage_ContentInclude extends pm_Hook_ContentInclude
{
    public function init()
    {
        if (pm_Session::isExist() && pm_Settings::get('enable') &&
            $message = pm_Settings::get('message')
        ) {
            if ('warning' == pm_Settings::get('type')) {
                pm_View_Status::addWarning($message);
            } else {
                pm_View_Status::addInfo($message);
            }
        }
    }

}
