/// <reference path="../../core/libs/_def/jquery.d.ts"/>

import $ = require('jquery');
import User = require('./../_common/js/user');
import HashStateControls = require('../_common/js/hash-state-controls');

/**
 * Page base class 
 */
class MagazinPage {

  	public user: User;
    
	constructor(private config: any = {}) {

		this.user = new User(config);

		$(document).ready(()=>{
			$('#admin_link').attr('href', '/admin');
			$('#demo_off').attr('href', '?demo=0');
			$('#demo_on').attr('href', '?demo=1');
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

export = MagazinPage;