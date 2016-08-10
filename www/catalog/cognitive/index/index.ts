import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import Utils = require('./../../_common/js/utils');
import Notifications = require('./../../_common/js/notifications');
import User = require('./../../_common/js/user');

import CognitivePage = require('./../cognitive');

import Purse = require('./js/purse');
import VM = require('./js/vm');

/**
 * Index page
 */
class IndexPage extends CognitivePage {

	private client_purse: Purse; // Кошелёк клиента
	private vm: VM; // Виртуальный аппарат по продаже кофе (Vending Machine)

	constructor(config: any) {

		super(config);

		// Ключ - номинал монеты
		this.client_purse = new Purse(config, 10, 30, 20, 15);
		this.vm = new VM(config);

		_.bindAll(this, 'clickMonet');

		$(document).ready(()=>{
			_.delay(()=>{ko.applyBindings(this)},200);
		});
	}

	/**
	 * Положить монету в VM
	 * @param obj
	 * @param ev
	 */
	private clickMonet(obj, ev) {
		var nominal = $(ev.target).closest('span').data('nominal');
		if (! nominal) return;

		if (this.client_purse.remove(nominal)) {
			this.vm.putMonet(nominal);
		}
	}

	/**
	 * Вернуть сдачу
	 * @param obj
	 * @param ev
	 */
	private clickShortChange(obj, ev) {
		this.client_purse.add(this.vm.shortChange());
	}
}

export = IndexPage;