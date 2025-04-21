<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;

class ModelPruneCommand extends PruneCommand
{
    protected static string $defaultName = 'model:prune';

    protected static string $defaultDescription = 'Prune models that are no longer needed';

    public function __construct()
    {
        $container = new Container();
        // 绑定 DispatcherContract 到 Dispatcher
        $container->singleton(DispatcherContract::class, function ($app) {
            return new Dispatcher($app);
        });
        $this->setLaravel($container);
        // 调用父类构造函数
        parent::__construct();
    }

    public function models()
    {
        if (! empty($models = $this->option('model'))) {
            return (new Collection($models))->filter(function ($model) {
                return class_exists($model);
            })->values();
        }

        $except = $this->option('except');

        if (! empty($models) && ! empty($except)) {
            throw new InvalidArgumentException('The --models and --except options cannot be combined.');
        }

        return (new Collection(Finder::create()->in($this->getPath())->files()->name('*.php')))
            ->map(function ($model) {
                return str_replace(
                        ['/', '.php'],
                        ['\\', ''],
                        Str::after($model->getRealPath(), realpath(app_path()).DIRECTORY_SEPARATOR)
                    );
            })->when(! empty($except), function ($models) use ($except) {
                return $models->reject(function ($model) use ($except) {
                    return in_array($model, $except);
                });
            })->filter(function ($model) {
                return class_exists($model);
            })->filter(function ($model) {
                return $this->isPrunable($model);
            })->values();
    }

    protected function getPath(): array|string
    {
        if (! empty($path = $this->option('path'))) {
            return (new Collection($path))
                ->map(fn ($path) => base_path($path))
                ->all();
        }

        return base_path('model');
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