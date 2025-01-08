<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

return [
    // 默认数据库
    'default' => 'default',

    // 各种数据库配置
    'connections' => [
        'default' => [
            'driver'      => env('DB_CONNECTION', 'mysql'),
            'host'        => env('DB_HOST', '127.0.0.1'),
            'port'        => env('DB_PORT', 3306),
            'database'    => env('DB_DATABASE', ''),
            'username'    => env('DB_USERNAME', ''),
            'password'    => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset'     => env('DB_CHARSET', 'utf8mb4'),
            'collation'   => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix'      => env('DB_PREFIX', ''),
            'suffix'      => env('DB_SUFFIX', ''),
            'strict'      => env('DB_STRICT', false),
            'engine'      => env('DB_ENGINE', null),
            'options' => [
                \PDO::ATTR_TIMEOUT => env('DB_TIMEOUT', 5),
            ]
        ],
    ],
];
