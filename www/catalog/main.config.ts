/// <reference path="../core/libs/_def/require.d.ts"/>

require.config({
	enforceDefine: true,
	baseUrl: "/",
	//urlArgs: "v=" + (new Date).getTime(),
	waitSeconds: 30,
	paths: {
		"underscore": "../core/libs/underscore-min",
		"knockout": "../core/libs/knockout-3.2.0.min",
		"jquery": "../core/libs/jquery-2.1.1.min",
		"jquery-ui": "../core/libs/jquery-ui-1.11.1/jquery-ui.min", // NO AMD
		"jquery-ui-core" : "../core/libs/jquery-ui-1.10.4/ui/minified/jquery.ui.core.min", // AMD
		"jquery-ui-widget" : "../core/libs/jquery-ui-1.10.4/ui/minified/jquery.ui.widget.min",
		"jquery-ui-mouse" : "../core/libs/jquery-ui-1.10.4/ui/minified/jquery.ui.mouse.min",
		"jquery-ui-position" : "../core/libs/jquery-ui-1.10.4/ui/minified/jquery.ui.position.min",
		"jquery-ui-menu" : "../core/libs/jquery-ui-1.10.4/ui/minified/jquery.ui.menu.min",
		"jquery-ui-datepicker" : "../core/libs/jquery-ui-1.10.4/ui/minified/jquery.ui.datepicker.min",
		"jquery-ui-draggable" : "../core/libs/jquery-ui-1.10.4/ui/minified/jquery.ui.draggable.min",
		"jquery-ui-slider" : "../core/libs/jquery-ui-1.10.4/ui/minified/jquery.ui.slider.min",
		"jquery-mobile": "../core/libs/jquery.mobile-1.4.4/jquery.mobile-1.4.4.min",
		"jquery-cookie": "../core/libs/jquery.cookie",
		"jquery-colorbox": "../core/libs/colorbox/jquery.colorbox-min",
		"jquery-maskedinput": "../core/libs/jquery.maskedinput",
		"bootstrap3": "../core/libs/bootstrap-3.1.1/js/bootstrap.min",
		"moment": "../core/libs/moment.min",
		"moment-ru": "../core/libs/moment.ru",
		"d3": "../core/libs/d3-3.4.3.min"
	},
	shim: {
		"jquery-ui": { // v1.11.1
			"deps": [
				"jquery"
			],
			"exports": "jQuery.ui"
		},
		"jquery-ui-core": { // v1.10.4 (AMD)
			"deps": [
				"jquery"
			],
			"exports": "jQuery"
		},
		"jquery-ui-widget": {
			"deps": [
				"jquery"
			],
			"exports": "jQuery"
		},
		"jquery-ui-position": {
			"deps": [
				"jquery"
			],
			"exports": "jQuery"
		},
		"jquery-ui-mouse": {
			"deps": [
				"jquery",
				"jquery-ui-widget"
			],
			"exports": "jQuery"
		},
		"jquery-ui-menu": {
			"deps": [
				"jquery",
				"jquery-ui-core",
				"jquery-ui-widget",
				"jquery-ui-position"
			],
			"exports": "jQuery.fn.menu"
		},
		"jquery-ui-datepicker": {
			"deps": [
				"jquery",
				"jquery-ui-core"
			],
			"exports": "jQuery.fn.datepicker"
		},
		"jquery-ui-draggable": {
			"deps": [
				"jquery",
				"jquery-ui-core",
				"jquery-ui-widget",
				"jquery-ui-mouse"
			],
			"exports": "jQuery.fn.draggable"
		},
		"jquery-ui-slider": {
			"deps": [
				"jquery",
				"jquery-ui-core",
				"jquery-ui-widget",
				"jquery-ui-mouse"
			],
			"exports": "jQuery.fn.slider"
		},
		"jquery-mobile": {
			"deps": [
				"jquery"
			],
			"exports": "jQuery"
		},
		"jquery-cookie": {
			"deps": [
				"jquery"
			],
			"exports": "jQuery.cookie"
		},
		"jquery-colorbox": {
			"deps": [
				"jquery"
			],
			"exports": "jQuery.fn.colorbox"
		},
		"jquery-maskedinput": {
			"deps": [
				"jquery"
			],
			"exports": "jQuery.fn.mask"
		},
		"bootstrap3": {
			"deps": [
				"jquery"
			]
		}/*,
		"moment": {
			"deps": [
				"moment-ru"
			],
			"exports": "moment"
		}*/
	}
});