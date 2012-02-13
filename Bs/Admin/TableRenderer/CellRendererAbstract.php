<?php

/**
 * Description of CellRendererAbstract
 *
 * @author pyro
 */
abstract class Bs_Admin_TableRenderer_CellRendererAbstract
{
    abstract function render($view, $col, $value, Bs_Admin_TableRender $tableRenderer, $row);
}

?>
