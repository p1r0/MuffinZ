<?php
class Bs_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
	private $_username;
	private $_password;
	/**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($username, $password)
    {
        $this->_username = $username;
        $this->_password = $password;
    }
 
    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot
     *                                     be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        try
    	{
    		$user = AdminUser::authenticate($this->_username, $this->_password);
    		$result = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user, array());
    		return $result;
    	}
        catch (Exception $e)
        {
        	switch ($e->getMessage())
        	{
        		case AdminUser::WRONG_PW:
        		case AdminUser::NOT_FOUND:
        			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null, array());
        			break;
        	}

                throw $e;
        }
    }
}