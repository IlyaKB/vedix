import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import Utils = require('./../../_common/js/utils');
//import User = require('./../_common/js/user');
import Notifications = require('./../../_common/js/notifications');
import Analytics = require('./../../_common/js/analytics');
import HashStateControls = require('./../../_common/js/hash-state-controls');
//import KnockoutExt = require('./../../_common/js/knockout_ext');

import MagazinPage = require('./../magazin');
import MagList = require('./js/list');
import MagDetail = require('./js/detail');

/**
 * Index page
 */
class IndexPage extends MagazinPage {

	private list: MagList;
	private detail: MagDetail;

	constructor(config: any) {

		super(config);

		if (config.is_list) {
			this.list = new MagList(config);
		} else {
			this.detail = new MagDetail(config);
		}

		$(document).ready(()=>{
			_.delay(()=>{ko.applyBindings(this)},200);
		});

		this.bindHandlers(config);
	}

	private bindHandlers(config: any) {

		// TODO: решить на уровне верстки и CSS
		$(window).resize(()=>{
			var elLeft = $('div.left-layer');
			var elLeftBlocks = elLeft.find('div.block');
			var h1 = 0;
			elLeftBlocks.each((i, e)=>{
				h1 += $(e).height();
			});
			var elMiddle = $('div.middle-layer');
			var h2 = Math.max(elMiddle.height() - 5, parseInt(elMiddle.css('min-height')) - 7);
			elLeft.height( Math.max(h1, h2));
		}).resize();
	}

	private addClick(obj, _ev) {
		obj.order_count( obj.order_count() + 1 );
	}

	private delClick(obj, _ev) {
		obj.order_count( Math.max(0, obj.order_count() -	 1) );
		return false;
	}

	private preOrderClick(_obj, _ev) {
		// TODO
	}
}

export = IndexPage;