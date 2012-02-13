<?php
/***
 * Plugin to reset search session values
 */
class Bs_Front_SearchPlugin extends Zend_Controller_Plugin_Abstract
{
 
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $controller = $request->getControllerName();
        if($controller != "busqueda")
        {
            $session = new Zend_Session_Namespace('search_widget');
            $session->unsetAll();
        }
    }
}
