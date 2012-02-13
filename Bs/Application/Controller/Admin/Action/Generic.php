<?php

/**
 * Description of BaseController
 *
 * @author pyro
 */
require_once APPLICATION_PATH."/../library/WideImage/WideImage.php";

class Bs_Application_Controller_Admin_Action_Generic extends Zend_Controller_Action
{

    public function init()
    {
        
    }

    public function getForm($table = "")
    {
        if($table == "")
        {
            $table = str_replace(array("Controller", "Admin_"), '', get_class($this));
        }
        return new Bs_Form_BaseFormGeneric($table);
    }

    public function indexAction()
    {
        $orderby = "id";
        $orderdir = "ASC";
        $request = $this->getRequest();

        if(isset($request->order))
        {
            $orderby = $request->order;
        }

        if(isset($request->direction))
        {
            $orderdir = strtoupper($request->direction);
        }

        $order = $orderby . " " . $orderdir;

        $table = str_replace(array("Controller", "Admin_"), '', get_class($this));
        $q = Doctrine_Query::create()
                ->select('*')
                ->from($table)
                ->orderBy($order);
        $this->view->entries = $q->execute();
        if($this->_helper->getHelper('FlashMessenger')->getMessages())
        {
            $this->view->messages = $this->_helper
                            ->getHelper('FlashMessenger')->getMessages();
        }

        $paginator = new Bs_Admin_Paginator($q);

        $data = $paginator->getData();

        $xml = new SimpleXMLElement(file_get_contents(APPLICATION_PATH . '/configs/forms.xml'));
        $columns = $xml->xpath("//tables/{$table}/table/columns/*");

        $cols = array();

        $ordercol = "";
        foreach($columns as $col)
        {
            $colAr = array();
            if(isset($col->width))
            {
                $colAr['width'] = $col->width->__toString();
            }
            else
            {
                $colAr['width'] = 150;
            }

            if(isset($col->origin))
            {
                $colAr['name'] = $col->origin->__toString();
            }
            else
            {
                $colAr['name'] = $col->getName();
            }

            $colAr['label'] = $this->_helper->translate($col->label->__toString());

            $cols[] = $colAr;

            if($ordercol == "")
            {
                $ordercol = $colAr['name'];
            }
        }

        $table = new Bs_Admin_TableRender($cols, $data, $ordercol);

        $this->view->tbl = $table;
        $this->view->paginator = $paginator;
    }

