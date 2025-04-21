<?php
namespace X2nx\WebmanMigrate\Commands\Migrate;

use Illuminate\Container\Container;
use Illuminate\Support\ConfigurationUrlParser;
use support\Db;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpFoundation\Exception\UnexpectedValueException;
use Symfony\Component\Process\Process;

class DbCommand extends \Illuminate\Database\Console\DbCommand
{
    protected static string $defaultName = 'db';

    protected static string $defaultDescription = 'Start a new database CLI session';

    public function __construct()
    {
        // 初始化服务容器
        $container = new Container();
        // 将容器绑定到 Command 类
        $this->setLaravel($container);
        // 调用父类构造函数
        parent::__construct();
    }

    public function getConnection()
    {
        $process = new Process(['mysql', '--version']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new UnexpectedValueException('You need to install mysql first.');
        }

        $connection = config('database.connections.'.
        (($db = $this->argument('connection')) ?? \config('database.default'))
        );

        if (empty($connection)) {
            throw new UnexpectedValueException("Invalid database connection [{$db}].");
        }

        if (! empty($connection['url'])) {
            $connection = (new ConfigurationUrlParser)->parseConfiguration($connection);
        }

        if ($this->option('read')) {
            if (is_array($connection['read']['host'])) {
                $connection['read']['host'] = $connection['read']['host'][0];
            }

            $connection = array_merge($connection, $connection['read']);
        } elseif ($this->option('write')) {
            if (is_array($connection['write']['host'])) {
                $connection['write']['host'] = $connection['write']['host'][0];
            }

            $connection = array_merge($connection, $connection['write']);
        }

        return $connection;
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