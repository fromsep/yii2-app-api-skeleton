<?php
namespace app\controllers;
use app\components\ApiHeaderAuth;
use app\exceptions\BusinessException;
use app\exceptions\ParamsException;
use yii\base\Controller;
use yii\base\ActionEvent;
use app\helpers\ParamsModel;
use Yii;
use app\components\IpRateLimiter;
use yii\filters\RateLimiter;

class BaseController extends Controller {
    public $optionalActions = [];
    public $modelClass      = 'app\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class'     => ApiHeaderAuth::className(),
            'optional'  => $this->optionalActions
        ];

        $behaviors['rateLimiter'] = [
            'class'     => IpRateLimiter::className(),
            'actions'   => ['info']
        ];
        return $behaviors;
    }

    public function beforeAction($action)
    {
        $event = new ActionEvent($action);
        $this->trigger(self::EVENT_BEFORE_ACTION, $event);

        return $event->isValid;
    }

    public function afterAction($action, $result)
    {
        if(!is_array($result)) {
            throw new BusinessException('100001');
        }

        $result = [
            'code'      => '000000',
            'message'   => '请求成功',
            'data'      => $result
        ];

        $event = new ActionEvent($action);
        $event->result = $result;
        $this->trigger(self::EVENT_AFTER_ACTION, $event);
        return $event->result;
    }

    /**
     * FunctionName: validateParams
     * Description : 验证参数
     *
     * @param $paramsKeys   array 需要验证的字段
     * @param $params       array 需要验证的参数
     * @param $rules        array 验证参数的规则
     * @return array 通过验证处理的参数
     * @throws ParamsException
     * @throws \yii\base\InvalidConfigException
     */
    public function validateParams($paramsKeys, $params, $rules) {
        if(empty($paramsKeys)) {
            return [];
        }

        $model = new ParamsModel($paramsKeys, $params);
        $success = $model->addRules($rules)->validate();
        if(!$success) {
            throw new ParamsException('200001', current($model->getFirstErrors()));
        }

        return $model->getAttributes();
    }

}