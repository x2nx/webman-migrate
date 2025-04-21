<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Closure;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Input\InputInterface;
use X2nx\WebmanMigrate\Db;

class DbSeedCommand extends SeedCommand
{
    protected static string $defaultName = 'db:seed';

    protected static string $defaultDescription = 'Seed the database with records';

    public function __construct()
    {
        $container = new Container();
        $container->singleton('config', function () {
            return config();
        });
        $this->setLaravel($container);
        // 初始化数据库连接
        $database = new Db();
        // 调用父类构造函数
        parent::__construct($database->init());
    }

    protected function getDatabase()
    {
        $database = $this->input->getOption('database');

        return $database ?: config('database.default');
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