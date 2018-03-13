<?php
namespace App\Controllers;

use App\Models\CommentsModel;
use \App\Controllers\Controller;
use \DateTime;
use App\System\App;

class CommentsController extends Controller {

    protected $table = "comments";

    public function add() {
        if(!empty($_POST)){
                $text  = isset($_POST['comment']) ? $_POST['comment'] : '';
                $text  = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
                $text  = $this->replace_link($text, "https://");
                $text  = $this->replace_link($text, "http://");
                $model = new CommentsModel;
                $model->create([
                    'created_at' => date('Y-m-d H:i:s'),
                    'user'       => $_COOKIE['user'],
                    'text'       => $text
                ]);
            }
         App::redirect('dashboard');
       }

       public function replace_link($comment, $url_prefix){
            $offset = 0;
            while(($pos = strpos($comment, $url_prefix, $offset)) !== false){
                $end = strpos($comment, " ", $pos);
                if ($end === false){
                    $end = strlen($comment);
                }
                $len = $end - $pos;
                $url = substr($comment, $pos, $len);
                $clickable = "<a onclick= \"return confirm('You are now leaving this website. Are you sure you wish to continue?')\" href=\"$url\">$url</a>";
                $comment = substr_replace($comment, $clickable, $pos, $len);
                $offset = $pos + strlen($clickable) + 1;
            }
            return $comment;
       }
    }
