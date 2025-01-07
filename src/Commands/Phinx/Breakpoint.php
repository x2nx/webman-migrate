<?php
namespace X2nx\WebmanMigrate\Commands\Phinx;

use Phinx\Config\Config;

class Breakpoint extends \Phinx\Console\Command\Breakpoint
{
    protected static $defaultName = 'migrate:breakpoint';

    protected static string $defaultDescription = 'Manage breakpoints';

    public function __construct(){
        parent::__construct();
        $config = new Config(config('plugin.x2nx.webman-migrate.phinx'), config_path('plugin/x2nx/webman-migrate/phinx.php'));
        $this->setConfig($config);
    }
}