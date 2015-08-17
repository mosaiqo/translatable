<?php namespace Mosaiqo\Translatable\Tests\Models;

use Mosaiqo\Translatable\Translatable;

use Jenssegers\Mongodb\Model as Moloquent;

class Article extends Moloquent
{
	use Translatable;

	protected $fillable = ['commentable'];

	protected $translatableAttributes = ['title', 'slug'];

	protected $translationModel = 'Mosaiqo\Translatable\Tests\Models\ArticleLocale';

}