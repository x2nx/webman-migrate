<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Container\Container;
use Illuminate\Database\Console\WipeCommand;
use support\Db;
use Symfony\Component\Console\Input\InputInterface;
use Closure;

class DbWipeCommand extends WipeCommand
{
    protected static string $defaultName = 'db:wipe';

    protected static string $defaultDescription = 'Drop all tables, views, and types';

    public function __construct()
    {
        $container = new Container();
        $container->bind('db', function () {
            return Db::getInstance();
        });
        $this->setLaravel($container);
        // 调用父类构造函数
        parent::__construct();
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