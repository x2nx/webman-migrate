<?php

$commands = [];

if (config('plugin.x2nx.webman-migrate.app.driver') === 'phinx') {
    $commands = array_merge($commands, [
        X2nx\WebmanMigrate\Commands\Phinx\Breakpoint::class,
        X2nx\WebmanMigrate\Commands\Phinx\Create::class,
        X2nx\WebmanMigrate\Commands\Phinx\Init::class,
        X2nx\WebmanMigrate\Commands\Phinx\ListAliases::class,
        X2nx\WebmanMigrate\Commands\Phinx\Migrate::class,
        X2nx\WebmanMigrate\Commands\Phinx\Rollback::class,
        X2nx\WebmanMigrate\Commands\Phinx\SeedCreate::class,
        X2nx\WebmanMigrate\Commands\Phinx\SeedRun::class,
        X2nx\WebmanMigrate\Commands\Phinx\Status::class,
        X2nx\WebmanMigrate\Commands\Phinx\Test::class,
    ]);
}

if (config('plugin.x2nx.webman-migrate.app.driver') === 'migrate') {
    $commands = array_merge($commands, [
        X2nx\WebmanMigrate\Commands\Migrate\FactoryMakeCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\MigrateFreshCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\MigrateInstallCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\MigrateCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\MigrateMakeCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\MigrateRefreshCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\MigrateResetCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\MigrateRollbackCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\MigrateStatusCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\DbSeedCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\DbSeedMakeCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\DbCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\SchemaDumpCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\DbMonitorCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\ModelPruneCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\DbShowCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\ModelShowCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\DbTableCommand::class,
        X2nx\WebmanMigrate\Commands\Migrate\DbWipeCommand::class,
    ]);
}

return $commands;