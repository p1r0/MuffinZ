<?php

/**
 * Base bootstrap class
 * 
 * @package MuffinZ
 * @copyright 2011-2012 BinarySputnik - http://www.binarysputnik.com
 * @author Tabaré Caorsi <tcaorsi@binarysputnik.com>
 */
class Bs_Application_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function _initControllerPlugins()
    {
        $controller = Zend_Controller_Front::getInstance();
        $layoutModulePlugin = new Bs_Module_LayoutPlugin();
        $layoutModulePlugin->registerModuleLayout('admin', APPLICATION_PATH . '/modules/admin/layouts/scripts');
        $layoutModulePlugin->registerModuleLayout('default', APPLICATION_PATH . '/modules/default/layouts/scripts');

        $controller->registerPlugin($layoutModulePlugin);
        $controller->registerPlugin(new Bs_Auth_AuthPlugin());
        $controller->registerPlugin(new Bs_Application_Controller_Translation_Plugin());
    }

    protected function _initAcl()
    {
        $registry = Zend_Registry::getInstance();
        $registry->set('acl', new Bs_Auth_Acl());
    }

    public function _initSiteConfig()
    {
        $registry = Zend_Registry::getInstance();
        $config = $this->getOption('site');
        $registry->set('site_config', $config);
    }

    protected function _initNavigation()
    {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml');

        $navigation = new Zend_Navigation($config);
        $view->navigation($navigation);
    }

    public function _initDoctrine()
    {
        require_once 'Doctrine/Doctrine.php';
        $loader = Zend_Loader_Autoloader::getInstance();

        $doctrineConfig = $this->getOption('doctrine');


        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(
                Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);

        // Add models and generated base classes to Doctrine autoloader
        Doctrine::loadModels($doctrineConfig['models_path']);

        $loader->pushAutoloader(array("Doctrine_Core", "modelsAutoload"));

        $con = $manager->openConnection($doctrineConfig['connection_string'], 'default');

        $con->setCharset('utf8');
        $con->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);

        return $manager;
    }

    protected function _initAdminMenu()
    {
        $registry = Zend_Registry::getInstance();
        $menu = new Zend_Config_Xml(APPLICATION_PATH . '/configs/admin-menu.xml');
        $registry->set('admin_menu', $menu->toArray());
    }

    protected function _initView()
    {
        $view = new Zend_View();
        $view->addFilterPath('Bs/View/Filter', 'Bs_View_Filter');
        $view->setFilter('Translate');
        
        $view->addHelperPath("Bs/View/Helper", "Bs_View_Helper");
        $view->addHelperPath(APPLICATION_PATH."/helpers", "Zend_View_Helper");
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
    }

    public function _initDefaultRoute()
    {
        // Create route with language id (lang)
        $routeLang = new Zend_Controller_Router_Route(
                        ':lang',
                        array(
                            'lang' => ''
                        ),
                        array('lang' => '[a-z]{2}')
        );

        // Now get router from front controller
        $front = $this->getResource('frontcontroller');
        $router = $front->getRouter();

        // Instantiate default module route
        $routeDefault = new Zend_Controller_Router_Route_Module(
                        array(),
                        $front->getDispatcher(),
                        $front->getRequest()
        );

        // Chain it with language route
        $routeLangDefault = $routeLang->chain($routeDefault);

        // Add both language route chained with default route and
        // plain language route
        $router->addRoute('default', $routeLangDefault);
        $router->addRoute('lang', $routeLang);
    }

    public function _initTranslateHelper()
    {
        Zend_Controller_Action_HelperBroker::addPrefix(
                'Bs_Application_Controller_Action_Helper');
        
    }
    
    protected function _initPartialCache()
    {
        $cacheConf = $this->getOption('cache');
        
        if(isset($cacheConf['partialCache']['enable']))
        {
            if($cacheConf['partialCache']['enable'])
            {
                Bs_View_Helper_PartialCache::setEnabled(true);
            }
            else
            {
                Bs_View_Helper_PartialCache::setEnabled(false );
                //Nothing else to do
                return;
            }
        }
        
        //If cache is enable but dir is not set we throw an exception
        if(!isset($cacheConf['partialCache']['cacheDir']))
        {
            throw new Bs_View_Helper_PartialCache_Excepction(
                    'Partial Cache is enabled but cache directory is not set.');
        }
        
        $manager = new Zend_Cache_Manager;

        $pcache = array(
            'frontend' => array(
                'name' => 'Core',
                'options' => array(
                    'lifetime' => 60, //For testing
                    'automatic_serialization' => false
                )
            ),
            'backend' => array(
                'name' => 'File',
                'options' => array(
                    'cache_dir' => $cacheConf['partialCache']['cacheDir']
                )
            )
        );

        $manager->setCacheTemplate('partial', $pcache);
        Zend_Registry::set('partial_cache', $manager->getCache('partial'));
    }

}

?>
