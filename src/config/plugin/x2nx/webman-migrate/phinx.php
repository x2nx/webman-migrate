<?php

return [
    "paths" => [
        "migrations"    => "database/migrations",
        "seeds"         => "database/seeders"
    ],
    "environments" => [
        "default_migration_table" => "migrations",
        "default_environment"     => "development",
        "development" => [
            "adapter" => config('database.default'),
            "host"    => config(sprintf('database.connections.%s.host', config('database.default'))),
            "name"    => config(sprintf('database.connections.%s.database', config('database.default'))),
            "user"    => config(sprintf('database.connections.%s.username', config('database.default'))),
            "pass"    => config(sprintf('database.connections.%s.password', config('database.default'))),
            "port"    => config(sprintf('database.connections.%s.port', config('database.default'))),
            "charset" => config(sprintf('database.connections.%s.charset', config('database.default'))),
            "table_prefix" => config(sprintf('database.connections.%s.prefix', config('database.default'))),
            "table_suffix" => config(sprintf('database.connections.%s.suffix', config('database.default'))),
        ],
        'version_order' => 'creation'
    ]
];