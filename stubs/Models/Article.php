<?php namespace Mosaiqo\Translatable\Tests\Models;

use Illuminate\Database\Eloquent\MassAssignmentException;

use Mosaiqo\Translatable\Exceptions\AttributeNotTranslatable;
use Mosaiqo\Translatable\Exceptions\LocaleNotDefinedException;

use Mosaiqo\Translatable\Translatable;

use Jenssegers\Mongodb\Model as Moloquent;

class Article extends Moloquent
{
	use Translatable;

	protected $availableLocales = ['es', 'en', 'de', 'fr', 'ca'];

	protected $fillable = ['title', 'slug', 'commentable'];

	protected $translatableAttributes = ['title', 'slug'];

	protected $translationModel = 'Mosaiqo\Translatable\Tests\Models\ArticleLocale';

	protected $currentLocales = [];


	public function locale($localeCode)
	{

		if(isset($this->currentLocales[$localeCode]))
		{
			$localeTranslation =  $this->currentLocales[$localeCode];
		}

		if(isset($this->locales[$localeCode]))
		{
			$localeTranslation = new \stdClass;
			foreach($this->locales[$localeCode] as $key => $value)
			{
				$localeTranslation->$key = $value;
			}
		}
		else
		{
			$localeTranslation = new \stdClass;
		}

		return $this->currentLocales[$localeCode] = $localeTranslation;
	}

	public function fill(array $attributes)
	{
		foreach($attributes as $locale => $parameters)
		{
			if(in_array($locale, $this->availableLocales))
			{
				$localeTranslation = $this->locale($locale);

				foreach($parameters as $attribute => $value)
				{
					if (!in_array($attribute, $this->getFillable()))
					{
						throw new MassAssignmentException;
					}

					$localeTranslation->$attribute = $value;
				}

				unset($attributes[$locale]);
			}
		}
		parent::fill($attributes);
	}

	public function __get($attribute)
	{
		if(in_array($attribute, $this->translatableAttributes))
		{
			$locale = app()->getLocale();
			return $this->locale($locale)->$attribute;
		}

		return parent::__get($attribute);
	}

	/**
	 * @param string $method
	 * @param array $parameters
	 *
	 * @return mixed|void
	 */
	public function __call($method, $parameters)
	{
		if(in_array($method, $this->availableLocales))
		{
			return $this->locale($method);
		}

		return parent::__call($method, $parameters);
	}


	public function save(array $options = [])
	{
		foreach($this->currentLocales as $locale => $currentLocale)
		{
			foreach(get_object_vars( $currentLocale ) as $key =>  $var)
			{
				if(!in_array($key, $this->translatableAttributes))
					throw new AttributeNotTranslatable;
			}
		}

		$this->locales = $this->currentLocales;


		parent::save($options);
	}

}