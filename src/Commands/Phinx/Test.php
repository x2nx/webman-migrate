<?php
namespace X2nx\WebmanMigrate\Commands\Phinx;

use Phinx\Config\Config;

class Test extends \Phinx\Console\Command\Test
{
    protected static $defaultName = 'migrate:test';

    protected static string $defaultDescription = 'Verify the configuration file';

    public function __construct(){
        parent::__construct();
        $config = new Config(config('plugin.x2nx.webman-migrate.phinx'), config_path('plugin/x2nx/webman-migrate/phinx.php'));
        $this->setConfig($config);
    }
}