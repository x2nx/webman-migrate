<?php
namespace X2nx\WebmanMigrate\Commands\Phinx;

use Phinx\Config\Config;

class SeedRun extends \Phinx\Console\Command\SeedRun
{
    protected static $defaultName = 'seed:run';

    protected static string $defaultDescription = 'Run database seeders';

    public function __construct(){
        parent::__construct();
        $config = new Config(config('plugin.x2nx.webman-migrate.phinx'), config_path('plugin/x2nx/webman-migrate/phinx.php'));
        $this->setConfig($config);
    }
}