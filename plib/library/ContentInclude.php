<?php

class Modules_BroadcastMessage_ContentInclude
{

    public static function init()
    {
        if (pm_Session::isExist()) {
            pm_View_Status::addInfo('info message');
        }
    }

    public static function getJsConfig()
    {
        return [
            'plesk-version' => '12.0',
        ];
    }

    public static function getJsOnReadyContent()
    {
        return 'PleskExt.BroadcastMessage.init();';
    }

    public static function getJsContent()
    {
        return '// example';
    }

    public static function getHeadContent()
    {
        return '<!-- some content inside head tag -->';
    }

}
