<?php
namespace Mrlaozhou\Guard;

trait FoolGuardAdapter
{

    /**
     * 获取守卫名称
     *
     * @return string
     */
    public function getFoolGuardName()
    {
        return auth()->getDefaultDriver();
    }
}