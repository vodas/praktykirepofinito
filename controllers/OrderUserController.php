<?php
namespace app\controllers;

use \yii\web\Controller;

class OrderUserController extends Controller {
    public function actionView() {
        $_SERVER['HTTP_COOKIE'];//it contains all information
        return $this->render('view');
    }
}