    public function addAction()
    {
        $registry = Zend_Registry::getInstance();
        $conf = $registry->get('site_config');
        
        $table = str_replace(array("Controller", "Admin_"), '', get_class($this));
        $request = $this->getRequest();
        $form = $this->getForm();

        if($this->getRequest()->isPost())
        {
            if($form->isValid($request->getPost()))
            {
                $values = $form->getValues(true);
                $item = new $table();
                $item->fromArray($values);
                $item->save();
                //Let's check if any element is an image and then process
                $elements = $form->getElements();
                foreach($elements as $element)
                {
                    if(is_a($element, "Bs_Form_Element_Image"))
                    {
                        $name = $element->getName();
                        if(isset($values[$name]))
                        {
                            $tablei = $table."_".$name;
                            $prefix = $tablei."_";
                            $tmp_name = $conf['img_uploaddir'] . DIRECTORY_SEPARATOR . $item->$name;
                            $image = WideImage::load($tmp_name);
                            $image->resize($conf[$tablei."_image"]["max_width"], $conf[$tablei."_image"]["max_height"])
                                    ->saveToFile($conf['img_uploaddir'] . DIRECTORY_SEPARATOR . $prefix . $item->id . '.jpg');

                            //And create thumbnail
                            $image->resize($conf[$tablei."_thumbnail"]["max_width"], $conf[$tablei."_thumbnail"]["max_height"])
                                    ->saveToFile($conf['img_uploaddir'] . DIRECTORY_SEPARATOR . 'th_'.$prefix . $item->id . '.jpg');

                            $item->$name = $prefix . $item->id . '.jpg';
                            $item->save();

                            //Delete temp image
                            @unlink($tmp_name);
                        }
                    }
                }

                $this->_helper->getHelper('FlashMessenger')->addMessage(
                        $this->_helper->translate('Saved succesfully'));

                return $this->_helper->redirector('index');
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $registry = Zend_Registry::getInstance();
        $conf = $registry->get('site_config');
        
        $table = str_replace(array("Controller", "Admin_"), '', get_class($this));
        $request = $this->getRequest();
        $form = $this->getForm();

        $item = Doctrine_Core::getTable($table)->findOneById($request->id);

        if(!$item)
        {
            return $this->_helper->redirector('index');
        }

        if($this->getRequest()->isPost())
        {
            if($form->isValid($request->getPost()))
            {
                $values = $form->getValues(true);

                $elements = $form->getElements();
                foreach($elements as $element)
                {
                    if(is_a($element, "Bs_Form_Element_Image"))
                    {
                        $name = $element->getName();
                        if($values[$name] == "")
                        {
                            unset($values[$name]);
                        }
                    }
                }
                $item->fromArray($values);
                $item->save();
                
                //Let's check if any element is an image and then process
                $elements = $form->getElements();
                foreach($elements as $element)
                {
                    if(is_a($element, "Bs_Form_Element_Image"))
                    {
                        $name = $element->getName();
                        
                        if(isset($values[$name]))
                        {
                            $tablei = $table."_".$name;
                            $prefix = $tablei."_";
                            $tmp_name = $conf['img_uploaddir'] . DIRECTORY_SEPARATOR . $item->$name;
                            $image = WideImage::load($tmp_name);
                            $image->resize($conf[$tablei."_image"]["max_width"], $conf[$tablei."_image"]["max_height"])
                                    ->saveToFile($conf['img_uploaddir'] . DIRECTORY_SEPARATOR . $prefix . $item->id . '.jpg');

                            //And create thumbnail
                            $image->resize($conf[$tablei."_thumbnail"]["max_width"], $conf[$tablei."_thumbnail"]["max_height"])
                                    ->saveToFile($conf['img_uploaddir'] . DIRECTORY_SEPARATOR . 'th_'.$prefix . $item->id . '.jpg');

                            $item->$name = $prefix . $item->id . '.jpg';
                            $item->save();

                            //Delete temp image
                            @unlink($tmp_name);
                        }
                    }
                }

                $this->_helper->getHelper('FlashMessenger')->addMessage(
                        $this->_helper->translate('Saved succesfully'));

                return $this->_helper->redirector('index');
            }
        }

        $form->populate($item->toArray());

        $this->view->form = $form;
    }

    public function deleteAction()
    {   
        $registry = Zend_Registry::getInstance();
        $conf = $registry->get('site_config');
        
        $request = $this->getRequest();
        $table = str_replace(array("Controller", "Admin_"), '', get_class($this));
        $model = Doctrine::getTable($table)->findOneById($request->id);
        if($model)
        {
            $msg = $this->beforeDelete($model);
            
            if($msg != "")
            {
                $this->_helper->getHelper('FlashMessenger')->addMessage($msg);
                return $this->_helper->redirector('index');
            }
            
            $model->delete();
            
            $prefix = $table."_";
            $imgName = $conf['img_uploaddir'] . DIRECTORY_SEPARATOR . $prefix . $request->id . '.jpg';
            $imgThName = $conf['img_uploaddir'] . DIRECTORY_SEPARATOR . 'th_'.$prefix . $request->id . '.jpg';
            @unlink($imgName);
            @unlink($imgThName);
            
            $this->_helper->getHelper('FlashMessenger')->addMessage(
                    $this->_helper->translate('Deleted'));
        }
        else
        {
            $this->_helper->getHelper('FlashMessenger')->addMessage(
                    $this->_helper->translate('Error deleting'));
        }
        return $this->_helper->redirector('index');
    }

    public function beforeDelete(Doctrine_Record $model)
    {
        return "";
    }
    
}

?>
