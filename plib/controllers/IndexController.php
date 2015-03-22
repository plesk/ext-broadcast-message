<?php
// Copyright 1999-2015. Parallels IP Holdings GmbH. All Rights Reserved.
class IndexController extends pm_Controller_Action
{

    protected $_accessLevel = 'admin';

    public function indexAction()
    {
        $this->view->pageTitle = $this->lmsg('settingsPageTitle');

        $form = new Modules_BroadcastMessage_Form_Settings();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $form->process();
            $this->_status->addMessage('info', $this->lmsg('settingsSaved'));
            $this->_helper->json(array('redirect' => pm_Context::getModulesListUrl()));
        }

        $this->view->form = $form;
    }
}
