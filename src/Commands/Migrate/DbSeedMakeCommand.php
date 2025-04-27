<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Container\Container;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Webman\Config;

class DbSeedMakeCommand extends SeederMakeCommand
{
    protected static string $defaultName = 'make:seeder';

    protected static string $defaultDescription = 'Create a new seeder class';

    public function __construct()
    {
        $container = new Container();
        $container->singleton('config', function () {
            return new Config();
        });
        $this->setLaravel($container);
        parent::__construct(
            new Filesystem()
        );
    }

    protected function resolveStubPath($stub): string
    {
        return is_file($customPath = base_path(sprintf('vendor/illuminate/database/Console/Seeds/%s', trim($stub, '/'))))
            ? $customPath
            : __DIR__.$stub;
    }

    protected function getPath($name): string
    {
        $name = str_replace('\\', '/', Str::replaceFirst($this->rootNamespace(), '', $name));

        if (is_dir(base_path('database/seeders'))) {
            return base_path(sprintf('database/seeders/%s.php', $name));
        }

        return base_path(sprintf('database/seeders/%s.php', $name));
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
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return 'database\seeders\\';
    }
}