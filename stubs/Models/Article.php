<?php namespace Mosaiqo\Translatable\Tests\Models;

use Illuminate\Database\Eloquent\MassAssignmentException;
use Mosaiqo\Translatable\Translatable;
use Mosaiqo\Translatable\Exceptions\LocaleNotDefinedException;

use Jenssegers\Mongodb\Model as Moloquent;

class Article extends Moloquent
{
	use Translatable;

	protected $fillable = [];

	protected $translatableAttributes = ['title', 'slug'];

	protected $translationModel = 'Mosaiqo\Translatable\Tests\Models\ArticleLocale';

	protected $currentLocales = [];


	public function locale($localeCode)
	{
		if(isset($this->currentLocales[$localeCode]))
		{
			return $this->locales[$localeCode];
		}

		return $this->currentLocales[$localeCode] = new \stdClass();
	}

	/**
	 * @param string $method
	 * @param array  $arguments
	 *
	 * @return mixed|void
	 */
	public function __call($method, $arguments)
	{
		return $this->locale($method);
	}


	public function save(array $options = [])
	{
		foreach($this->currentLocales as $locale => $currentLocale)
		{
			dd( get_object_vars( $currentLocale ));
			if($currentLocale);
		}

		$this->locales = $this->currentLocales;


		parent::save($options);
	}

}