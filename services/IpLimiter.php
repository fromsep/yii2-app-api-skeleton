<?php
namespace app\services;

use yii\filters\RateLimitInterface;
use app\helpers\Redis;

class IpLimiter implements RateLimitInterface {
    protected $cacheKeyPrefix    = 'limiter:ip:';
    protected $rateLimit;
    protected $window;
    protected $allowance;
    protected $allowance_updated_at;


    public function __construct($rateLimit = 100, $window = 600) {
        $this->rateLimit = $rateLimit;
        $this->window    = $window;
    }

    /**
     * FunctionName: getRateLimit
     * Description : 返回允许的请求的最大数目及时间
     *
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @return array
     */
    public function getRateLimit($request, $action)
    {
        return [$this->rateLimit, $this->window]; // $rateLimit requests per second
    }

    /**
     * FunctionName: loadAllowance
     * Description : 返回剩余的允许的请求和最后一次速率限制检查时 相应的 UNIX 时间戳数
     *
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @return array
     */
    public function loadAllowance($request, $action)
    {
        $ipBin = ip2long($request->getUserIP());
        $keyName = $this->cacheKeyPrefix . $ipBin;
        $content = Redis::get($keyName);

        if(empty($content)) {
            $this->allowance_updated_at = time();
            // 新建
            $content = "{$this->rateLimit}_{$this->allowance_updated_at}";
            Redis::setex($keyName, $this->window, $content);

            return [$this->rateLimit, $this->allowance_updated_at];
        }

        // 5_1522
        $info = explode('_', $content);

        return [$info[0], $info[1]];
    }

    /**
     * FunctionName: saveAllowance
     * Description : 保存剩余的允许请求数和当前的 UNIX 时间戳
     *
     * @param \yii\web\Request $request
     * @param \yii\base\Action $action
     * @param int $allowance
     * @param int $timestamp
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $this->allowance            = $allowance;
        $this->allowance_updated_at = $timestamp;

        $ipBin = ip2long($request->getUserIP());
        $keyName = $this->cacheKeyPrefix . $ipBin;

        // 新建
        $content = "{$this->allowance}_{$this->allowance_updated_at}";
        Redis::setex($keyName, Redis::ttl($keyName), $content);

        return [$this->allowance, $this->allowance_updated_at];
    }

}