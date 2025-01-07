<?php
namespace X2nx\WebmanMigrate\Commands\Phinx;

use Phinx\Config\Config;

class Migrate extends \Phinx\Console\Command\Migrate
{
    protected static string $defaultDescription = 'Migrate the database';

    public function __construct(){
        parent::__construct();
        $config = new Config(config('plugin.x2nx.webman-migrate.phinx'), config_path('plugin/x2nx/webman-migrate/phinx.php'));
        $this->setConfig($config);
    }
}