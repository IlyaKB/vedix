/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.colorbox.d.ts"/>

import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import jquery_colorbox = require('jquery-colorbox');
//import Utils = require('./../../../_common/js/utils');
//import Notifications = require('./../../../_common/js/notifications');
//import Analytics = require('./../../../_common/js/analytics');
//import HashStateControls = require('./../../../_common/js/hash-state-controls');
//import KnockoutExt = require('./../../../_common/js/knockout_ext');
import Comments = require('./../../../_components/comments/comments');

/**
 * Detail
 */
class MagDetail {

	private hitsPrint: KnockoutObservable<any>;
	private order_count: KnockoutObservable<number>;
	private comments: Comments;

	constructor(config: any) {

		jquery_colorbox;

		this.hitsPrint = ko.observable(0);
		this.order_count = ko.observable(0); // TODO: из кук

		this.comments = new Comments(config);

		$(document).ready(()=> {
			//
		});

		this.bindHandlers(config);
	}

	private bindHandlers(config: any) {
		$('#feature_photos a.gallery').colorbox({ opacity:0.5 , rel:'group1' });
	}
}

export = MagDetail;