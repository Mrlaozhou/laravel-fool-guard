<?php
namespace Mrlaozhou\Guard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RequestHelper
{

    /**
     * 获取当前请求 Guard name
     * @return mixed
     */
    public static function guardName()
    {
        return collect(request()->route()->middleware())->mapWithKeys(function ($item) {
            return [explode(':', $item)[0] => explode(':', $item)[1] ?? ''];
        })->get('auth', auth()->getDefaultDriver());
    }
}