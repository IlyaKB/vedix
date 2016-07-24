/// <reference path="../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../core/libs/_def/knockout.d.ts"/>
/// <reference path="../../core/libs/_def/underscore.d.ts"/>

import $ = require('jquery');
import HashStateControls = require('./../_common/js/hash-state-controls');
import BasePage = require('./../main');

import Poll = require('./_components/poll/poll');

/**
 * Page base class 
 */
class SitePage extends BasePage {

	private poll_first: Poll;
	private poll_second: Poll;
	private poll_random: Poll;
    
	constructor(config: any = {}) {

		super(config);

		var elPoll1 = $('#poll_first');
		this.poll_first = (elPoll1.length ? new Poll('#poll_first', config) : null);
		var elPoll2 = $('#poll_second');
		this.poll_second = (elPoll2.length ? new Poll('#poll_second', config) : null);

		var elPollR = $('#poll_random');
		this.poll_random = (elPollR.length ? new Poll('#poll_random', config) : null);

		$(document).ready(()=>{

			$('#admin_link').attr('href', '/admin');
			$('#demo_off').attr('href', '?demo=0');
			$('#demo_on').attr('href', '?demo=1');

			$('#id_uagent').hide().click(()=>{
			  $('#id_uagent').hide();
			  $('#id_uagent_but').show();
			});
		});

		$(window).resize(()=>{
			var h = $(window).height() - $('.header-layer').height() - $('.footer-layer').height();
			$('.middle-layer').css('min-height', h);
		}).resize();
	}
}

export = SitePage;