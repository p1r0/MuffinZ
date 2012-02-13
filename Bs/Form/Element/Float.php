<?php
/**
 * Description of Text
 *
 * @author pyro
 */
class Bs_Form_Element_Float extends Zend_Form_Element_Text
{
    public function __construct($name, $options = array(), $column = null, $length = 100)
    {
        $defOptions = array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Float'
            ),
            'class' => 'inp-form'
        );
        
        $options = array_merge($defOptions, $options);
        parent::__construct($name, $options);
    }
}

?>
