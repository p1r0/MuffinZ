<?php
class Bs_Auth_Acl extends Zend_Acl
{
	public function __construct()
	{
		$this->add(new Zend_Acl_Resource(Bs_Auth_Resources::MOD_ADMIN));
                $this->add(new Zend_Acl_Resource(Bs_Auth_Resources::MOD_SITE));
		$this->addRole(new Zend_Acl_Role(Bs_Auth_Roles::GUEST));
                $this->addRole(new Zend_Acl_Role(Bs_Auth_Roles::ADMIN), Bs_Auth_Roles::GUEST);
		
		$this->allow(Bs_Auth_Roles::GUEST, Bs_Auth_Resources::MOD_SITE);
                $this->allow(Bs_Auth_Roles::ADMIN, Bs_Auth_Resources::MOD_ADMIN);
	
	}
}