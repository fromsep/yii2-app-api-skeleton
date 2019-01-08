<?php

namespace app\models;

use app\helpers\Redis;
use Yii;

/**
 * This is the model class for table "auth_user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $access_token
 */
class Auth implements \yii\web\IdentityInterface
{
    public $id;
    public $access_token;

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
    }

    /**
     * FunctionName: findIdentityByAccessToken
     * Description :
     *
     * @param mixed $token
     * @param null $type
     * @return Auth|null
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $userInfo = Redis::get($token);

        if(empty($userInfo)) {
            return NULL;
        }

        $userInfo = json_decode($userInfo, true);
        $user = new self();

        $user->id           = $userInfo['id'];
        $user->access_token = $userInfo['access_token'];

        return $user;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
    }
}
