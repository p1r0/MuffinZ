<?php 

/**
 * 
 * Form base for other BS forms.
 * @author yuri
 *
 */
class Bs_Form_BaseForm extends Zend_Form 
{
    protected $_translate;
    
    public function __construct()
    {
        $this->loadTranslate();
        parent::__construct();        
    }
    
    public function init()
    {
        $this->removeDefaultErrorDecorators();    
        $this->setErrorDecorator();
    }

    protected function removeDefaultErrorDecorators()
    {
        $elements = $this->getElements();
        foreach($elements as &$elem)
        {
            $elem->removeDecorator("Errors");
        }
    }
    
    protected function setErrorDecorator()
    {
        $errorDecorator = new Zend_Form_Decorator_FormErrors(array(
			'ignoreSubForms'=>true,
			'markupElementLabelEnd'=> '</b>',
			'markupElementLabelStart'=> '<b>',
			'markupListEnd' => '</div>',
			'markupListItemEnd'=>'</span>',
			'markupListItemStart'=>'<span>',
			'markupListStart'=>'<div class="forms-errors">',
            'placement' => 'prepend'));
        
        $this->setDecorators(array(
			'FormElements',
			$errorDecorator,
			'Form'));
    }
    
    protected function loadTranslate()
    {
        $registry = Zend_Registry::getInstance();
        $this->_translate = $registry->get("Zend_Translate");
    }
    
}
?>