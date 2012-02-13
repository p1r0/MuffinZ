<?php
/**
 * Description of Text
 *
 * @author pyro
 */
class Bs_Form_Element_Image extends Zend_Form_Element_File
{
    protected $_imgPath = null;
    
    public function __construct($name, $options, $column = null, $length = 100)
    {
        $registry = Zend_Registry::getInstance();
        $conf = $registry->get('site_config');
        
        parent::__construct($name);
        
        $this->setLabel($options['label'])
                ->addFilter('Rename',
                            array('source' => $this->file,
                            'target' => $conf['img_uploaddir'].DIRECTORY_SEPARATOR.uniqid(),
                            'overwrite' => true));
        $this->addDecorator('Description', array('tag' => 'div', 'class' => 'description image-preview', 'escape' => false));
        // ensure only 1 file
        $this->addValidator('Count', false, 1);
        // only JPEG, PNG, and GIFs
        $this->addValidator('Extension', false, 'jpg,jpeg,png,gif');
    }
    
    public function render(Zend_View_Interface $view = null)
    {
        $registry = Zend_Registry::getInstance();
        $conf = $registry->get('site_config');
        
        if($this->_imgPath != "")
        {
            $this->setDescription('<br /><a target="_blank" href="'.$conf['img_pubdir'].DIRECTORY_SEPARATOR.$this->_imgPath.'"><img src="'.$conf['img_pubdir'].DIRECTORY_SEPARATOR.'th_'.$this->_imgPath.'" /></a>');
        }
        return parent::render($view);
    }
    
    public function setValue($value)
    {
        $this->_imgPath = $value;
        return parent::setValue($value);
    }
}

?>
