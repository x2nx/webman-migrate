<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Database\Console\DumpCommand;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use support\Db;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpFoundation\Exception\UnexpectedValueException;
use Symfony\Component\Process\Process;
use Webman\Config;

class SchemaDumpCommand extends DumpCommand
{
    protected static string $defaultName = 'schema:dump';

    protected static string $defaultDescription = 'Dump the given database schema';

    public function __construct()
    {
        // 初始化服务容器
        $container = new Container();
        $container->singleton(ConnectionResolverInterface::class, function () {
            return Db::getInstance()->getDatabaseManager();
        });
        $container->bind('config', function() {
            return new Config();
        });
        // 绑定 DispatcherContract 到 Dispatcher
        $container->singleton(DispatcherContract::class, function ($app) {
            return new Dispatcher($app);
        });
        // 将容器绑定到 Command 类
        $this->setLaravel($container);
        // 绑定容器到 Facade 类
        Facade::setFacadeApplication($container);
        // 调用父类构造函数
        parent::__construct();
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
     * @param Connection $connection
     * @return string
     */
    protected function path(Connection $connection): string
    {
        $process = new Process(['mysqldump', '--version']);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new UnexpectedValueException('You need to install mysqldump first.');
        }
        return tap($this->option('path') ?: runtime_path('schema/'.$connection->getName().'-schema.sql'), function ($path) {
            (new Filesystem)->ensureDirectoryExists(dirname($path));
        });
    }
}