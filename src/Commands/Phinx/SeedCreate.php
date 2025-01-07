<?php
namespace X2nx\WebmanMigrate\Commands\Phinx;

use Phinx\Config\Config;

class SeedCreate extends \Phinx\Console\Command\SeedCreate
{
    protected static $defaultName = 'seed:create';

    protected static string $defaultDescription = 'Create a new database seeder';

    public function __construct(){
        parent::__construct();
        $config = new Config(config('plugin.x2nx.webman-migrate.phinx'), config_path('plugin/x2nx/webman-migrate/phinx.php'));
        $this->setConfig($config);
    }
}