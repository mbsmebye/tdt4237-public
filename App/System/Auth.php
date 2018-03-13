<?php
namespace App\System;

use \App\Models\UsersModel;

class Auth{

    protected $userRep;

    public function __construct(){
        $this->userRep = new UsersModel;
    }

    public function checkCredentials($username, $password)
    {
        $user = $this->userRep->getUserRow($username);

        if ($user === false) {
            return false;
        }

        $id = $this->userRep->getId($username);
        $passwordHash = hash('sha256', Settings::getConfig()['salt'] . $password);

        if ($passwordHash === $this->userRep->getPasswordhash($username)){
            $this->userRep->update($id, ['login_status'  => 0]);
            return true;
        }else{
            $loginStatus = $this->userRep->getLoginStatus($username);
            $this->userRep->update($id, ['login_status'  => ++$loginStatus]);
            if ($loginStatus > 4) {
              $time = date('Y-m-d H:i:s', strtotime('+60 minutes'));
              $this->userRep->update($id, ['unlock_time'  => $time]);
            }
            return false;
        }
    }

    public function checkLoginStatus($username){
      $loginStatus = $this->userRep->getLoginStatus($username);
      if ($loginStatus > 4){
          $unlockTime = $this->userRep->getUnlockTime($username);
          if (date('Y-m-d H:i:s', strtotime('now')) < $unlockTime){
              return false;
          }
      }
      return true;
    }

    public function isAdmin(){
        if ($this->isLoggedIn()){
          if (isset($_SESSION['admin'])){
            return $_SESSION['admin'];
          }
        }
    }

    public function isLoggedIn(){
        if (isset($_COOKIE['user'])){
            return true;
        }
    }

    public function isAdminPage($template){
        if (strpos($template, 'admin') == '6'){
            return true;;
        }else{
            return false;
        }

    }
}
