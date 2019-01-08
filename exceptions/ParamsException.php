<?php
namespace app\exceptions;

class ParamsException extends CommonException {
    public $code;
    protected $_messages = [
        '200000' => '',
        '200001' => '参数异常',
    ];
}