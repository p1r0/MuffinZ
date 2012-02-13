<?php
class Bs_Auth_AuthPlugin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $loginController = 'login';
        $loginAction     = 'index';
        
        if($request->getModuleName() == "")
        {
            $request->setModuleName('default');
        }
        
        if($request->getActionName() == "")
        {
            $request->setActionName('index');
        }
        
        if($request->getControllerName() == "")
        {
            $request->setControllerName('index');
        }
        
        $namespace = "auth_".$request->getModuleName();

        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session($namespace));
        
        $registry = Zend_Registry::getInstance();
        $acl = $registry->get('acl');

        //First lets check if the user is guest and can access the current page
        $role = Bs_Auth_Roles::GUEST;
        try
        {
            $isAllowed = $acl->isAllowed($role,
                                         $request->getModuleName()."/*",
                                         $request->getActionName());
        }
        catch(Zend_Acl_Exception $e)
        {
            $isAllowed = false;
        }

        if($isAllowed)
        {
            return;
        }



        // If user is not logged in and is not requesting login page
        // - redirect to login page.
        if (!$auth->hasIdentity()
                && !($request->getControllerName() == $loginController)) 
        {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
            $redirector->gotoSimpleAndExit($loginAction, $loginController, $request->getModuleName());
        }
        else 
        {
        	
        }

        // User is logged in or on login page.

        if ($auth->hasIdentity()) {
            // Is logged in
            // Let's check the credential
            $identity = $auth->getIdentity();
            // role is a column in the user table (database)
            //exit($identity->role);
            $role = is_a($identity, "AdminUser") ? Bs_Auth_Roles::ADMIN : Bs_Auth_Roles::GUEST;
            
            //Ugly dirty hack
            if($identity->salesOnly && $request->getModuleName() == 'admin' && $request->getControllerName() != $loginController)
            {
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
                $redirector->gotoUrlAndExit('/');
            }
            
            try
            {
                $isAllowed = $acl->isAllowed($role,
                                             $request->getModuleName()."/*",
                                             $request->getActionName());
            }
            catch(Zend_Acl_Exception $e)
            {
                $isAllowed = false;
            }
            if (!$isAllowed) {
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
                $redirector->gotoUrlAndExit('/');
            }
        }
    }
}