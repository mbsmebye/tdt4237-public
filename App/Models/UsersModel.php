<?php
namespace App\Models;

use \App\System\App;
use \App\Models\Model;
use \App\System\Auth;

class UsersModel extends Model {

    protected $table = "users";

    public function login($username, $passwordHash) {
        $userRow = $this->getUserRow($username);
        if($userRow) {
            if($userRow->password === $passwordHash) {
                $_SESSION['auth'] = $userRow->id;
                return true;
            }
        }
        return false;
    }

    public static function logged(){
        if(!isset($_SESSION['auth'])) {
            App::redirect('signin');
            exit;
        }
    }

    public function getUserRow($username){
      //Mitt forsøk
      //$sql = App::getDb()->prepare('SELECT * FROM users WHERE username = :username', true);
      //$sql->execute(array(':username' => $username));
      //return $sql;

      //Orginal
      //return App::getDb()->query('SELECT * FROM users WHERE username = "' . $username .'"', true);
      return $this -> query('SELECT * FROM users WHERE username = ?', array($username), true);
    }

    public function getPasswordHash($username){
        $userRow = $this->getUserRow($username);
        return $userRow->password;
    }

    public function getLoginStatus($username){
        $userRow = $this->getUserRow($username);
        return $userRow->login_status;
    }

    public function getUnlockTime($username){
        $userRow = $this->getUserRow($username);
        return $userRow->unlock_time;
    }

    public function getId($username){
        $userRow = $this->getUserRow($username);
        return $userRow->id;
    }

    public function getEmail($username){
        $userRow = $this->getUserRow($username);
        return $userRow->email;
    }

    public function getAdmin($username){
        $userRow = $this->getUserRow($username);
        return $userRow->admin;
    }

}
