<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Filesystem\Filesystem;
use support\Db;
use Symfony\Component\Console\Input\InputInterface;
use Closure;

class MigrateStatusCommand extends StatusCommand
{
    protected static string $defaultName = 'migrate:status';

    protected static string $defaultDescription = 'Show the status of each migration';

    public function __construct()
    {
        $container = new Container();
        $this->setLaravel($container);
        $dispatcher = new Dispatcher($container);
        $files = new Filesystem();
        $connectionResolver = Db::getInstance()->getDatabaseManager();
        $repository = new DatabaseMigrationRepository($connectionResolver, 'migrations');
        $migrator = new Migrator($repository, $connectionResolver, $files, $dispatcher);
        // 调用父类构造函数
        parent::__construct($migrator);
    }

    protected function getDefaultConfirmCallback(): Closure
    {
        return function () {
            return true;
        };
    }

    /**
     * 覆盖 configurePrompts 方法，避免调用 runningUnitTests()
     *
     * @param InputInterface $input
     */
    protected function configurePrompts(InputInterface $input)
    {
        // 空实现，绕过 ConfiguresPrompts 的功能
    }

    /**
     * 覆盖 getMigrationPath 方法，返回自定义的迁移文件路径
     *
     * @return string
     */
    protected function getMigrationPath(): string
    {
        // 返回自定义的迁移文件路径
        return base_path('database/migrations');
    }
}