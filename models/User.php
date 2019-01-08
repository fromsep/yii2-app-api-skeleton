<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $name 用户名
 * @property string $email 邮箱
 * @property string $mobile 手机号
 * @property string $password 密码
 * @property string $access_token 连接token
 * @property string $auth_key
 * @property string $last_login_time 上次登录时间
 * @property string $create_ip 创建ip
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['last_login_time', 'create_time', 'update_time'], 'safe'],
            [['create_ip'], 'required'],
            [['name'], 'string', 'max' => 16],
            [['email'], 'string', 'max' => 64],
            [['mobile'], 'string', 'max' => 11],
            [['password'], 'string', 'max' => 255],
            [['access_token', 'auth_key'], 'string', 'max' => 128],
            [['create_ip'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '用户名',
            'email' => '邮箱',
            'mobile' => '手机号',
            'password' => '密码',
            'access_token' => '连接token',
            'auth_key' => 'Auth Key',
            'last_login_time' => '上次登录时间',
            'create_ip' => '创建ip',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }


    public static function generateNewAccessToken() {
        return Yii::$app->security->generateRandomString();
    }

    public static function generatePasswordHash($password) {
        return Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

}
