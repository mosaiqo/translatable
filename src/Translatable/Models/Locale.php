<?php namespace Mosaiqo\Translatable\Models;

use Jenssegers\Mongodb\Model as Moloquent;
use Mosaiqo\Translatable\Exceptions\LocaleNotDefinedException;
use Mosaiqo\Translatable\Tests\Models\ArticleLocale;

class Locale extends Moloquent
{
	public $timestamps = false;

	protected $fillable = ['es', 'de', 'fr', 'ca', 'en'];


	public function __call($method, $arguments)
	{
		if(in_array($method, $this->getFillable()))
			return $this->embedsOne($this->getTranslationModel(), $method);

		return parent::__call($method, $arguments);
	}

	private function getTranslationModel()
	{
		return $this->getParentRelation()->getParent()->getTranslationModelName();
	}

}