<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Suffix
	|--------------------------------------------------------------------------
	|
	| Defines the default suffix for the translation. For example, if you
	| want to use ArticleTranslation instead of ArticleLocale
	| application, set this to 'Translation'.
	|
	*/
	'suffix' => 'Locale',
	/*
	|--------------------------------------------------------------------------
	| Application Locales
	|--------------------------------------------------------------------------
	|
	| Contains an array with the available locales for the application.
	|
	*/
	'locales' => [ 'es', 'en', 'fr', 'de', 'ca' ],
	/*
	|--------------------------------------------------------------------------
	| Locale key
	|--------------------------------------------------------------------------
	|
	| Defines the 'locales' field name, which is used to make the relation
	| for the translation model.
	|
	*/
	'locale_key' => 'locales',

	/*
	|--------------------------------------------------------------------------
	| Custom ID
	|--------------------------------------------------------------------------
	|
	| Enable to have a custom ID, instead the normal one it uses the localeCode
	|
	*/
	'custom_id' => true,

];