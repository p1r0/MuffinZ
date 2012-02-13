<?php
/**
 * Partial helper that acts the same as way as Zend's Partial
 * but caches the resulting html.
 * 
 * @package MuffinZ
 * @copyright 2011-2012 BinarySputnik - http://www.binarysputnik.com
 * @author Tabaré Caorsi <tcaorsi@binarysputnik.com>
 */
class Bs_View_Helper_PartialCache extends Zend_View_Helper_Partial
{

    protected static $_enabled = true;
    
    public function __construct()
    {
        
    }

    public function partialCache($name = null, $module = null, $model = null, $key = null)
    {
        if(!self::$_enabled)
        {
            return $this->partial($name, $module, $model);
        }
        
        $logger = new Bs_Plogger_Logger('CACHE');
        
        if (0 == func_num_args()) {
            return $this;
        }
        
        if ((null == $model) && (null !== $module)
            && (is_array($module) || is_object($module)))
        {
            $model = $module;
        }
        
        $id = $name;
        
        if(is_array($model) && $key == null)
        {
            $id = $name.'?'.http_build_query($model);
        }
        else if($key != null)
        {
            $id = $name.$key;
        }
        
        
        $cacheId = md5($id);
        $cache = Zend_Registry::get('partial_cache');

        $data = '';
        
        if(($data = $cache->load($cacheId)) === false)
        {
            $logger->debug('Cache MISS', $cacheId, $id);
            $data = $this->partial($name, $module, $model);
            if(!$cache->save($data))
            {
                $logger->error("Couldn't save cache!");
            }
        }
        else
        {
            $logger->debug('Cache HIT', $cacheId, $id);
        }

        return $data;
    }
    
    public static function setEnabled($enabled)
    {
        self::$_enabled = $enabled;
    }

}

?>
