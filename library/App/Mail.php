<?php

/**
 * Class to send emails
 *
 * @category App
 * @package App_Mail
 * @copyright Copyright (c) 2011, Morteza Milani
 */
class App_Mail extends Zend_Mail{
    
    public function notification($email, $notification){
        $this->setBodyHtml('<p>You have a new notification from Kode:</p><p dir="rtl">'.$notification.'</p>');
        $this->setSubject('Notification');
        $this->setFrom('kode@algoritmi.sbu.ac.ir');
        $this->addTo($email);
        $this->send();
    }
    
    public function resetPassword($email,$password){
        $this->setBodyHtml('Your new password: <strong>'.$password.'</strong>');
        $this->setSubject('Password Reset');
        $this->setFrom('kode@algoritmi.sbu.ac.ir');
        $this->addTo($email);
        $this->send();
    }
    
    public function recoverUsername($email,$username){
        $this->setBodyHtml('Your username: <strong>'.$username.'</strong>');
        $this->setSubject('Username');
        $this->setFrom('kode@algoritmi.sbu.ac.ir');
        $this->addTo($email);
        $this->send();
    }
}