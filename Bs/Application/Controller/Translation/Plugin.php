<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Plugin
 *
 * @author pyro
 */
class Bs_Application_Controller_Translation_Plugin extends Zend_Controller_Plugin_Abstract
{

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $defaultNamespace = new Zend_Session_Namespace('Default');
        $lang = $request->getParam('lang');
        //exit();
        if($lang == "")
        {
            if(isset($defaultNamespace->lang))
            {
                $lang = $defaultNamespace->lang;
            }
            else
            {
                $lang = 'en';
            }
            
            $url = new Bs_Http_Url();
            $url = $url->getCurrent();
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
            $url = "/".$lang.$url;
            
            $redirector->gotoUrlAndExit($url);
        }
        
        
        Zend_Registry::set('lang', $lang);
        $defaultNamespace->lang = $lang;
        
        $locale = new Zend_Locale($lang);
        Zend_Registry::set('Zend_Locale', $locale);

        $t = new Zend_Translate(
                        'gettext', //the adapter
                        APPLICATION_PATH . '/language', //where the lang files will be stored
                        'auto', //set to auto include .mo files
                        array('scan' => Zend_Translate::LOCALE_FILENAME) //set to scan lang files
        );

        if (!$t->isAvailable($locale->getLanguage())) 
        {
            // not available languages are rerouted to another language
            $t->setLocale('en');
            $request->setParam('lang', 'en');
        }
        else
        {
            $t->setLocale($lang);
        }
        
        Zend_Registry::set('Zend_Translate', $t);
        
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $router->setGlobalParam('lang', $lang);
    }

}

?>
