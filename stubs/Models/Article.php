<?php namespace Mosaiqo\Translatable\Tests\Models;

use Illuminate\Database\Eloquent\MassAssignmentException;

use Mosaiqo\Translatable\Exceptions\AttributeNotTranslatable;

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


	public function locales()
	{
		return $this->embedsOne(\Mosaiqo\Translatable\Models\Locale::class, 'locales');
	}

	protected function locale($localeCode)
	{
		$localeTranslation = null;
		if ($locales = $this->locales()->getResults() )
		{
			$localeTranslation = $locales->$localeCode()->getResults();
		}

		if( !$locales || !$localeTranslation )
		{
			$localeTranslation = $this->createNewLocaleTranslation();
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
				$newFillable = array_merge_recursive($localeTranslation->getFillable(),$this->translatableAttributes);
				$localeTranslation->fillable($newFillable);

				$localeTranslation->fill($parameters);

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
		if( $this->locales)
			$localeTranslation = $this->locales()->getResults();
		else
			$localeTranslation = $this->locales()->create([]);

		foreach($this->currentLocales as $locale => $currentLocale)
		{

			foreach($currentLocale->getAttributes() as $key =>  $var)
			{
				if(!in_array($key, $this->translatableAttributes))
					throw new AttributeNotTranslatable;
			}

			$localeTranslation->$locale()->associate($currentLocale);
		}

		$this->locales()->associate($localeTranslation);

		$this->currentLocales = [];
		return parent::save($options);

	}


	public function translate($locale)
	{
		$locale = $this->locale( $locale );

		return $locale;
	}

	private function createNewLocaleTranslation()
	{
		return app()->make( $this->translationModel );
	}

	public function getTranslationModelName()
	{
		return $this->translationModel;
	}

}