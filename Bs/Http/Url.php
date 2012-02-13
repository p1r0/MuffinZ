<?php

/**
 * 
 *  Copyright 2011 BinarySputnik Co - http://binarysputnik.com
 * 
 * 
 *  This file is part of MuffinPHP.
 *
 *  MuffinPHP is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, version 3 of the License.
 *
 *  MuffinPHP is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 *
 */


/**
 * Class to handle Url related functions
 *
 * @author Tabaré "pyro" Caorsi <tcaorsi@binarysputnik.com>
 */
class Bs_Http_Url
{
    /**
     * Create an absolute url from a given relative url.
     * 
     * This function DOES NOT check if the given url is relative or not.
     * 
     * @param String $url the relative url to conver to absolute
     * @return String the absolute url made from $url
     */
    public function makeAbsolute($url)
    {
        $protocol = $this->getProtocol();
        $port = $this->getPort();
        $domain = $_SERVER['HTTP_HOST'];
        
        return $protocol.$domain.$port.$url;
    }
    
    /**
     * Returns the current relative URL
     * 
     * @return String
     */
    public function getCurrent()
    {
        return $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Returns the current absolute URL
     * 
     * @return String
     */
    public function getCurrentAbsolute()
    {
        return $this->makeAbsolute($this->getCurrent());
    }
    
    /**
     * Returns the referer
     * @return String
     */
    public function getReferer()
    {
        if(isset($_SERVER['HTTP_REFERER'])){
            if(!is_null($_SERVER['HTTP_REFERER']))
            {
                return $_SERVER['HTTP_REFERER'];
            }
        }
        return false;
    }
    
    /**
     * Returns the host
     * @return String
     */
    public function getHost()
    {
        return $_SERVER['HTTP_HOST'];        
    }
    
	/**
     * Returns the port
     * @return String
     */
    public function getPort()
    {
        $port = "";
        if($_SERVER['SERVER_PORT'] != "80" && $_SERVER['SERVER_PORT'] != "443")
        {
            $port = ":".$_SERVER['SERVER_PORT'];
        }        
        return $port;
    }
    
    /**
     * Returns the protocol
     * @return String
     */
    public function getProtocol()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? "https://" : "http://";
        return $protocol;
    }
    
    public function getRelativeRefererUrl()
    {
        $port = $this->getPort();
        $host = $this->getHost();
        $referer = $this->getReferer();
        $aux = explode($host.$port,$referer);
        return $aux[1];
    }
    
    public function hasReferer()
    {
        return isset($_SERVER['HTTP_REFERER']);
        
    }
}
?>