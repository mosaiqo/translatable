[![Stories in Ready](https://badge.waffle.io/mosaiqo/translatable.png?label=ready&title=Ready)](https://waffle.io/mosaiqo/translatable)
# Mosaiqo Translatable
----------------------------------------------

[![Latest Stable Version](https://poser.pugx.org/mosaiqo/translatable/v/stable)](https://packagist.org/packages/mosaiqo/translatable)
[![Total Downloads](https://poser.pugx.org/mosaiqo/translatable/downloads)](https://packagist.org/packages/mosaiqo/translatable)
[![Latest Unstable Version](https://poser.pugx.org/mosaiqo/translatable/v/unstable)](https://packagist.org/packages/mosaiqo/translatable)
[![License](https://poser.pugx.org/mosaiqo/translatable/license)](https://packagist.org/packages/mosaiqo/translatable)


This is a Laravel 5 package to have multi language models.
It is prepared to use with MongoDB and Eloquent (it is planed to make it work also with MySQL).

This package was created to easily store the multi language models by `embedsOne` relations.
Its a **Plug & Play package**, if you are using [Laravel MondoDB](https://github.com/jenssegers/laravel-mongodb).




# Instalation
----------------------------------------------

## Install it via composer


##### Require it with composer :

```shell
 composer require mosaiqo/translatable
```

##### Add this to your `config/app.php`

```php
 Mosaiqo\Translatable\TranslatableServiceProvider:class,
```

##### Publish the config:
You can change the values, if you like.


```shell
php artisan vendor:publish --provider="Mosaiqo\Translatable\TranslatableServiceProvider" --tag="config"
```




# Use it
----------------------------------------------

### Models

1. You need to `use` the `Mosaiqo\Translatable\Traits\Translatable` in the models you want to have multi language ability.
2. You also need to create a Translation model. The convention is to use the model's name for that you want to use with translatable and append `Locale` to it (You can override it in the config file).
3. Optional you can specify the model to use for the translation, if not set it will search by default for a model with the current `modelname` and append `Locale` to it.
4. Set the attributes you want to be translatable with `protected $translatableAttributes`.


```php
<?php namespace App;

use Jenssegers\Mongodb\Model;

class Article extends Model 
{

    use \Mosaiqo\Translatable\Traits\Translatable;

    protected $fillable = ['commentable'];

	/**
	 * This are the attributes you want to have in multiple languages.
	 */
	protected $translatableAttributes = ['title', 'slug'];

	
	/**
	 * This is optional by default it will search for App\ArticleLocale.
	 */ 
	protected $translationModel = 'Mosaiqo\Translatable\Tests\Models\ArticleLocale'; 
	

}
```

The translation model should look similar to this.
It is necesary to make the attributes fillable for the MassAsignment.

```php
<?php namespace App;

use Jenssegers\Mongodb\Model;

class ArticleLocale extends Eloquent 
{

    protected $fillable = ['title', 'slug'];

}
```



# Documentation
----------------------------------------------
#### Create a Multilanguage Model

It's very esay like with a normal model, the only difference is that you pass 
the translatable attributes inside an array and the key is the locale value.

```php
$article = Article::create([
	'commentable' => true,
	'published'   => true,
	'en' => [
		'title' => 'My title',
		'body'  => 'This is the text for my new post ...'
	]
]);
```

There is an other method, you can first create the model and than assign it the 
translations.

```php
$article = Article::create([
	'commentable' => true
]);
	
	
$article->en([
	'title' => 'My title',
	'body'  => 'This is the text for my new post ...'
]);

$article->es([
	'title' => 'Mi titulo',
	'body'  => 'Este es mi texto para mi nuevo post ...'
]);	
```

#### Get the translations
Assuming you have a model stored in the DDBB.

```php
$article = Article::first();
```
This will output the attribute in the language you use, in this particular cas `es()` will return 'Mi titulo'.

```php
echo $article->es()->title; 
```
And here 'My title'.

```php
echo $article->en()->title; 	
```

You also can assign it to a variable to use it later.

```php
$article_es = $article->es();
$article_en = $article->en();
```
	
There is a more verbose mode if you like.	
	
```php
$article->translate('es')->title;	
```
What if the translation doesn't exists, doesn't mather you can try with the default fallback language if the one you need is not available, just passing the second argument `true`.

```php
$article->translate('de', true)->title;
```
Or you can use the alias for it

```php
$article->translateOrDefault('de')->title;
```	
	
You can also set the fallback language for this particular call if you want to display it diferent.

```php
$article->translate('de', 'es')->title;
```

You can know if a Model has a translation or not. This will return wheter true/false.

```php
$article->hasTranslation('fr');
$article->hasTranslation('en');

// or

$article->isTranslated('en');
$article->isTranslated('ca');
```

You can get the holw translations instance

```php
$article->translations();
```






















