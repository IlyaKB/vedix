/// <reference path="../../core/libs/_def/jquery.d.ts"/>

import $ = require('jquery');

import BasePage = require('./../main');

/**
 * Page base class 
 */
class EttonPage extends BasePage {

	constructor(config: any = {}) {

		super(config);

		$(document).ready(()=>{
			//...
		});

		// TODO: решить на уровне верстки и CSS
		$(window).resize(()=>{
			var h = $(window).height() - $('.header-layer').height() - $('.footer-layer').height();
			$('.middle-layer').css('min-height', h);
		});

		_.delay(()=>{
			$(window).resize();
		}, 400);
	}
}

export = EttonPage;