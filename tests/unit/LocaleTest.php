<?php

use Mosaiqo\Translatable\Models\Locale;

class LocaleTest extends TestsBase
{

	/**
	 * @test
	 */
	public function it_has_a_correct_relation_to_the_related_translation( )
	{
		$locale = new Locale();

		$this->assertTrue($locale->es() instanceof \Jenssegers\Mongodb\Relations\EmbedsOne);
		$this->assertTrue($locale->en() instanceof \Jenssegers\Mongodb\Relations\EmbedsOne);
		$this->assertTrue($locale->de() instanceof \Jenssegers\Mongodb\Relations\EmbedsOne);
		$this->assertTrue($locale->ca() instanceof \Jenssegers\Mongodb\Relations\EmbedsOne);

	}
}