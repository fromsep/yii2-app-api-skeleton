<?php
namespace app\components;

use yii\base\ActionFilter;
use yii\web\Request;
use yii\web\Response;
use Yii;
use yii\web\TooManyRequestsHttpException;
use yii\filters\RateLimitInterface;
use app\services\IpLimiter;
class IpRateLimiter extends ActionFilter {
    public $actions = [];
    /**
     * @var bool whether to include rate limit headers in the response
     */
    public $enableRateLimitHeaders = true;
    /**
     * @var string the message to be displayed when rate limit exceeds
     */
    public $errorMessage = 'Rate limit exceeded.';

    /**
     * @var Request the current request. If not set, the `request` application component will be used.
     */
    public $request;
    /**
     * @var Response the response to be sent. If not set, the `response` application component will be used.
     */
    public $response;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->request === null) {
            $this->request = Yii::$app->getRequest();
        }
        if ($this->response === null) {
            $this->response = Yii::$app->getResponse();
        }
    }


    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        $this->checkRateLimit($this->request, $this->response, $action);
        return true;
    }

    /**
     * Checks whether the rate limit exceeds.
     * @param RateLimitInterface $user the current user
     * @param Request $request
     * @param Response $response
     * @param \yii\base\Action $action the action to be executed
     * @throws TooManyRequestsHttpException if rate limit exceeds
     */
    public function checkRateLimit($request, $response, $action)
    {
        $limiter = new IpLimiter(10, 60);
        list($limit, $window)           = $limiter->getRateLimit($this->request, $action);
        list($allowance, $timestamp)    = $limiter->loadAllowance($this->request, $action);

        $current = time();

        $allowance += (int) (($current - $timestamp) * $limit / $window);

        if ($allowance > $limit) {
            $allowance = $limit;
        }

        if ($allowance < 1) {
            $limiter->saveAllowance($request, $action, 0, $current);
            $this->addRateLimitHeaders($response, $limit, 0, $window);
            throw new TooManyRequestsHttpException($this->errorMessage);
        }

        $limiter->saveAllowance($request, $action, $allowance - 1, $current);
        $this->addRateLimitHeaders($response, $limit, $allowance - 1, (int) (($limit - $allowance + 1) * $window / $limit));
    }

    /**
     * Adds the rate limit headers to the response.
     * @param Response $response
     * @param int $limit the maximum number of allowed requests during a period
     * @param int $remaining the remaining number of allowed requests within the current period
     * @param int $reset the number of seconds to wait before having maximum number of allowed requests again
     */
    public function addRateLimitHeaders($response, $limit, $remaining, $reset)
    {
        if ($this->enableRateLimitHeaders) {
            $response->getHeaders()
                ->set('X-Rate-Limit-Limit', $limit)
                ->set('X-Rate-Limit-Remaining', $remaining)
                ->set('X-Rate-Limit-Reset', $reset);
        }
    }
}