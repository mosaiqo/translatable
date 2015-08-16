var elixir = require('laravel-elixir');

///*
// |--------------------------------------------------------------------------
// | Elixir Asset Management
// |--------------------------------------------------------------------------
// |
// | Elixir provides a clean, fluent API for defining some basic Gulp tasks
// | for your Laravel application. By default, we are compiling the Less
// | file for our application, as well as publishing vendor resources.
// |
// */
//elixir.config.testing.phpUnit = {
//	path: 'tests',
//
//	// https://www.npmjs.com/package/gulp-phpunit#api
//	options: {
//		debug: true,
//		notify: true,
//		testSuite: "integration",
//		group: 'develop'
//	}
//}


elixir(function (mix) {
	mix.phpUnit('./tests/unit/**/*');
});
