<?php
return [
    'user' => [
        'user_level' => [
            'default' => 100,
            'roles' => [
                'admin' => [
                    'label' => '관리자',
                    'level' => 1024,
                ],
                'user' => [
                    'label' => '회원',
                    'level' => 91, // default 100, 
                ]
            ]
        ],
    ],
    'level' => [
        'level_100'=>[
            'label'=>'회원',
            'roles'=>[
                'login'=>true,
                'service'=>true, // 방보유, 방입장, 당청금수령
                'withdraw'=>true, //출금
            ]
        ],
        'level_95'=>[
            'label'=>'출금정지 회원',
            'roles'=>[
                'login'=>true,
                'service'=>true,
                'withdraw'=>false,
            ]
        ],
        'level_91'=>[
            'label'=>'서비스제한 회원',
            'roles'=>[
                'login'=>true,
                'service'=>false,
                'withdraw'=>false,
            ]
        ],
        'level_90'=>[
            'label'=>'로그인불가 회원',
            'roles'=>[
                'login'=>false,
                'service'=>false,
                'withdraw'=>false,
            ]
        ],
    ]
];