<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Container\Container;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use support\Db;
use Symfony\Component\Console\Input\InputInterface;
use Illuminate\Database\Console\Migrations\InstallCommand;
use Closure;

class MigrateInstallCommand extends InstallCommand
{
    protected static string $defaultName = 'migrate:install';

    protected static string $defaultDescription = 'Create the migration repository';

    public function __construct()
    {
        $container = new Container();
        $this->setLaravel($container);
        $connectionResolver = Db::getInstance()->getDatabaseManager();
        $repository = new DatabaseMigrationRepository($connectionResolver, 'migrations');
        // 调用父类构造函数
        parent::__construct($repository);
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