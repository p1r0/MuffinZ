<?php
/**
 * Description of Text
 *
 * @author pyro
 */
class Bs_Form_Element_Text extends Zend_Form_Element_Text
{
    public function __construct($name, $options, $column = null, $length = 100)
    {
        $defOptions = array(
            'validators' => array(
                array('StringLength', false, array(1, $length)),
            ),
            'class' => 'inp-form'
        );
        
        $options = array_merge($defOptions, $options);
        parent::__construct($name, $options);
    }
}

?>
