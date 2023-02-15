<?php

return [

    'create_users' => false,

    'truncate_tables' => true,


    'roles_structure' => [
        'administrator' => [
            'users' => 'c,r,u,d',
            'profile' => 'r,u'
        ],
        'maquilador' => [
            'profile' => 'c,r',
        ],
        'almacen' => [
            'profile' => 'r,u',
        ],
        'chofer' => [
            'profile' => 'u,d',
        ],
        'control_calidad' => [
            'profile' => 'c,d',
        ],
        'compras' => [
            'profile' => 'c,u',
        ],
        'ventas' => [
            'profile' => 'u,d',
        ],
        'logistica-y-mesa-de-control' => [
            'profile' => 'r,u',
        ],

    ],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ],
];
