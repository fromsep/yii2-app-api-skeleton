<?php
namespace app\helpers;

use yii\base\DynamicModel;
use yii\validators\Validator;
use yii\base\InvalidConfigException;

/**
 * Class ParamsModel
 * @package app\models
 * 参数验证 动态模型
 */
class ParamsModel extends DynamicModel {
    public function __construct($requiredParamKeys, array $params = [], $config = []) {
        $requiredAttributes = [];
        foreach($requiredParamKeys as $key) {
            $requiredAttributes[$key] = NULL;
            if(isset($params[$key])) {
                $requiredAttributes[$key] = $params[$key];
            }
        }
        parent::__construct($requiredAttributes, $config);
    }

    public function addRules($rules = []) {
        if (!empty($rules)) {
            $validators = $this->getValidators();
            foreach ($rules as $rule) {
                if ($rule instanceof Validator) {
                    $validators->append($rule);
                } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                    $validator = Validator::createValidator($rule[1], $this, (array) $rule[0], array_slice($rule, 2));
                    $validators->append($validator);
                } else {
                    throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
                }
            }
        }
        return $this;
    }



}