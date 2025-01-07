<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Database\Console\Factories\FactoryMakeCommand as IlluminateFactoryMakeCommand;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Symfony\Component\Console\Input\InputInterface;
use Webman\Config;

class FactoryMakeCommand extends IlluminateFactoryMakeCommand
{
    protected static string $defaultName = 'make:factory';

    protected static string $defaultDescription = 'Create a new model factory';

    public function __construct()
    {
        // 获取容器实例
        $container = new Container();
        $container->bind('config', function() {
            return new Config();
        });
        $this->setLaravel($container);
        parent::__construct(new Filesystem());
    }

    protected function rootNamespace(): string
    {
        return 'app\\';
    }

    protected function buildClass($name): array|string
    {
        $factory = class_basename(Str::ucfirst(str_replace('Factory', '', $name)));
        echo 1;
        $namespaceModel = $this->option('model')
            ? $this->qualifyModel($this->option('model'))
            : $this->qualifyModel($this->guessModelName($name));

        $model = class_basename($namespaceModel);

        $namespace = $this->getNamespace(
            Str::replaceFirst($this->rootNamespace(), 'Database\\Factories\\', $this->qualifyClass($this->getNameInput()))
        );

        $replace = [
            '{{ factoryNamespace }}' => $namespace,
            'NamespacedDummyModel' => $namespaceModel,
            '{{ namespacedModel }}' => $namespaceModel,
            '{{namespacedModel}}' => $namespaceModel,
            'DummyModel' => $model,
            '{{ model }}' => $model,
            '{{model}}' => $model,
            '{{ factory }}' => $factory,
            '{{factory}}' => $factory,
        ];

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    protected function getPath($name): string
    {
        $name = (new Stringable($name))->replaceFirst($this->rootNamespace(), '')->finish('Factory')->value();

        return base_path('/database/factories/'.str_replace('\\', '/', $name).'.php');
    }

    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = base_path(sprintf('vendor/illuminate/database/Console/Factories/%s', trim($stub, '/'))))
            ? $customPath
            : __DIR__.$stub;
    }

    protected function getNamespace($name): string
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
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