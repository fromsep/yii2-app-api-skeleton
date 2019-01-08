<?php
namespace app\exceptions;
use yii\base\UserException;

/**
 * Class CommonException
 * @package app\exceptions
 * 业务通用异常
 */
class CommonException extends UserException {
    protected $_messages = [];

    public function __construct($code = 0, $message = "",  \Exception $previous = null) {
        $this->code     = '999999';
        $this->message  = '系统繁忙';
        if(!isset($this->_messages[$code]))
            return ;

        $this->code     = $code;
        $this->message  = $this->_messages[$code];

        if($message !== '') {
            $this->message = $message;
        }
    }
}