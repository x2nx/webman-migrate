<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Closure;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use support\Db;
use Symfony\Component\Console\Input\InputInterface;

class DbSeedCommand extends SeedCommand
{
    protected static string $defaultName = 'db:seed';

    protected static string $defaultDescription = 'Seed the database with records';

    public function __construct()
    {
        $container = new Container();
        $connectionResolver = Db::getInstance()->getDatabaseManager();
        $this->setLaravel($container);
        // 调用父类构造函数
        parent::__construct($connectionResolver);
    }

    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $this->components->info('Seeding database.');

        $previousConnection = $this->resolver->getDefaultConnection();

        $this->resolver->setDefaultConnection($this->getDatabase());

        Model::unguarded(function () {
            $this->getSeeder()->__invoke();
        });

        if ($previousConnection) {
            $this->resolver->setDefaultConnection($previousConnection);
        }

        return 0;
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