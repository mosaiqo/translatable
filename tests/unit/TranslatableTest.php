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
	public function it_dont_overrride_call_method()
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
		$article->ca()->slug = "títol";
		$article->save();

		$this->assertEquals( $article->en()->title, 'titulo' );
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
	 * @expectedException \Illuminate\Database\Eloquent\MassAssignmentException
	 */
	public function it_fails_when_setting_a_not_fillable_property()
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

		$this->assertEquals($article->title, $this->$defaultLanguage()->title);
	}

}