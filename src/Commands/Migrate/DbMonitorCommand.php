<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Container\Container;
use Illuminate\Database\Console\MonitorCommand;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputInterface;
use Webman\Config;
use X2nx\WebmanMigrate\Db;

class DbMonitorCommand extends MonitorCommand
{
    protected static string $defaultName = 'db:monitor';

    protected static string $defaultDescription = 'Monitor the number of connections on the specified database';

    public function __construct()
    {
        $container = new Container();
        $dispatcher = new Dispatcher($container);
        $container->singleton('config', function () {
            return new Config();
        });
        $this->setLaravel($container);
        // 初始化数据库连接
        $database = new Db();
        // 调用父类构造函数
        parent::__construct($database->init(), $dispatcher);
    }

    public function parseDatabases($databases): Collection
    {
        return (new Collection(@explode(',', $databases)))->map(function ($database) {
            if (! $database) {
                $database = config('database.default');
            }

            $maxConnections = $this->option('max');

            $connections = $this->connection->connection($database)->threadCount();

            return [
                'database' => $database,
                'connections' => $connections,
                'status' => $maxConnections && $connections >= $maxConnections ? '<fg=yellow;options=bold>ALERT</>' : '<fg=green;options=bold>OK</>',
            ];
        });
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