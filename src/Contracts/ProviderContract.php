<?php
namespace Mrlaozhou\Guard\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface ProviderContract
{

    /**
     * @param string|null $token
     *
     * @return mixed
     */
    public function check(string $token = null);

    /**
     * 生成token
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return string
     */
    public function buildByUser(Authenticatable $user);

    /**
     * 刷新token
     * @return mixed
     */
    public function refresh();

    /**
     * 摧毁token
     * @return mixed
     */
    public function destroy();

    /**
     * 检出用户
     * @return mixed
     */
    public function retrieveUser();


    /**
     * @return \Mrlaozhou\Guard\Token
     */
    public function getTokenInstance();
}