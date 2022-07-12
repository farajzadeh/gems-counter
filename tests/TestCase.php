<?php


namespace Farajzadeh\GemsCounter\Test;

use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Farajzadeh\GemsCounter\GemsCounterServiceProvider;

abstract class TestCase extends Orchestra
{
    /** @var \Farajzadeh\GemsCounter\Test\User */
    protected $testUser;

    protected static $migrations;

    public function setUp(): void
    {
        parent::setUp();

        if (!self::$migrations || self::$migrations == []) {
            self::$migrations = [];
            $this->prepareMigration();
        }

        // Note: this also flushes the cache from within the migration
        $this->setUpDatabase($this->app);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            GemsCounterServiceProvider::class,
        ];
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->softDeletes();
        });
        foreach (self::$migrations as $migration)
            $migration->up();

        $this->testUser = User::create(['email' => 'test@user.com']);
    }

    private function prepareMigration()
    {
        include_once __DIR__ . '/../database/migrations/create_transactions_table.php.stub';
        self::$migrations []= new \CreateTransactionsTables();
        include_once __DIR__ . '/../database/migrations/create_gems_table.php.stub';
        self::$migrations []= new \CreateGemsTables();
    }
}