<?php

class Modules_BroadcastMessage_Form_Settings extends pm_Form_Simple
{

    public function init()
    {
        $this->addElement('checkbox', 'enable', array(
            'label' => $this->lmsg('fieldEnable'),
            'value' => (bool)pm_Settings::get('enable'),
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

        $this->addControlButtons(array(
            'cancelHidden' => true,
        ));
    }

    public function process()
    {
        $values = $this->getValues();
        pm_Settings::set('enable', (bool)$values['enable']);
        pm_Settings::set('message', $values['message']);
        pm_Settings::set('type', $values['type']);
    }

}
