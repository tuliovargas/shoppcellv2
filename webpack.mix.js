const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.scripts(
	["node_modules/chart.js/dist/Chart.bundle.js"],
	"public/js/plugins.js"
);

mix
	.js("resources/js/app.js", "public/js")
	.vue()
	.sass("resources/sass/app.scss", "public/css")

	//CACHIER
	.js("resources/js/modules/cashier.js", "public/js/module-cashier.js")
	.js("resources/js/modules/clients.js", "public/js/module-clients.js")
	.js("resources/js/modules/products.js", "public/js/module-products.js")
	.js("resources/js/modules/dashboard.js", "public/js/module-dashboard.js")
	.js("resources/js/modules/suppliers.js", "public/js/module-supplier.js")
	.js("resources/js/components/helpers/image.js", "public/js/helpers/image.js");
