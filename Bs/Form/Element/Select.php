<?php

class Bs_Form_Element_Select extends Zend_Form_Element_Select
{
    public function __construct($name, $options, $column = null)
    {
        parent::__construct($name, $options);
        
        $list = array();
        
        if(isset($column->extra->data))
        {
            foreach($column->extra->data->option as $opt)
            {
                $ar = explode(",", $opt);
                $list[$ar[0]] = $ar[1];
            }
        }
        else if(isset($column->extra->origin))
        {
            $loader = isset($column->extra->loader) ? $column->extra->loader : 'getList';
            $class = $column->extra->origin->__toString();

            $list = call_user_func("$class::$loader");
        }
        
        $this->addMultiOptions($list);
    }
}

?>
