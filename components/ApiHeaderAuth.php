<?php
namespace app\components;

use yii\filters\auth\AuthMethod;

class ApiHeaderAuth extends AuthMethod{
    /**
     * @var string the HTTP header name
     */
    public $header = 'access-token';
    /**
     * @var string a pattern to use to extract the HTTP authentication value
     */
    public $pattern;
    /**
     * {@inheritdoc}
     */
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get($this->header);

        if ($authHeader !== null) {
            if ($this->pattern !== null) {
                if (preg_match($this->pattern, $authHeader, $matches)) {
                    $authHeader = $matches[1];
                } else {
                    return null;
                }
            }

            $identity = $user->loginByAccessToken($authHeader, get_class($this));
            if ($identity === null) {
                $this->challenge($response);
                $this->handleFailure($response);
            }

            return $identity;
        }

        return null;
    }
}