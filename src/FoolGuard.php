<?php
namespace Mrlaozhou\Guard;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Database\Eloquent\Concerns\GuardsAttributes;
use Illuminate\Foundation\Application;
use Mrlaozhou\Guard\Contracts\ProviderContract;

class FoolGuard implements \Illuminate\Contracts\Auth\Guard, StatefulGuard
{
    use GuardHelpers;

    /**
     * @var ProviderContract
     */
    public $tokenProvider;

    /**
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected $lastAttempted;


    /**
     * LzGuard constructor.
     *
     * @param \Illuminate\Foundation\Application          $app
     * @param array                                       $config
     * @param \Mrlaozhou\Guard\Contracts\ProviderContract $tokenProvider
     */
    public function __construct(Application $app, array $config, ProviderContract $tokenProvider)
    {
        $this->setProvider(\Auth::createUserProvider($config['provider']));
        $this->setTokenProvider($tokenProvider);
        $this->request  =   $app['request'];
    }

    /**
     * @return bool
     */
    public function check()
    {
        if( $this->user() ) {
            return true;
        }
        if( $user_id = $this->getTokenProvider()->check($this->getTokenFromRequest()) ) {
            $this->onceUsingId($user_id);
            return true;
        }
        return false;
    }

    public function guest()
    {
        return ! $this->check();
    }

    public function id()
    {
        return $this->user()->getAuthIdentifier();
    }

    public function setUser(Authenticatable $user)
    {
        $token          =   $this->getTokenProvider()->getTokenInstance();
        $user->token    =   $token->getKey();
        $user->expired_at   =   $token->expired_at->diffForHumans();
        $this->user =   $user;
    }

    public function user()
    {
        return $this->user;
    }

    /**
     * @param array $credentials
     *
     * @return bool|string
     */
    public function validate(array $credentials = [])
    {
        return $this->attempt($credentials, false);
    }

    /**
     * @param array $credentials
     * @param bool  $remember
     *
     * @return bool|string
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        //  根据整数获取用户
        $this->lastAttempted  =   $user   =   $this->getProvider()->retrieveByCredentials($credentials);
        //  密码验证
        if( $user && $this->getProvider()->validateCredentials($user, $credentials) ) {
            return $this->login($user);
        }
        return false;
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param bool                                       $remember
     *
     * @return string
     */
    public function login(Authenticatable $user, $remember = false)
    {
        $token = $this->getTokenProvider()->buildByUser($user);
        $this->setUser($user);
        return $token;
    }

    /**
     * @param mixed $id
     * @param bool  $remember
     *
     * @return bool|\Illuminate\Contracts\Auth\Authenticatable|string
     */
    public function loginUsingId($id, $remember = false)
    {
        if( !is_null($user = $this->getProvider()->retrieveById($id)) ) {
            return $this->login($user);
        }
        return false;
    }

    public function logout()
    {
        $this->getTokenProvider()->destroy();
        $this->user =   null;
    }

    /**
     * @param array $credentials
     *
     * @return bool
     */
    public function once(array $credentials = [])
    {
        if( $this->validate($credentials) ) {
            $this->setUser($this->lastAttempted);
            return true;
        }
        return false;
    }

    /**
     * @param mixed $id
     *
     * @return bool
     */
    public function onceUsingId($id)
    {
        if( ! is_null( $user = $this->getProvider()->retrieveById($id) ) ) {
            $this->setUser($user);
            return true;
        }
        return false;
    }

    public function viaRemember()
    {
        // TODO: Implement viaRemember() method.
    }

    public function setTokenProvider(ProviderContract $tokenProvider)
    {
        $this->tokenProvider    =   $tokenProvider;
    }

    /**
     * @return \Mrlaozhou\Guard\Contracts\ProviderContract
     */
    public function getTokenProvider()
    {
        return $this->tokenProvider;
    }

    protected function getTokenFromRequest()
    {
        return ($token = $this->request->header($tokenParamName = config('fool-guard.token_header_name')))
            ?   $token
            :   $this->request->get(config('fool-guard.token_param_name'));
    }
}
