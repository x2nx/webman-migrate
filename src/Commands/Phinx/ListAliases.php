<?php
namespace X2nx\WebmanMigrate\Commands\Phinx;

use Phinx\Config\Config;

class ListAliases extends \Phinx\Console\Command\ListAliases
{
    protected static $defaultName = 'migrate:list:aliases';

    protected static string $defaultDescription = 'List template class aliases';

    public function __construct(){
        parent::__construct();
        $config = new Config(config('plugin.x2nx.webman-migrate.phinx'), config_path('plugin/x2nx/webman-migrate/phinx.php'));
        $this->setConfig($config);
    }
}