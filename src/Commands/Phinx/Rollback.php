<?php
namespace X2nx\WebmanMigrate\Commands\Phinx;

use Phinx\Config\Config;

class Rollback extends \Phinx\Console\Command\Rollback
{
    protected static $defaultName = 'migrate:rollback';

    protected static string $defaultDescription = 'Rollback the last or to a specific migration';

    public function __construct(){
        parent::__construct();
        $config = new Config(config('plugin.x2nx.webman-migrate.phinx'), config_path('plugin/x2nx/webman-migrate/phinx.php'));
        $this->setConfig($config);
    }
}