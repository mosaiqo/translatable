<?php

use Mosaiqo\Translatable\Tests\Models\Article;

class TranslatableTest extends TestsBase
{

	/**
	 * @test
	 */
	public function it_can_save_locales_by_iso_code()
	{
		$article = new Article();
		$article->en()->title = "title";
		$article->save();

		$this->assertEquals($article->en()->title , 'title');
	}

	/**
	 * @test
	 */
	public function it_do_not_override_call_method()
	{
		$article = new Article();
		$article->en()->title = "title";
		$article->save();


		$article = Article::first([]);

		$this->assertTrue($article instanceof \Mosaiqo\Translatable\Tests\Models\Article);
	}


	/**
	 * @test
	 */
	public function it_can_save_multiple_locales()
	{
		$article              = new Article();
		$article->en()->title = "title";
		$article->es()->title = "titulo";
		$article->ca()->title = "títol";
		$article->save();

		$this->assertEquals( $article->en()->title, 'title' );
		$this->assertEquals( $article->es()->title, 'titulo' );
	}

	/**
	 * @test
	 * @expectedException \Mosaiqo\Translatable\Exceptions\AttributeNotTranslatable
	 */
	public function it_can_only_set_attributes_specified_for_translation()
	{
		$article              = new Article();
		$article->en()->name = "My name";
		$article->es()->title = "titulo";
		$article->ca()->title = "títol";
		$article->save();

		$this->assertEquals( $article->es()->title, 'titulo' );
		$this->assertEquals( $article->ca()->title, 'títol' );
	}

	/**
	 * @test
	 	 */
	public function it_can_update_a_translation()
	{
		$article              = new Article();
		$article->en()->title = "Title";
		$article->en()->slug = "title";
		$article->save();

		$article = Article::first();

		$article->en()->title = "My new title";
		$article->es()->title = "Mi titulo";
		$article->save();

		$article = Article::first();

		$this->assertEquals( $article->en()->title, 'My new title' );
		$this->assertEquals( $article->en()->slug, 'title' );
		$this->assertEquals( $article->es()->title, 'Mi titulo' );
	}
	/**
	 * @test
	 */
	public function fills_the_attributes_correctly()
	{
		$article = new Article();
		$article->fill([
			'en' => [
				'title' => 'My title',
				'slug' => 'my-title'
			]
		]);
		$article->save();

		$this->assertEquals($article->en()->title, 'My title');
	}

	/**
	 * @test
	 */
	public function fills_the_translations_correctly()
	{
		$article = Article::create(['commentable' => 1]);
		$article->en()->fill([
				'title' => 'My title',
				'slug' => 'my-title'
		]);
		$article->save();

		$this->assertEquals($article->en()->title, 'My title');
	}

//	/**
//	 * @test
//	 * @group bug
//	 */
//	public function creates_the_translations_correctly()
//	{
//		$article = Article::create(['commentable' => 1]);
//
//		$article->en([
//			'title' => 'My title',
//			'slug'  => 'my-title'
//		]);
//
//		$this->assertEquals($article->en()->title, 'My title');
//	}


	/**
	 * @test
	 */
	public function creates_locales_by_parents_create_correctly()
	{
		$article = Article::create([
			'en' => [
				'title' => 'My title',
				'slug' => 'my-title'
			],
			'commentable' => true
		]);

		$this->assertEquals($article->en()->title, 'My title');
	}


	/**
	 * @test
	 */
	public function it_only_creates_the_translatable_attributes()
	{
		$article = Article::create([
			'en'          => [
				'whatever' => 'Some text',
			],
			'commentable' => true
		]);

	}

	/**
	 * @test
	 */
	public function it_returns_the_attributes_for_the_default_language()
	{
		$article = Article::create([
			'en'          => [
				'title' => 'My title'
			],
			'es'          => [
				'title' => 'Mi titulo'
			],
			'commentable' => true
		]);

		$article = Article::first();

		$defaultLanguage = app()->getLocale();

		$this->assertEquals($article->title, $article->$defaultLanguage()->title);
	}

	/**
	 * @test
	 */
	public function it_returns_the_property_in_the_language_passed_by()
	{
		$article = Article::create( [
			'en'          => [
				'title' => 'My title'
			],
			'es'          => [
				'title' => 'Mi titulo'
			],
			'ca'          => [
				'title' => 'El meu títol'
			],
			'commentable' => true
		] );

		$this->assertEquals($article->translate('es')->title, 'Mi titulo');
		$this->assertEquals($article->translate('ca')->title, 'El meu títol');
		$this->assertEquals($article->translate('en')->slug, null);
	}

	/**
	 * @test
	 */

