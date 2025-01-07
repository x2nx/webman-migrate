<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Container\Container;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Input\InputInterface;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand as IlluminateMigrateMakeCommand;

class MigrateMakeCommand extends IlluminateMigrateMakeCommand
{
    protected static string $defaultName = 'make:migration';

    protected static string $defaultDescription = 'Create a new migration file';

    public function __construct()
    {
        // 初始化服务容器
        $container = new Container();
        // 将容器绑定到 Command 类
        $this->setLaravel($container);
        // 初始化 MigrationCreator
        $files = new Filesystem();
        $customStubPath = __DIR__ . '/../../Stubs/'; // 如果需要自定义模板路径，可以设置
        $creator = new MigrationCreator($files, $customStubPath);
        // 初始化 Composer
        $composer = new Composer($files, __DIR__ . '/../../..'); // 确保路径正确
        // 调用父类构造函数
        parent::__construct($creator, $composer);
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