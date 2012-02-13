<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Strings
 *
 * @author pyro
 */
class Bs_View_Helper_StringCut extends Zend_View_Helper_Abstract
{
    public function stringCut($str, $chars, $pad = "")
    {
        $newStr = "";
        $padded = false;
        $chunks =  explode(" ", $str);
        foreach($chunks as $chunk)
        {
            if((strlen($newStr) + strlen($chunk)) <= $chars)
            {
                $newStr .= " ".$chunk;
            }
            else
            {
            	if($pad != "" && !$padded)
            	{
            		$newStr .= $pad;
            		$padded = true;
            	}
                return $newStr;
            }
        }

        return $newStr;
    }
}
?>
