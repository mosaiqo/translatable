<?php namespace Mosaiqo\Translatable\Models;

use Jenssegers\Mongodb\Model as Moloquent;

/**
 * Class Locale
 *
 * @package Mosaiqo\Translatable\Models
 */
class Locale extends Moloquent
{
	/**
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * @var array
	 */
	protected $fillable = ['es', 'de', 'fr', 'ca', 'en'];


	/**
	 * @param string $method
	 * @param array  $arguments
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\EmbedsMany|mixed
	 */
	public function __call($method, $arguments)
	{
		if(in_array($method, $this->getFillable()))
			return $this->embedsOne($this->getTranslationModel(), $method);

		return parent::__call($method, $arguments);
	}


	/**
	 * @return mixed
	 */
	private function getTranslationModel()
	{
		return $this->getParentRelation()->getParent()->getTranslationModelName();
	}

	/**
	 * @return array
	 */
	public function getTranslations()
	{
		$translations = [ ];
		foreach ( $this->getFillable() as $locale )
		{
			if ( $this->$locale )
			{
				$translations[ $locale ] = $this->$locale()->getResults();
			}
		}

		return $translations;
	}
}