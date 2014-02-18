<?php

class Modules_BroadcastMessage_ContentInclude
{

    public function init()
    {
        pm_View_Status::addInfo('info message');
    }

    public function getJsConfig()
    {
        return [
            'plesk-version' => '12.0',
        ];
    }

    public function getJsOnReadyContent()
    {
        return 'PleskExt.BroadcastMessage.init();';
    }

    public function getJsContent()
    {
        return '// example';
    }

    public function getHeadContent()
    {
        return '<!-- some content inside head tag -->';
    }

}
