const path = require('path');
const ExtractTextWebpackPlugin = require('extract-text-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const browsers = [ 'ie 10', 'last 2 versions' ];
module.exports = {
	mode: 'development', 
	entry: './src/main.js', 
	output: {
		path: path.resolve(__dirname, 'dist'), 
		filename: '[name].js', 
	}, 
	plugins: [
		new ExtractTextWebpackPlugin(
			'[name].css', 
		), 
		new HtmlWebpackPlugin({
			title: 'webpack development', 
		}), 
	], 
	module: {
		rules: [
			{
				test: /\.jsx?$/, 
				exclude: /node_modules/, 
				use: [
					{
						loader: 'babel-loader', 
						options: {
							presets: [
								[ 'env', { targets: { browsers } } ], 
								'react', 
							], 
							plugins: [
								'transform-object-rest-spread', 
								'transform-class-properties', 
								'transform-decorators-legacy', 
							], 
						}, 
					}, 
				], 
			}, 
			{
				test: /\.(css|s[ac]ss)$/, 
				use: ExtractTextWebpackPlugin.extract({
					fallback: 'style-loader', 
					use: [
						{
							loader: 'css-loader', 
						}, 
						{
							loader: 'postcss-loader', 
							options: { plugins: [ require('autoprefixer')({
								targets: browsers, 
							})]}, 
						}, 
						{
							loader: 'sass-loader', 
						}, 
					], 
				}), 
			}, 
			{
				test: /\.(eot|ttf|woff|woff2|svg)$/, 
				use: [
					{
						loader: 'file-loader', 
						options: {
							name: path => path.replace(/^.*\/node_modules\//, 'fonts/vendor/'), 
						}, 
					}, 
				], 
			},  
		], 
	}, 
};