<?php
// Copyright 1999-2026. WebPros International GmbH.

use PleskExt\BroadcastMessage\Helper\Expiration;

class Modules_BroadcastMessage_Form_Settings extends pm_Form_Simple
{

    public function init()
    {
        $this->addElement('checkbox', 'enable', array(
            'label' => $this->lmsg('fieldEnable'),
            'value' => (bool)pm_Settings::get('enable'),
        ));

        $this->addElement('checkbox', 'allowHtml', array(
            'label' => $this->lmsg('fieldAllowHtml'),
            'value' => (bool)pm_Settings::get('allowHtml'),
        ));

        $this->addElement('text', 'message', array(
            'label' => $this->lmsg('fieldMessage'),
            'value' => pm_Settings::get('message'),
            'class' => 'f-max-size',
        ));

        $this->addElement('select', 'type', array(
            'label' => $this->lmsg('fieldType'),
            'value' => pm_Settings::get('type', 'info'),
            'multiOptions' => [
                'info' => $this->lmsg('typeInfo'),
                'warning' => $this->lmsg('typeWarning'),
            ],
        ));

        $showUntilValidator = new Zend_Validate_Callback([Expiration::class, 'isValidFormat']);
        $showUntilValidator->setMessage(
            $this->lmsg('errorShowUntilInvalid'),
            Zend_Validate_Callback::INVALID_VALUE
        );
        $this->addElement('text', 'showUntil', array(
            'label' => $this->lmsg('fieldShowUntil'),
            'description' => $this->lmsg('fieldShowUntilDescription'),
            'value' => pm_Settings::get('showUntil'),
            'validators' => array($showUntilValidator),
        ));

        $this->addControlButtons(array(
            'cancelHidden' => true,
        ));
    }

    public function process()
    {
        $values = $this->getValues();
        pm_Settings::set('enable', (bool)$values['enable']);
        pm_Settings::set('allowHtml', (bool)$values['allowHtml']);
        pm_Settings::set('message', $values['message']);
        pm_Settings::set('type', $values['type']);
        pm_Settings::set('showUntil', trim($values['showUntil']));
    }

}