	public function it_returns_if_has_translations()
	{
		Article::create( [
			'en'          => [
				'title' => 'My title'
			],
			'es'          => [
				'title' => 'Mi titulo'
			],
			'ca'          => [
				'title' => 'El meu títol'
			],
			'commentable' => true
		] );

		$article = Article::first();

		$this->assertTrue( $article->hasTranslation());
		$this->assertTrue( $article->hasTranslation('es'));
		$this->assertFalse( $article->hasTranslation('fr'));
		$this->assertTrue( $article->isTranslated('ca'));
		$this->assertFalse( $article->isTranslated('jp'));
	}


	/**
	 * @test
	 *
	 * @expectedException \Mosaiqo\Translatable\Exceptions\LocaleNotDefinedException
	 */
	public function it_can_return_fallback_if_not_exist()
	{
		$article = Article::create( [
			'en'          => [
				'title' => 'My title'
			],
			'es'          => [
				'title' => 'Mi titulo'
			],

			'commentable' => true
		]);


		$this->assertEquals($article->translate('es')->title, 'Mi titulo');
		$this->assertEquals($article->translate('ca', true)->title, 'My title');
		$this->assertEquals($article->translateOrDefault('ca')->title, 'My title');
		$this->assertEquals($article->translate('ca', 'es')->title, 'Mi titulo');
		$this->assertEquals($article->translate('ca', 'xx')->title, 'Mi titulo');
	}

	/**
	 * @test
	 *
	 * @group bug#6
	 */
	public function it_deletes_a_locale()
	{
		$article = Article::create([
			'en'          => [
				'title' => 'My title'
			],
			'es'          => [
				'title' => 'To delete'
			],
			'ca'          => [
				'title' => 'El meu títol'
			],
			'commentable' => true
		] );

		$removed = $article->remove('es');

		$article = Article::first();


		$this->assertEquals( $removed->title, 'To delete');

		$this->assertEquals( $article->es()->title, NULL);
		$this->assertEquals($article->en()->title, 'My title');
		$this->assertEquals($article->ca()->title, 'El meu títol');
	}

	/**
	 * @test
	 *
	 * @group bug#8
	 */
	public function it_creates_a_locale_after_deleting()
	{
		$article = Article::create([
			'en'          => [
				'title' => 'My title'
			],
			'es'          => [
				'title' => 'To delete'
			],
			'ca'          => [
				'title' => 'El meu títol'
			],
			'commentable' => true
		] );

		$removed = $article->remove('es');

		$article = Article::first();
		$this->assertEquals( $removed->title, 'To delete');
		$article->save();	

		$this->assertEquals( $article->es()->title, "");
		$this->assertEquals($article->en()->title, 'My title');
		$this->assertEquals($article->ca()->title, 'El meu títol');
	}

	/**
	 * @test
	 */
	public function it_finds_an_item_based_on_relationship()
	{
		Article::create([
			'en'          => [
				'title' => 'My title'
			],
			'es'          => [
				'title' => 'Mi titulo'
			],
			'ca'          => [
				'title' => 'El meu títol'
			],
			'commentable' => true
		] );


		$article  = Article::where('locales.es.title', '=','Mi titulo')->first();

		$this->assertEquals($article->es()->title, 'Mi titulo');
		$this->assertEquals($article->en()->title, 'My title');
		$this->assertEquals($article->ca()->title, 'El meu títol');
	}

	/**
	 * @test
	 */
	public function it_orders_the_results()
	{
		Article::create(['en' => [ 'title' => 'C']]);
		Article::create(['en' => [ 'title' => 'D']]);
		Article::create(['en' => [ 'title' => '1']]);
		Article::create(['en' => [ 'title' => 'B']]);
		Article::create(['en' => [ 'title' => 'A']]);

		$articles  = Article::orderBy('locales.en.title', 'asc')->get();

		$this->assertEquals($articles[0]->title, '1');
		$this->assertEquals($articles[1]->title, 'A');
		$this->assertEquals($articles[2]->title, 'B');
		$this->assertEquals($articles[3]->title, 'C');
		$this->assertEquals($articles[4]->title, 'D');
	}

	/**
	 * @test
	 * @group bug#5
	 */
	public function a_custom_id_can_be_set()
	{
		$article = Article::create( [ 'en' => [ 'title' => 'My title' ] ] );
		$this->assertEquals("en", $article->en()->id);

		config()->set('translatable.custom_id', false );
		$article = Article::create(['en' => ['title' => 'My title']]);
		$this->assertNotEquals("en", $article->en()->id);
	}

	/**
	 * @test
	 * @group bug#3
	 * @group bug#7
	 * @group develop
	 */
	public function a_locales_array_can_be_passed_to_create_them()
	{
		$article = new Article( [
			'otro'        => 'aki',
			'en'          => [ 'title' => 'My title' ],
			'commentable' => true
		] );

		$article->save();

		$this->assertEquals( "My title", $article->en()->title );

		$article = Article::create([
			'locales' => [
				'en' => ['title' => 'My title']
			],
			'commentable' => true
		]);
		$this->assertEquals("My title", $article->en()->title);





	}


}