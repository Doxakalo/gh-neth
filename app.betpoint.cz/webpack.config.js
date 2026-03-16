const path = require('path');
const BrowserSyncPlugin = require('browser-sync-v3-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const ESLintPlugin = require('eslint-webpack-plugin');
const FileManagerPlugin = require('filemanager-webpack-plugin');

// load optional local config
const webpackConfLocalFile = 'webpack.config.local.js';
try {
	var webpackConfigLocal = require('./' + webpackConfLocalFile);
} catch (e) {
	if (e instanceof Error && e.code === "MODULE_NOT_FOUND") {
		console.log('No ' + webpackConfLocalFile + ' present, using default config.');
	} else {
		throw e;
	}
}


// paths
const srcPath = './src/';
const distPath = './';


// BrowserSync options
var browserSyncOptions = {
	host: 'localhost',
	port: 3000,
	server: { baseDir: ['www'] }
};

if (webpackConfigLocal && webpackConfigLocal.browserSyncOptions) {
	browserSyncOptions = webpackConfigLocal.browserSyncOptions;
	console.log('Loaded ' + webpackConfLocalFile);
}


// Helper – asset filenames
const generateAssetFilename = (pathData) => {
	const filepath = path
		.dirname(pathData.filename)
		.split("/")
		.slice(1)
		.join("/");
	return `${filepath}/[name][ext][query]`;
}

const generateImageAssetFilename = (pathData) => {
	const filepath = path
		.dirname(pathData.filename)
		.split("/")
		.slice(1, -1)
		.join("/");
	return `${filepath}/[name][ext][query]`;
}


module.exports = (env, argv) => {
	const isProduction = argv.mode === 'production';

	return {
		mode: isProduction ? 'production' : 'development',

		entry: {
			backend: [
				srcPath + 'js/backend/backend.js',
				srcPath + 'sass/backend/backend.scss'
			],
			frontend: [
				srcPath + 'js/frontend/frontend.tsx',
				srcPath + 'sass/frontend/frontend.scss'
			],
			mail: [
				srcPath + 'sass/mail/mail.scss'
			]
		},

		output: {
			path: path.resolve(__dirname, distPath),
			filename: (chunkData) => {
				switch(chunkData.chunk.name) {
					case 'backend':
						return '/backend/web/js/[name].js';
					case 'frontend':
						return '/frontend/web/js/[name].js';
					case 'mail':
						return '/common/mail/js/[name].js';
				}
			},
			assetModuleFilename: (pathData) => {
				const filepath = path
					.dirname(pathData.filename)
					.split("/")
					.slice(1)
					.join("/");
				return `${filepath}/[name][ext][query]`;
			},
		},

		module: {
			rules: [
				{
					test: /\.(js|jsx|ts|tsx)$/,
					exclude: [
						/(node_modules)/,
						path.resolve(__dirname, 'src/js/backend')
					],
					use: 'ts-loader',
				},
				{
					test: /sass\/.*\.(sa|sc|c)ss$/,
					use: [
						MiniCssExtractPlugin.loader,
						"css-loader",
						"postcss-loader",
						"resolve-url-loader",
						{
							loader: "sass-loader",
							options: { sourceMap: true }
						}
					]
				},
				{
					test: /images\/.*\.(png|jpg|jpeg|gif|svg|webp)$/,
					type: 'asset/resource',
					generator: {
						outputPath: 'frontend/web/',
						filename: generateImageAssetFilename,
					}
				},
				{
					test: /fonts\/.*\.(woff|woff2|eot|ttf|otf|svg)$/,
					type: 'asset/resource',
					generator: {
						outputPath: 'frontend/web/',
						filename: generateAssetFilename
					}
				}
			]
		},

		resolve: {
			extensions: [".js", ".jsx", ".ts", ".tsx"]
		},

		performance: {
			hints: "warning",
			maxEntrypointSize: isProduction ? 2 * 1024 * 1024 : 10 * 1024 * 1024,
			maxAssetSize: isProduction ? 1024 * 1024 : 10 * 1024 * 1024,
		},

		devtool: isProduction ? false : 'inline-source-map',

		plugins: [
			new BrowserSyncPlugin(browserSyncOptions),

			new MiniCssExtractPlugin({
				filename: ({ chunk }) => {
					switch (chunk.name) {
						case 'backend':
						case 'frontend':
							return `/${chunk.name}/web/css/${chunk.name}.css`;
						case 'mail':
							return '/common/mail/css/mail.css';
						default:
							return `css/${chunk.name}.css`;
					}
				}
			}),

			new ESLintPlugin({
				configType: 'flat',
			}),

			...(isProduction ? [
				new FileManagerPlugin({
					events: {
						onEnd: {
							copy: [
								{
									source: 'frontend/web/fonts/sbc-icon',
									destination: 'backend/web/fonts/sbc-icon'
								}
							]
						}
					}
				})
			] : [])
		]
	};
};
