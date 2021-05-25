/**
 * External dependencies
 */
const path = require( 'path' );
const IgnoreEmitWebpackPlugin = require( 'ignore-emit-webpack-plugin' );

/**
 * WordPress dependencies.
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const cwd = process.cwd();

module.exports = {
	...defaultConfig,
	entry: {
		// Scripts.
		admin: path.resolve( cwd, 'assets/js', 'admin.js' ),
	},
	output: {
		...defaultConfig.output,
		path: path.resolve( cwd, 'assets/build' ),
	},
	optimization: {
		...defaultConfig.optimization,
		// Disable automatic splitting of modules in to separate files when
		// CSS is detected. CSS is loaded directly through entry points instead
		// of imported within a JS module.
		//
		// https://github.com/WordPress/gutenberg/blob/trunk/packages/scripts/config/webpack.config.js#L114-L119
		splitChunks: {
			...defaultConfig.optimization.splitChunks,
			cacheGroups: {
				...defaultConfig.optimization.splitChunks.cacheGroups,
				style: {},
			},
		},
	},
	plugins: [
		...defaultConfig.plugins,
		// Ignore files starting with `style-` from being emitted.
		new IgnoreEmitWebpackPlugin( /style-(.*).js/ ),
	],
};
