<?php
class Bs_Module_LayoutPlugin extends Zend_Controller_Plugin_Abstract {
 
	/**
	 * Array of layout paths associating modules with layouts
	 */
	protected $_moduleLayouts;
 
	/**
	 * Registers a module layout.
	 * This layout will be rendered when the specified module is called.
	 * If there is no layout registered for the current module, the default layout as specified
	 * in Zend_Layout will be rendered
	 * 
	 * @param String $module		The name of the module
	 * @param String $layoutPath	The path to the layout
	 * @param String $layout		The name of the layout to render
	 */
	public function registerModuleLayout($module, $layoutPath, $layout=null){
		$this->_moduleLayouts[$module] = array(
			'layoutPath' => $layoutPath,
			'layout' => $layout
		);
	}
 
	public function preDispatch(Zend_Controller_Request_Abstract $request){
		$layout = Zend_Layout::getMvcInstance(); 
		$moduleName = $request->getModuleName() != '' ? $request->getModuleName() : 'default';
		if(isset($this->_moduleLayouts[$moduleName])){
			$config = $this->_moduleLayouts[$moduleName];
			
			if($layout->getMvcEnabled()){
				$layout->setLayoutPath($config['layoutPath']);
 
				if($config['layout'] !== null){
					$layout->setLayout($config['layout']);
				}
			}
		}
	}
}
