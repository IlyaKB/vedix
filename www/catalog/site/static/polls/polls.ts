/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>

import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
//import KnockoutExt = require('./../../../_common/js/knockout_ext');
import Utils = require('./../../../_common/js/utils');
import Notifications = require('./../../../_common/js/notifications');
import Analytics = require('./../../../_common/js/analytics');
//import HashStateControls = require('./../../_common/js/hash-state-controls');

/**
 * Blog page
 */
class PollsPage {

	constructor(config: any) {

		//KnockoutExt.initSourceData();

		$(document).ready(()=>{
			ko.applyBindings(this);
			//HashStateControls.hashChange();
		});
	}
}

export = PollsPage;