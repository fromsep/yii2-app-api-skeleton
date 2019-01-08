<?php
namespace app\components;

use yii\web\ErrorHandler;
use Yii;
use yii\web\Response;
use yii\base\UserException;
use app\exceptions\CommonException;

class ApiErrorHandler extends ErrorHandler{
    protected function renderException($exception)
    {
        if (Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();
            // reset parameters of response to avoid interference with partially created response data
            // in case the error occurred while sending the response.
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
        } else {
            $response = new Response();
        }

        // 捕捉业务异常
        if($exception instanceof CommonException) {
            $response->setStatusCode(200);
            $response->data = static::convertBizExceptionToArray($exception);
            $response->send();
            return;
        }

        // $rawErrorData = static::convertExceptionToString($exception);

        $response->setStatusCodeByException($exception);

        $useErrorView = $response->format === Response::FORMAT_HTML && (!YII_DEBUG || $exception instanceof UserException);

        if ($useErrorView && $this->errorAction !== null) {
            $result = Yii::$app->runAction($this->errorAction);
            if ($result instanceof Response) {
                $response = $result;
            } else {
                $response->data = $result;
            }
        } elseif ($response->format === Response::FORMAT_HTML) {
            if ($this->shouldRenderSimpleHtml()) {
                // AJAX request
                $response->data = '<pre>' . $this->htmlEncode(static::convertExceptionToString($exception)) . '</pre>';
            } else {
                // if there is an error during error rendering it's useful to
                // display PHP error in debug mode instead of a blank screen
                if (YII_DEBUG) {
                    ini_set('display_errors', 1);
                }
                $file = $useErrorView ? $this->errorView : $this->exceptionView;
                $response->data = $this->renderFile($file, [
                    'exception' => $exception,
                ]);
            }
        } elseif ($response->format === Response::FORMAT_RAW) {
            $response->data = static::convertExceptionToString($exception);
        } else {
            $data = $this->convertExceptionToArray($exception);
            // windows 平台错误信息转码
            $data['message'] = iconv("GB2312", "UTF-8//IGNORE", $data['message']);
            $data['error-info'] = iconv("GB2312", "UTF-8//IGNORE", $data['error-info']);
            $response->data = $data;
        }

        $response->send();
    }

    public static function convertBizExceptionToArray(CommonException $exception) {
        return [
            'code'      => $exception->getCode(),
            'message'   => $exception->getMessage()
        ];
    }
}