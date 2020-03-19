const mix = require('laravel-mix')
const tailwind = require('tailwindcss')
const { CleanWebpackPlugin } = require('clean-webpack-plugin')
const webpack = require('webpack')
require('laravel-mix-purgecss')

mix.copy('node_modules/element-ui/lib/theme-chalk/fonts', 'public/css/fonts')
mix.js('resources/js/app.js', 'public/js').extract()
mix.sass('resources/sass/app.scss', 'public/css').options(
{
    processCssUrls: false,
    postCss: [
        tailwind('resources/sass/tailwind.js'),
    ]
})

if (mix.inProduction()) {
	mix.version().purgeCss()
}

mix.webpackConfig({
  	resolve: {
    	alias: {
            '@': __dirname + '/resources/js',
            'vue$': 'vue/dist/vue.runtime.js'
    	},
  	},
  	plugins: [
    	new CleanWebpackPlugin({
    		cleanOnceBeforeBuildPatterns: ['js/*', 'css/*'],
    	})
  	],
})
