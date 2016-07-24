import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import Utils = require('./../../_common/js/utils');
import Notifications = require('./../../_common/js/notifications');
import User = require('./../../_common/js/user');

import EttonPage = require('./../etton');

/**
 * Index page
 */
class IndexPage extends EttonPage {

	constructor(config: any) {

		super(config);

		$(document).ready(()=>{
			_.delay(()=>{ko.applyBindings(this)},200);
		});

		this.bindHandlers(config);
	}

	private bindHandlers(config: any) {
		//
	}

	private clickAuth(obj, ev) {
		this.user.auth();
	}
}

export = IndexPage;