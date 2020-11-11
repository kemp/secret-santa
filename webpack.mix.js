const mix = require('laravel-mix');
const tailwind = require('laravel-mix-tailwind');

mix.sass('resources/sass/app.scss', 'public/css')
      .options({
        processCssUrls: false,
      })
    .tailwind()
    .version();
