<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Container\Container;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Console\ShowCommand;
use Symfony\Component\Console\Input\InputInterface;
use X2nx\WebmanMigrate\Db;

class DbShowCommand extends ShowCommand
{
    protected static string $defaultName = 'db:show';

    protected static string $defaultDescription = 'Display information about the given database';

    public function __construct()
    {
        $container = new Container();
        $container->singleton(ConnectionResolverInterface::class, function () {
            // 初始化数据库连接
            $database = new Db();
            return $database->init();
        });
        $this->setLaravel($container);
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
}