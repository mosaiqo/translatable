<?php namespace Mosaiqo\Translatable;

use Illuminate\Database\Eloquent\Collection;
use Mosaiqo\Translatable\Exceptions\AttributeNotTranslatable;
use Mosaiqo\Translatable\Exceptions\LocaleNotDefinedException;

/**
 * Class Translatable
 *
 * @package Mosaiqo\Translatable
 */
trait Translatable
{
	/**
	 * @var array
	 */
	protected $currentLocales = [];

	/**
	 * @var array
	 */
	static protected $availableLocales = [];


	/**
	 * Boots up the trait and doo some necessary stuff
	 * before it can be used.
	 */
	public static function bootTranslatable()
	{
		// Set available locales for the app
		self::$availableLocales = (array) config('translatable.locales', [] );
	}

	/**
	 * Declares the relation for the locales.
	 * @return mixed
	 */
	public function locales()
	{
		return $this->embedsOne( \Mosaiqo\Translatable\Models\Locale::class, 'locales' );
	}

	/**
	 * Return or create a new locale for the given localeCode
	 * @param string $localeCode Locale ISO code for the language
	 *
	 * @return mixed|null
	 */
	protected function locale( $localeCode, $parameters = [] )
	{
		$localeTranslation = null;

		if ( $locales = $this->locales()->getResults() )
		{
			$localeTranslation = $locales->$localeCode()->getResults();
		}

		if ( ! $locales || ! $localeTranslation )
		{
			$localeTranslation = $this->createNewLocaleTranslation($parameters);

			if($this->exists)
			{
				$locale = $this->locales()->getResults();
				$locale->$localeCode()->associate( $localeTranslation );
				$this->locales()->associate( $locale );
				$this->save();
			}
		}

		return $this->currentLocales[ $localeCode ] = $localeTranslation;
	}

	/**
	 * Fill the model with an array of attributes.
	 *
	 * @param  array $attributes
	 *
	 * @return $this
	 *
	 * @throws \Illuminate\Database\Eloquent\MassAssignmentException
	 */
	public function fill( array $attributes )
	{
		foreach ( $attributes as $locale => $parameters )
		{
			if ( in_array( $locale, $this->getAvailableLocales() ) )
			{
				$localeTranslation = $this->locale($locale);
				$newFillable       = array_merge_recursive( $localeTranslation->getFillable(), $this->getTranslatableAttributes() );
				$localeTranslation->fillable( $newFillable );
				$localeTranslation->fill( $parameters );
				unset( $attributes[ $locale ] );
			}
		}
		return parent::fill( $attributes );
	}

	/**
	 * @param $attribute
	 *
	 * @return mixed
	 */
	public function __get( $attribute )
	{
		if ( in_array( $attribute, $this->getTranslatableAttributes() ) )
		{
			$locale = app()->getLocale();
			return $this->locale( $locale )->$attribute;
		}

		return parent::__get( $attribute );
	}

	/**
	 * @param string $method
	 * @param array  $parameters
	 *
	 * @return mixed|void
	 */
	public function __call( $method, $parameters )
	{
		if ( in_array( $method, $this->getAvailableLocales() ) )
		{
			return $this->locale( $method, $parameters);
		}

		return parent::__call( $method, $parameters );
	}


	/**
	 * Save the model to the database.
	 *
	 * @param  array $options
	 *
	 * @throws AttributeNotTranslatable
	 * @return bool
	 */
	public function save( array $options = [ ] )
	{
		if ( $this->getAttribute('locales') )
			$localeTranslation = $this->locales()->getResults();
		else
			$localeTranslation = $this->locales()->create([]);


		foreach ( $this->currentLocales as $locale => $currentLocale )
		{
			if($this->isTranslatable( $currentLocale ))
			{
				$currentLocale->fireModelEvent('saving');
				$localeTranslation->$locale()->associate( $currentLocale );
			}
		}

		$localeTranslation->fireModelEvent( 'saving' );
		$this->locales()->associate( $localeTranslation );

		$this->currentLocales = [];

		return parent::save( $options );
	}


	/**
	 * Translates a to a given localeCode
	 *
	 * @param      $locale
	 *
	 * @param bool $withFallback
	 *
	 * @return Model
	 * @throws LocaleNotDefinedException
	 */
	public function translate($locale , $withFallback = null)
	{


		if ( ! $this->hasTranslation( $locale ) )
		{
			if(is_string($withFallback))
			{
				$locale = $withFallback;
			}

			if (is_bool( $withFallback ) )
			{
				$locale = $this->getFallbackLocale();
			}

			if(! in_array( $withFallback, $this->getAvailableLocales() ) )
			{
				throw new LocaleNotDefinedException;
			}

		}
		return $this->locale( $locale );
	}

	/**
	 * Returns the translations
	 * @return mixed
	 */
	public function translations()
	{
		return Collection::make( $this->locales()->getResults()->getTranslations() );
	}

	/**
	 *
	 * Returns whether if its translated or not.
	 * @param null $locale
	 *
	 * @return bool
	 */
	public function hasTranslation( $locale = null )
	{
		if(is_null($locale))
			$locale = $this->getFallbackLocale();

		if( $this->getAttribute( 'locales' ) )
		{
			$locales = $this->locales()->getResults();
			if($locales && $locales->getAttribute( $locale ) )
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * An alias for the hasTranslation method
	 * @param $locale
	 *
	 * @return bool
	 */
	public function isTranslated( $locale )
	{
		return $this->hasTranslation($locale);
	}


	/**
	 * Simple method to easy get the translation or default fallback
	 * @param $locale
	 *
	 * @return Model
	 * @throws LocaleNotDefinedException
	 */
	public function translateOrDefault($locale)
	{
		return $this->translate($locale, true);
	}


	/**
	 * Creates the translation model via the IOC
	 * @return mixed
	 */
	private function createNewLocaleTranslation($parameters = [])
	{
		return app()->make($this->getTranslationModelName(), $parameters );
	}

	/**
	 * Gets the translations model name
	 * @return string
	 */
	public function getTranslationModelName()
	{
		return $this->translationModel?: $this->getDefaultTranslationName();
	}

	/**
	 * Figures out the default translation model name.
	 * @return string
	 */
	public function getDefaultTranslationName()
	{
		return get_class( $this ) . config('translatable.locale_suffix', 'Locale');
	}

	/**
	 * Gets the available locales to use
	 * @return array
	 */
	protected function getAvailableLocales()
	{
		return self::$availableLocales;
	}

	/**
	 * Gets the attributes that can be translated
	 * @return mixed
	 */
	protected function getTranslatableAttributes()
	{
		return $this->translatableAttributes;
	}

	/**
	 * Determines if it is translatable
	 * @param $currentLocale
	 *
	 * @throws AttributeNotTranslatable
	 * @return bool
	 */
	protected function isTranslatable( $currentLocale )
	{
		foreach ( $currentLocale->getAttributes() as $key => $var )
		{
			if ( ! in_array( $key, $this->getTranslatableAttributes() ) )
			{
				throw new AttributeNotTranslatable;
			}
		}

		return true;
	}

	/**
	 * Gets the application fallback_locale
	 * @return mixed
	 */
	protected function getFallbackLocale()
	{
		return config('app.fallback_locale', 'en');
	}
}