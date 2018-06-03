var webpack = require("webpack");

module.exports = {
	entry: "./app/main.ts",
	output:{
		path:'./dist',
		filename:'bundle.js'
	},
	module:{
		loaders:[
		{test:/\.ts$/,loader:'ts'},
			{
				test:/\.css$/,
				loader:'style-loader!css-loader'
			}
		]
	},
	resolve:{
		extensions:["",'.js','.ts']
	}
}