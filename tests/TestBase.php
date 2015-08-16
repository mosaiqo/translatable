<?php

use Illuminate\Support\Facades\DB;
use Mosaiqo\Translatable\Tests\Models\Article;
use Orchestra\Testbench\TestCase;


class TestsBase extends TestCase
{
	protected $queriesCount;

	protected $tablesToRefresh = ['articles'];

	public function setUp()
	{
		parent::setUp();

		$this->resetDatabase();
//		$this->countQueries();
	}

	public function tearDown()
	{

	}

//	public function testRunningMigration()
//	{
////		$country = Country::find( 1 );
////		$this->assertEquals( 'gr', $country->code );
//	}

	protected function getPackageProviders( $app )
	{
		return [
			'Mosaiqo\Translatable\TranslatableServiceProvider',
			'Jenssegers\Mongodb\MongodbServiceProvider',
		];
	}

	protected function getEnvironmentSetUp( $app )
	{
		$app['path.base'] = __DIR__ . '/..';

		$app['config']->set( 'database.default', 'translatable' );
		$app['config']->set( 'database.connections.translatable',[
			'driver'   => 'mongodb',
			'host'     => env( 'MONGO_HOST', 'localhost' ),
			'port'     => env( 'MONGO_PORT', '27017' ),
			'username' => env( 'MONGO_USER', '' ),
			'password' => env( 'MONGO_PASS', '' ),
			'database' => env( 'MONGO_DB', 'mosaiqo' ),
			'options'  => [
				'db' => 'admin' // sets the authentication database required by mongo 3
			]
		]);

		$app['config']->set( 'translatable.locales', [ 'el', 'en', 'fr', 'de', 'id' ] );
	}

	protected function getPackageAliases( $app )
	{
		return [
			'Eloquent' => 'Illuminate\Database\Eloquent\Model',
			'Moloquent' => 'Jenssegers\Mongodb\Model',
		];
	}

	protected function countQueries()
	{
//		$that  = $this;
//		$event = App::make( 'events' );
//		$event->listen( 'illuminate.query', function () use ( $that )
//		{
//			$that->queriesCount ++;
//		} );
	}

	private function resetDatabase()
	{
		// Relative to the testbench app folder: vendors/orchestra/testbench/src/fixture

		foreach ( $this->tablesToRefresh as $table )
		{
			Schema::drop( $table );
		}
		// Migrate to compare

//		$article = Article::create(['commentable' => true]);
//
//		$article->en()->title = 'My title';
//  	$article->es()->title = 'Mi titulo';
//		$article->de()->title = 'Meine Überschrift';
//		$article->ca()->title = 'El meu títol';
//		$article->save();
	}
}
