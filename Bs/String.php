<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of String
 *
 * @author pyro
 */
class Bs_String
{
    public static function numeric2Char($string)
    {
        return preg_replace_callback('/&#([0-9]+);/', function($matches)
                        {
                            return chr($matches[1]);
                        }, $string);
    }
}

?>
