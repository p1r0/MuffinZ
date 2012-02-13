<?php

/**
 * 
 * Form base for other BS forms.
 * @author yuri
 *
 */
class Bs_Form_BaseFormGeneric extends Bs_Form_BaseForm
{

    protected $_xml;
    public $table = "";

    public function __construct($table = "")
    {
        $this->table = $table;
        parent::__construct();
    }

    protected function loadXml()
    {
        $this->_xml = new SimpleXMLElement(file_get_contents(APPLICATION_PATH . '/configs/forms.xml'));
    }

    public function init()
    {
        parent::init();
        $this->loadXml();
        $registry = Zend_Registry::getInstance();
        $conf = $registry->get('site_config');
        $this->setMethod('post');

        $columns = $this->_xml->xpath("//tables/{$this->table}/columns/*");

        
        //$columns = $table[0]->xpath('columns');
        
        foreach($columns as $column)
        {
            if($column->ignore->__toString() != '1')
            {
                $name = $column->getName();
                $label = $column->label->__toString();
                $class = $column->class->__toString();
                $length = $column->length->__toString();
                $required = $column->required->__toString() == "1" ? true : false;
                $this->addElement(new $class($name, array(
                    'label' => $label,
                    'required' => $required,
                    'filters' => array('StringTrim')
                ), $column, $length));
            }
        }
        
        $this->addElement('submit', $this->_translate->_('Save'), array(
			'ignore'   		=> true,
			'description'	=> '<a href="javascript:history.go(-1);">'.$this->_translate->_('Cancel').'</a>',
			'decorators' 	=> array(
				array('ViewHelper'),
				array('Description', 	array('escape' => false, 'tag' => 'span', 'class'=>'element-cancel-link')),
				array('HtmlTag', 		array('tag' => 'p', 'class'=>'submit-group'))
			)
		));
    }
    
    

}

?>