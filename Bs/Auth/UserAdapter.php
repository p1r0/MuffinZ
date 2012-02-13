<?php

class Bs_Auth_UserAdapter implements Zend_Auth_Adapter_Interface
{

    private $_clientNumber;
    private $_password;

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($clientNumber, $password)
    {
        $this->_clientNumber = $clientNumber;
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
            $user = User::authenticate($this->_clientNumber, $this->_password);
            $result = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user, array());
            return $result;
        }
        catch(Exception $e)
        {
            switch($e->getMessage())
            {
                case User::WRONG_PW:
                case User::NOT_FOUND:
                    return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null, array());
                    break;
            }

            throw $e;
        }
    }

}