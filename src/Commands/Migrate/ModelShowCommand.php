<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Database\Console\ShowModelCommand;
use Illuminate\Database\Eloquent\ModelInspector;
use Symfony\Component\Console\Input\InputInterface;

class ModelShowCommand extends ShowModelCommand
{
    protected static string $defaultName = 'model:show';

    protected static string $defaultDescription = 'Show information about an Eloquent model';

    public function __construct()
    {
        $container = new Container();
        $this->setLaravel($container);
        // 调用父类构造函数
        parent::__construct();
    }

    /**
     * @param ModelInspector $modelInspector
     * @return int
     */
    public function handle(ModelInspector $modelInspector): int
    {
        $modelInspector = new ModelInspector(
            Container::getInstance()->make(ApplicationContract::class)
        );
        try {
            $info = $modelInspector->inspect(
                $this->argument('model'),
                $this->option('database')
            );
        } catch (BindingResolutionException $e) {
            $this->components->error($e->getMessage());

            return 1;
        }

        $this->display(
            $info['class'],
            $info['database'],
            $info['table'],
            $info['policy'],
            $info['attributes'],
            $info['relations'],
            $info['events'],
            $info['observers']
        );

        return 0;
    }

    protected function getDefaultConfirmCallback()
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