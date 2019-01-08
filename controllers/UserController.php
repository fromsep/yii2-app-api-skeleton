<?php
namespace app\controllers;

use app\models\User as UserModel;
use app\services\User;
use Yii;

class UserController extends BaseController {
    public $optionalActions = ['register', 'login', 'index'];

    public function behaviors() {
        $behaviors = parent::behaviors();
        return $behaviors;
    }
    
    public function actionRegister() {
        $requiredParamsKeys = [
            'mobile',
            'password'
        ];

        $rules = [
            [['mobile', 'password'], 'filter', 'filter' => 'trim'],
            [['mobile', 'password'], 'required'],
        ];

        $params = $this->validateParams($requiredParamsKeys, Yii::$app->request->post(), $rules);

        return User::register($params);
    }

    public function actionLogin() {
        $requiredParamsKeys = [
            'mobile',
            'password',
        ];

        $rules = [
            [['mobile', 'password'], 'filter', 'filter' => 'trim'],
            [['mobile', 'password'], 'required'],
        ];

        $params = $this->validateParams($requiredParamsKeys, Yii::$app->request->post(), $rules);

        return User::login($params);
    }

    public function actionLogout() {
        return User::logout();
    }

    public function actionInfo() {
        $userId = Yii::$app->user->id;
        $userInfo = UserModel::findOne($userId)->toArray();
        unset($userInfo['id'], $userInfo['password'], $userInfo['auth_key']);
        return $userInfo;
    }






}