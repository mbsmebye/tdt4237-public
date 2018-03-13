<?php
namespace App\Controllers;

use \App\System\App;
use \App\System\Settings;
use \App\System\FormValidator;
use \App\Controllers\Controller;
use \App\Models\UsersModel;
use \App\System\Auth;

class SessionsController extends Controller {

    public function login() {
        if(!empty($_POST)) {

            $username = isset($_POST['username']) ? $_POST['username'] : '';
            //$password = isset($_POST['password']) ? hash('sha1', Settings::getConfig()['salt'] . $_POST['password']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            if($this->auth->checkLoginStatus($username)) {
                if($this->auth->checkCredentials($username, $password)) {
                    setcookie("user", $username);
                    setcookie("password",  $_POST['password']);
                    if ($this->userRep->getAdmin($username)){
                        $_SESSION['admin'] = true;
                    }else{
                        $_SESSION['admin'] = false;
                    }
                    $_SESSION['auth']       = $username;
                    $_SESSION['id']         = $this->userRep->getId($username);
                    $_SESSION['email']      = $this->userRep->getEmail($username);
                    $_SESSION['password']   = $password;
                    $_SESSION['latestAction'] = time();
                    if (empty($_SESSION['token'])) {
                      $_SESSION['token'] = bin2hex(random_bytes(32));
                    }

                    App::redirect('dashboard');
                }

                else {
                    $errors = [
                        "Your username and your password don't match."
                    ];
                }
            }
            else {
              $errors = [
                  "Your user is temporarily suspended."
              ];
            }
        }

        $this->render('pages/signin.twig', [
            'title'       => 'Sign in',
            'description' => 'Sign in to the dashboard',
            'errors'      => isset($errors) ? $errors : ''
        ]);
    }

    public function logout() {
        App::redirect();
    }

}
