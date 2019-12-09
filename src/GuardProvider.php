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
        $this->instance = $instance = $this->eloquent->newQuery()->find($token);
        return ( !is_null($instance) && $instance->exists && $this->verifyExpiredAt() ) ? $this->retrieveUserId() : false;
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return string
     */
    public function buildByUser(Authenticatable $user)
    {
        $this->eloquent->fill([
            'token'     =>  $token = Hash::make($user->getAuthIdentifier()),
            'user_id'   =>  $user->getAuthIdentifier(),
            'expired_at'=>  now()->addSeconds(config('fool-guard.expire', 3600)),
        ])->save();

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