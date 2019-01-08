<?php
namespace app\exceptions;
class BusinessException extends CommonException {
    protected $_messages = [
        '100000'  => '',
        '100001'  => '返回数据类型错误',

        '200000'  => '',
        '200001'  => '用户名或密码错误',
        '200002'  => '注册用户失败',
        '200003'  => '该用户已注册',
    ];
}