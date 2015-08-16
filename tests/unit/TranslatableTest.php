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
	 * @expectedException Mosaiqo\Translatable\Exceptions\AttributeNotTranslatable
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


}