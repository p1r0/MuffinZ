<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Translate
 *
 * @author pyro
 */
class Bs_View_Helper_Translate extends Zend_View_Helper_Abstract
{
    public function translate($text)
    {
        if(!Zend_Registry::isRegistered('Zend_Translate'))
        {
            throw new Bs_View_Helper_Exception("No translation object found.");
        }
        
        $translator = Zend_Registry::get('Zend_Translate');
        
        return $translator->_($text);
    }
}

?>
