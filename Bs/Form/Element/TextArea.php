<?php

class Bs_Form_Element_TextArea extends Zend_Form_Element_Textarea
{
    public function __construct($name, $options, $column = null, $length = 100)
    {
        $defOptions = array(
            'validators' => array(
                array('StringLength', false, array(1, $length)),
            ),
            'class' => 'form-textarea'
        );
        
        $options = array_merge($defOptions, $options);
        parent::__construct($name, $options);
    }
}
?>
