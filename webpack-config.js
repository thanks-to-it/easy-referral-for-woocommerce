const path = require('path'),
	WPGetPluginData = require('wp-get-plugin-data'),
	Webpack = require("webpack"),
	fs = require("fs"),
	AutoPrefixer = require('autoprefixer'),
	LiveReloadPlugin = require('webpack-livereload-plugin'),
	MiniCssExtractPlugin = require('mini-css-extract-plugin'),
	UglifyJSPlugin = require('uglifyjs-webpack-plugin'),
	OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin'),
	StyleLintPlugin = require('stylelint-webpack-plugin');

// Javascript File Header Comments
const preamble = fs.readFileSync('./.preamble', 'utf8');

/*WPGetPluginData('easy-referral-for-woocommerce.php').then(function(parsedFile){
	console.log(parsedFile);
});*/

/*const mainPluginFile = fs.readFileSync('./easy-referral-for-woocommerce.php', 'utf8');
const pluginInfs = (file) => {
	let infsToGet = [
		{name: 'name', 'regex': file.match(/(?<=\*\s?plugin Name\:).+$/igm)},
		{name: 'author', 'regex': file.match(/(?<=\*\s?author\:).+$/igm)},
	];
	let infs = infsToGet.map((inf) => ({[inf.name]: inf.regex ? inf.regex[0].trim() : ''})).reduce(((r, c) => Object.assign(r, c)), {});
	return infs;
}
console.log(pluginInfs(mainPluginFile));*/









// Development
const devConfig = {
	context: __dirname,
	entry: {
		frontend: ['./src/assets/js/frontend/frontend-index.js', './src/assets/scss/frontend/frontend.scss'],
		admin: ['./src/assets/js/admin/admin-index.js', './src/assets/scss/admin/admin.scss']
	},
	output: {
		path: path.resolve(__dirname, 'assets'),
		filename: '[name].js'
	},
	mode: 'development',
	devtool: 'source-map',
	module: {
		rules: [
			{
				enforce: 'pre',
				exclude: /node_modules/,
				test: /\.jsx?$/,
				loader: 'eslint-loader',
				options: {
					fix: true,
				},
			},
			{
				exclude: /node_modules/,
				test: /\.jsx?$/,
				loader: 'babel-loader',
				options: {
					presets: ["@babel/preset-env"]
				}
			},
			{
				test: /\.(scss|css)$/,
				use: [
					MiniCssExtractPlugin.loader, {loader: "css-loader"},
					{
						loader: "postcss-loader",
						options: {
							autoprefixer: {browsers: ["last 2 versions"]},
							plugins: () => [AutoPrefixer]
						},
					},
					{
						loader: "sass-loader",
						options: {}
					}
				]
			}
		]
	},
	plugins: [
		new LiveReloadPlugin({}),
		new StyleLintPlugin({context: __dirname + "/src/assets/scss"}),
		new MiniCssExtractPlugin({filename: '[name].css'}),
		new Webpack.BannerPlugin({
			banner: preamble,
			raw: true,
			entryOnly: true,
		}),
	],
	optimization: {
		minimize: false
	}
};

// Production
const prodConfig = {
	...devConfig,
	mode: 'production',
	output: {
		path: path.resolve(__dirname, 'assets'),
		filename: '[name].min.js'
	},
	plugins: [
		new StyleLintPlugin({fix: true, context: __dirname + "/src/assets/scss"}),
		new MiniCssExtractPlugin({filename: '[name].min.css'}),
	],
	optimization: {
		minimizer: [new UglifyJSPlugin({
			uglifyOptions: {
				output: { // See https://github.com/mishoo/UglifyJS2#output-options
					beautify: false,
					comments: 'some',
					preamble: preamble
				},
			}
		}), new OptimizeCssAssetsPlugin()]
	}
}

module.exports = (env, argv) => {
	switch (argv.mode) {
		case 'production':
			return prodConfig;
		default:
			return devConfig;
	}
}