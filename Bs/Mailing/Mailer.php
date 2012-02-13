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
 * Description of Mailer
 *
 * @author pyro
 */

set_include_path( realpath(dirname(__FILE__))."/../../phpMailer_v2.3" . PATH_SEPARATOR .  get_include_path());

require_once("class.phpmailer.php");
require_once("class.smtp.php");

class Bs_Mailing_Mailer
{
    protected $_config;
    public function  __construct() {
        $registry = Zend_Registry::getInstance();
        $conf = $registry->get('site_config');
        $this->_config = $conf['mail'];
    }

    public function sendMail($to, $from, $subject, $body, $fromName = "")
    {
        $mail = new PHPMailer();
        /*if($configManager->getValue("mail.issmtp"))
        {
                $mail->IsSMTP();
                $mail->Host = $configManager->getValue("mail.host");
                $mail->SMTPAuth = TRUE;
                $mail->Username = $configManager->getValue("mail.user");
                $mail->Password = $configManager->getValue("mail.pass");
        }*/

        $mail->From = $from;
        $mail->Sender = $this->_config['sender'];
        $mail->FromName = $fromName == "" ? "Bot" : $fromName;
        $mail->Subject=$subject;
        $mail->MsgHTML($body);
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        if(is_array($to))
        {
                foreach($to as $t)
                        $mail->AddBCC($t);
        }
        else
        {
                $mail->AddAddress($to);
        }

        $sent = $mail->Send();
        if(!$sent)
         ;//exit("Error!!!");
        return $sent;
    }
}
?>
