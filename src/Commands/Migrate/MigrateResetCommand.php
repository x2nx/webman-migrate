<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Console\Migrations\ResetCommand;
use support\Db;
use Symfony\Component\Console\Input\InputInterface;
use Closure;

class MigrateResetCommand extends ResetCommand
{
    protected static string $defaultName = 'migrate:reset';

    protected static string $defaultDescription = 'Rollback all database migrations';

    public function __construct()
    {
        $container = new Container();
        $this->setLaravel($container);
        $dispatcher = new Dispatcher($container);
        $files = new Filesystem();
        $database = Db::getInstance();
        if (empty($database)) {
            $config = require __DIR__ . '/../../config/database.php';
            // 初始化数据库连接
            $database = new Capsule;
            $database->addConnection($config['connections'][$config['default']]);
            $database->setAsGlobal(); // 使 Capsule 全局可用
            $database->bootEloquent(); // 启动 Eloquent ORM
        }
        $connectionResolver = $database->getDatabaseManager();
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