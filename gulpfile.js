var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */
require('laravel-elixir-browserify-official');
require('laravel-elixir-vueify');


elixir(function(mix) {
    
    mix.scripts([
        'node_modules/peerjs/dist/peer.js'
    ], 'public/js/peer.js', './');

    mix.scripts([
        'node_modules/adapterjs/publish/adapter.debug.js'
    ], 'public/js/adapter.debug.js', './');

    mix.scripts([
        'node_modules/adapterjs/publish/adapter.min.js'
    ], 'public/js/adapter.min.js', './');

    mix.scripts([
        'node_modules/adapterjs/publish/adapter.screenshare.js'
    ], 'public/js/adapter.screenshare.js', './');

    mix.scripts([
        'node_modules/adapterjs/publish/adapter.screenshare.min.js'
    ], 'public/js/adapter.screenshare.min.js', './');

    mix.scripts([
        'resources/assets/js/screenshare.js'
    ], 'public/js/screenshare.js', './');

    mix.browserify('app.js');
    mix.sass('app.scss').version("public/css");
});
