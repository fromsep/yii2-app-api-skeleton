<?php
namespace app\services;

use Yii;
use app\exceptions\BusinessException;
use app\helpers\Redis;
use app\models\User as UserModel;

class User {

    public static function register($params) {
        $user = UserModel::findOne(['mobile' => $params['mobile']]);

        if($user instanceof UserModel) {
            throw new BusinessException('200003');
        }

        $userInfo = [
            'mobile'        => $params['mobile'],
            'password'      => UserModel::generatePasswordHash($params['password']),
            'create_ip'     => Yii::$app->request->userIP,
            'create_time'   => date('Y-m-d H:i:s'),
        ];

        $user = new UserModel($userInfo);
        $result = $user->save();

        if(!empty($user->errors) || $result !== true) {
            throw new BusinessException('200002');
        }

        $userInfo = $user->toArray(['mobile']);

        return $userInfo;
    }

    public static function login($params) {
        $data = [
            'access_token' => ''
        ];

        $user = UserModel::findOne(['mobile' => $params['mobile']]);
        if(!$user instanceof UserModel
            || $user->validatePassword($params['password']) !== true) {
            throw new BusinessException('200001');
        }

        $user->access_token     = $user->generateNewAccessToken();
        $user->last_login_time  = date('Y-m-d H:i:s');
        $user->save();

        $userInfo = $user->toArray();

        $expTime = 60000;
        Redis::setex($user->access_token, $expTime, json_encode($userInfo));

        return [
            'access_token' => $user->access_token
        ];
    }


    public static function logout() {
        $accessToken = Yii::$app->user->identity->getAccessToken();
        Redis::del($accessToken);
        return [];
    }
}