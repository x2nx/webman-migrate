<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Console\Migrations\MigrateCommand as IlluminateMigrateCommand;
use Illuminate\Filesystem\Filesystem;
use support\Db;
use Symfony\Component\Console\Input\InputInterface;
use Closure;

class MigrateCommand extends IlluminateMigrateCommand
{
    protected static string $defaultName = 'migrate';

    protected static string $defaultDescription = 'Migrate the database';

    public function __construct()
    {
        $container = new Container();
        $dispatcher = new Dispatcher($container);
        $files = new Filesystem();
        $this->setLaravel($container);
        // 绑定容器到 Facade 类
        $connectionResolver = Db::getInstance()->getDatabaseManager();
        $repository = new DatabaseMigrationRepository($connectionResolver, 'migrations');
        $migrator = new Migrator($repository, $connectionResolver, $files, $dispatcher);
        // 调用父类构造函数
        parent::__construct($migrator, $dispatcher);
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

    /**
     * Get the path to the stored schema for the given connection.
     *
     * @param  Connection  $connection
     * @return string
     */
    protected function schemaPath($connection): string
    {
        if ($this->option('schema-path')) {
            return $this->option('schema-path');
        }

        if (file_exists($path = runtime_path('schema/'.$connection->getName().'-schema.dump'))) {
            return $path;
        }

        return runtime_path('schema/'.$connection->getName().'-schema.sql');
    }
}