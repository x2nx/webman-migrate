<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Database\Console\ShowModelCommand;
use Illuminate\Database\Eloquent\ModelInspector;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use function Illuminate\Support\enum_value;

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

    public function handle($modelInspector = null)
    {
        try {
            $info = $this->inspect(
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

    public function inspect($model, $connection = null)
    {
        $class = $this->qualifyModel($model);

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = Container::getInstance()->make($class);

        if ($connection !== null) {
            $model->setConnection($connection);
        }

        return [
            'class' => get_class($model),
            'database' => $model->getConnection()->getName(),
            'table' => $model->getConnection()->getTablePrefix().$model->getTable(),
            'policy' => $this->getPolicy($model),
            'attributes' => $this->getAttributes($model),
            'relations' => $this->getRelations($model),
            'events' => $this->getEvents($model),
            'observers' => $this->getObservers($model),
            'collection' => $this->getCollectedBy($model),
            'builder' => $this->getBuilder($model),
        ];
    }

    protected function getPolicy($model)
    {
//        $policy = Gate::getPolicyFor($model::class);

        $policy = false;

        return $policy ? $policy::class : null;
    }

    /**
     * Get the column attributes for the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function getAttributes($model)
    {
        $connection = $model->getConnection();
        $schema = $connection->getSchemaBuilder();
        $table = $model->getTable();
        $columns = $schema->getColumns($table);
        $indexes = $schema->getIndexes($table);

        return (new BaseCollection($columns))
            ->map(fn ($column) => [
                'name' => $column['name'],
                'type' => $column['type'],
                'increments' => $column['auto_increment'],
                'nullable' => $column['nullable'],
                'default' => $this->getColumnDefault($column, $model),
                'unique' => $this->columnIsUnique($column['name'], $indexes),
                'fillable' => $model->isFillable($column['name']),
                'hidden' => $this->attributeIsHidden($column['name'], $model),
                'appended' => null,
                'cast' => $this->getCastType($column['name'], $model),
            ])
            ->merge($this->getVirtualAttributes($model, $columns));
    }

    protected function getColumnDefault($column, $model)
    {
        $attributeDefault = $model->getAttributes()[$column['name']] ?? null;

        return enum_value($attributeDefault) ?? $column['default'];
    }

    protected function getEvents($model)
    {
        return (new BaseCollection($model->dispatchesEvents()))
            ->map(fn (string $class, string $event) => [
                'event' => $event,
                'class' => $class,
            ])->values();
    }

    protected function getObservers($model)
    {
        $listeners = $this->app->make('events')->getRawListeners();

        // Get the Eloquent observers for this model...
        $listeners = array_filter($listeners, function ($v, $key) use ($model) {
            return Str::startsWith($key, 'eloquent.') && Str::endsWith($key, $model::class);
        }, ARRAY_FILTER_USE_BOTH);

        // Format listeners Eloquent verb => Observer methods...
        $extractVerb = function ($key) {
            preg_match('/eloquent.([a-zA-Z]+)\: /', $key, $matches);

            return $matches[1] ?? '?';
        };

        $formatted = [];

        foreach ($listeners as $key => $observerMethods) {
            $formatted[] = [
                'event' => $extractVerb($key),
                'observer' => array_map(fn ($obs) => is_string($obs) ? $obs : 'Closure', $observerMethods),
            ];
        }

        return new BaseCollection($formatted);
    }

    protected function getCollectedBy($model)
    {
        return $model->newCollection()::class;
    }

    protected function getBuilder($model)
    {
        return $model->newQuery()::class;
    }

    protected function qualifyModel(string $model)
    {
        if (str_contains($model, '\\') && class_exists($model)) {
            return $model;
        }

        $model = ltrim($model, '\\/');

        $model = str_replace('/', '\\', $model);

        $rootNamespace = $this->getNamespace();

        if (Str::startsWith($model, $rootNamespace)) {
            return $model;
        }

        return is_dir(app_path('model'))
            ? $rootNamespace.'model\\'.$model
            : $rootNamespace.$model;
    }

    protected function getNamespace() {
        return 'app\\';
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