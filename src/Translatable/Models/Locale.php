<?php namespace Mosaiqo\Translatable\Models;

use Jenssegers\Mongodb\Model as Moloquent;
use Mosaiqo\Translatable\Exceptions\LocaleNotDefinedException;
use Mosaiqo\Translatable\Tests\Models\ArticleLocale;

class Locale extends Moloquent
{
	public $timestamps = false;

	protected $fillable = ['es', 'de', 'fr', 'ca', 'en'];

}