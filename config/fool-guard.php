<?php

return [

    //  token参数名
    'token_param_name'      =>  'X-CSRF-TOKEN',


    //  有效时间
    'expire'                =>  3600,


    //  剩余多少时间刷新token
    'leeway'                =>  1000,


    //  token模型
    'model'                 =>  \Mrlaozhou\Guard\Token::class,
];