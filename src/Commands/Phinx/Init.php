<?php
namespace X2nx\WebmanMigrate\Commands\Phinx;

class Init extends \Phinx\Console\Command\Init
{
    protected static $defaultName = 'migrate:init';

    protected static string $defaultDescription = 'Initialize the application for Phinx';
}