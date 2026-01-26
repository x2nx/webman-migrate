<?php

namespace X2nx\WebmanMigrate;

use PDO;
use Webman\Config;

class Db extends \support\Db
{
    protected function setupDefaultConfiguration(): void
    {
        $this->container['config']['database.fetch'] = PDO::FETCH_OBJ;
        $this->container['config']['database.default'] = Config::get('database.default');
    }

    public function init(): \Illuminate\Database\DatabaseManager
    {
        $connections = Config::get('database.connections', []);

        foreach ($connections as $name => $config) {
            $this->addConnection($config, $name);
        }

        $this->setAsGlobal(); // 使 Capsule 全局可用
        $this->bootEloquent(); // 启动 Eloquent ORM

        return $this->getDatabaseManager();
    }
}