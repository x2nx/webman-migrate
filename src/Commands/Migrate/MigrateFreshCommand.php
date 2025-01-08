<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Filesystem\Filesystem;
use support\Db;
use Symfony\Component\Console\Input\InputInterface;
use Closure;

class MigrateFreshCommand extends FreshCommand
{
    protected static string $defaultName = 'migrate:fresh';

    protected static string $defaultDescription = 'Drop all tables and re-run all migrations';

    public function __construct()
    {
        $container = new Container();
        $dispatcher = new Dispatcher($container);
        $container->bind('db', function () use($dispatcher) {
            return Db::getInstance();
        });
        $this->setLaravel($container);
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
}