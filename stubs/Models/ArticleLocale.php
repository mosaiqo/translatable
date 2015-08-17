<?php namespace Mosaiqo\Translatable\Tests\Models;

use Jenssegers\Mongodb\Model as Moloquent;

class ArticleLocale extends Moloquent
{
	protected $fillable = ['title', 'slug'];

}