<?php

namespace Mrlaozhou\Guard;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Mrlaozhou\Guard\Contracts\ProviderContract;

class GuardProvider implements ProviderContract
{
    /**
     * @var \Mrlaozhou\Guard\Token
     */
    public $eloquent;

    /**
     * @var \Mrlaozhou\Guard\Token|null
     */
    protected $instance;

    /**
     * @var
     */
    protected $token;


    public function __construct()
    {
        $this->eloquent        =   app(config('fool-guard.model'));
    }

    /**
     * @param string|null $token
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function check(string $token = null)
    {
        if( is_null($token) )  return false;
        /** @var \Mrlaozhou\Guard\Token|null instance */
        $this->instance = $instance = $this->getNewInstance()->newQuery()->where('guard', RequestHelper::guardName())->find($token);
        return ( !is_null($instance) && $instance->exists && $this->verifyExpiredAt() ) ? $this->retrieveUserId() : false;
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable|\Mrlaozhou\Guard\FoolGuardAdapter $user
     *
     * @return string
     * @throws \Exception
     */
    public function buildByUser(Authenticatable $user)
    {
        if( ! method_exists($user, 'getFoolGuardName') ) {
            throw new \Exception("守卫 Model 必须引入 interface[\Mrlaozhou\Guard\FoolGuardAdapter]");
        }
        $instance       =   $this->getNewInstance();
        $instance->fill([
            'token'     =>  $token = Hash::make($user->getAuthIdentifier()),
            'guard'     =>  $user->getFoolGuardName(),
            'user_id'   =>  $user->getAuthIdentifier(),
            'expired_at'=>  now()->addSeconds(config('fool-guard.expire', 3600)),
        ])->save();

        $this->instance = $instance->refresh();

        return $token;
    }

    /**
     * @return mixed|void
     * @throws \Exception
     */
    public function destroy()
    {
        $this->instance->delete();
    }

    /**
     * @return bool|mixed
     */
    public function refresh()
    {
        $this->instance->expired_at = now()->addSeconds(config('fool-guard.expire'));
        return $this->instance->save();
    }

    public function retrieveUser()
    {
        return $this->instance->user;
    }

    public function retrieveUserId()
    {
        return $this->instance->user_id;
    }

    /**
     * @return \Mrlaozhou\Guard\Token|null
     */
    public function getTokenInstance()
    {
        return $this->instance;
    }

    /**
     * @return \Mrlaozhou\Guard\Token
     */
    public function getNewInstance()
    {
        return clone $this->eloquent;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function verifyExpiredAt()
    {
        /** @var \Carbon\Carbon $expired */
        $expired        =   $this->instance->expired_at;
        //  过期
        if( $expired->isPast() ) {
            $this->destroy();
            return false;
        }
        if( ($expired->getTimestamp() - time()) < config('fool-guard.leeway', 300) ) {
            $this->refresh();
        }
        return true;
    }
}