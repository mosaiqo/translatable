<?php


use Jenssegers\Mongodb\Relations\EmbedsOne;
use Mosaiqo\Translatable\Tests\Models\Article;

class LocaleTest extends TestsBase
{
	/**
	 * @test
	 */
	public function it_returns_the_translation_as_a_relation()
	{
		$article = new Article();
		$article->en()->fill(['title' => 'My title']);
		$article->es()->fill(['title' => 'Mi titulo']);
		$article->save();

		$this->assertTrue( $article->locales->es() instanceof EmbedsOne);
		$this->assertTrue( $article->locales->en() instanceof EmbedsOne);
		$this->assertTrue( $article->locales->de() instanceof EmbedsOne);
		$this->assertTrue( $article->locales->ca() instanceof EmbedsOne);
	}


	/**
	 * @test
	 */
	public function it_returns_all_the_translations()
	{
		$article = new Article();
		$article->en()->fill(['title' => 'My title']);
		$article->es()->fill(['title' => 'Mi titulo']);
		$article->save();

		$this->assertCount(2, $article->locales->getTranslations());
		$this->assertArrayHasKey('en', $article->locales->getTranslations());
		$this->assertArrayHasKey('es', $article->locales->getTranslations());
		$this->assertArrayNotHasKey('de', $article->locales->getTranslations());
	}

}