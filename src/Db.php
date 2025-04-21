<?php

namespace X2nx\WebmanMigrate;

use PDO;

class Db extends \support\Db
{
    protected function setupDefaultConfiguration(): void
    {
        $this->container['config']['database.fetch'] = PDO::FETCH_OBJ;
        $this->container['config']['database.default'] = config('database.default');
    }

    public function init(): \Illuminate\Database\DatabaseManager
    {
        $this->addConnection(
            config(sprintf('database.connections.%s', config('database.default'))),
            config('database.default')
        );
        $this->setAsGlobal(); // 使 Capsule 全局可用
        $this->bootEloquent(); // 启动 Eloquent ORM

        return $this->getDatabaseManager();
    }
}