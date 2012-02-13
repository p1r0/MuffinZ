<?php

class Bs_Admin_TableRender
{
    protected $_cols;
    protected $_data;
    protected $_request;
    protected $_controllerName;
    protected $_moduleName;
    protected $_orderColumn;
    protected static $_lastOrderBy = "";
    protected static $_lastOrderDir = "";
    protected $_orderCallback = null;
    protected $_actions = null;
    protected $_renderers = array();
    protected $_noActions = false;

    public function __construct(array $cols, $data, $orderBy, $orderDir = "ASC", array $orderCallback = null, array $actions = null, $noActions = false)
    {
        $this->_actions = $actions;
        $this->_cols = $cols;
        $this->_data = $data;
        $this->_noActions = $noActions;
        $front = Zend_Controller_Front::getInstance();
        $this->_controllerName = $front->getRequest()->getControllerName();
        $this->_moduleName = $front->getRequest()->getModuleName();
        self::$_lastOrderBy = self::url2Field($front->getRequest()->order);
        self::$_lastOrderDir = $front->getRequest()->direction;
        if($orderCallback != null)
        {
            $this->setOrderCallBack($orderCallback[0], $orderCallback[1]);
        }

        if(isset($front->getRequest()->oid) && $this->_orderCallback != null)
        {
            call_user_func_array($this->_orderCallback, 
                                 array($front->getRequest()->oid, $front->getRequest()->odir));
        }
    }

    public function setOrderColumn($colName)
    {
        $this->_orderColumn = $colName;
    }

    public function setOrderCallBack(Zend_Controller_Action $obj, $func)
    {
        $this->_orderCallback = array($obj, $func);
    }

    public function renderHeader($view)
    {
        $header = "    <thead>
        <tr>
";

        $pos = 0;

        foreach($this->_cols as $col)
        {
            if(++$pos == count($this->_cols))
            {
                $size = "";
            }
            else
            {
                $size = isset($col['width']) ? $col['width'] : "250";
            }
            $label = isset($col['label']) ? $col['label'] : ucfirst($col['name']);

            $orderDir = "ASC";
            if(self::$_lastOrderBy == $col['name'])
            {
                if(self::$_lastOrderDir == "ASC")
                {
                    $orderDir = "DESC";
                }
            }
            $label = '<a href="' .
                    $view->url(array('module' => $this->_moduleName, 'controller' => $this->_controllerName, 'action' => 'index', 'order' => self::field2Url($col['name']), 'direction' => $orderDir), 'default', true) .
                    '">' . $label . '</a>';
            $header .= "            <th class=\"table-header-repeat\" width=\"{$size}\">{$label}</th>";
        }
        if(($this->_actions === null || count($this->_actions) > 0) && !$this->_noActions)
        {
            $header .= "<th class=\"table-header-options line-left\" >Acciones</th>";
        }
        //$header .="<th class=\"empty\">&nbsp;</th>";


        $header .= "        </tr>
    </thead>";

        return $header;
    }

    public function renderBody($view)
    {
        $body = "
    <tbody>
";
        foreach($this->_data as $row)
        {
            $body .= "      <tr>";
            foreach($this->_cols as $col)
            {
                if(isset($this->_renderers[$col['name']]))
                {
                    $body .= "
                <td>";
                    $body .= $this->_renderers[$col['name']]->render($view, $col, $this->getValue($row, $col['name']), $this, $row);
                    $body .= "</td>";
                }
                else
                {
                    $body .= "
                <td>";
                    if($col['name'] == $this->_orderColumn)
                    {
                        $body .= $this->renderOrder($view, $row);
                    }
                    else
                    {
                        $body .= $view->escape($this->getValue($row, $col['name']));
                    }
                    $body .= "</td>";
                }
            }
            //The actions
            if(!$this->_noActions)
            {
                $body .= "
            <td class=\"table-body-options\">";

                if($this->_actions === null)
                {
                    $body .= '<a href="' . $view->url(array('module' => $this->_moduleName, 'controller' => $this->_controllerName, 'action' => 'edit', 'id' => $row->id), 'default', true) . '"><img src="/img/edit.png" title="Modificar" alt="Modificar" /></a>&nbsp;&nbsp;';
                    $body .= '<a href="' . $view->url(array('module' => $this->_moduleName, 'controller' => $this->_controllerName, 'action' => 'delete', 'id' => $row->id), 'default', true) . '"><img src="/img/delete.png" title="Borrar" alt="Borrar" /></a></a>';
                }
                else if(is_array($this->_actions))
                {
                    foreach($this->_actions as $action)
                    {
                        if($action["enabled"])
                        {
                            $url = $view->url(array('module' => $this->_moduleName, 'controller' => $this->_controllerName, 'action' => $action["action"], 'id' => $row->id), 'default', true);
                            $body .= "<a href='{$url}' onclick='{$action["onclick"]}'>
                					<img src='{$action["img_src"]} '
                						alt='{$action["alt"]}' 
                						title='{$action["title"]}' /></a>";
                        }
                    }
                }
                $body .= "</td>";
            }
            $body .= "</tr>";
            //$body .= "<td>&nbsp;</td>
        }
        $body .= "    </tbody>";

        return $body;
    }

    public function render($view)
    {
        return "<table class=\"admin-table\" width=\"100%\">
                " . $this->renderHeader($view) . $this->renderBody($view) .
                "</table>";
    }

    public function getValue($obj, $path)
    {
        $pathAr = explode("/", $path);

        foreach($pathAr as $property)
        {
            $obj = $obj->$property;
        }

        return $obj;
    }

    protected function renderOrder($view, $obj)
    {
        $cname = $this->_orderColumn;
        $html = '<form action="" method="POST" onsubmit=""><input name="oid" type="hidden" value="'
                . $obj->id . '" /><input type="hidden" id="odir" name="odir" value="up" />
                 <input type="submit" value="^" onClick="document.getElementById(\'odir\').value = \'up\'" />
                 ' . $obj->$cname . '
                 <input type="submit" value="v" onClick="document.getElementById(\'odir\').value = \'down\'" /></form>';

        return $html;
    }

    public static function field2Url($field)
    {
        return str_replace('/', '.', $field);
    }

    public static function url2Field($urlField)
    {
        return str_replace('.', '/', $urlField);
    }

    public function setCellRederer($col, $renderer)
    {
        $this->_renderers[$col] = $renderer;
    }

}

?>
