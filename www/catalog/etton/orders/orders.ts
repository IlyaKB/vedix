import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import Utils = require('./../../_common/js/utils');
import Notifications = require('./../../_common/js/notifications');
import KnockoutExt = require('./../../_common/js/knockout_ext');

import EttonPage = require('./../etton');
import EttonList = require('./js/list');
import EttonPositions = require('./js/positions');

/**
 * Orders page
 */
class OrdersPage extends EttonPage {

	private list: EttonList;
	private positions: EttonPositions;
	private form: KnockoutObservable<string>;
	private forms_msg: KnockoutObservable<any>;

	constructor(config: any) {

		super(config);

		KnockoutExt.initSourceData();

		this.form = ko.observable('list'); // Формы: list, detail
		this.forms_msg = ko.observable([]); // Сообщение для реакции других форм на изменения в текущей
		this.forms_msg.subscribe(this.messageForms, this);

		this.list = new EttonList(config, this.forms_msg);
		this.positions = new EttonPositions(config, this.forms_msg);

		$(document).ready(()=>{
			_.delay(()=>{
				ko.applyBindings(this);
				$(document).keyup((ev: any)=>{
					if (ev.which == 27) { // Обработка клавиши Esc (закрытие форм)
						if (this.positions.displayed()) {
							this.forms_msg({'positions': {'hide': true}});
						} else if (this.positions.subtypes.displayed()) {
							this.forms_msg({'subtypes': {'hide': true}});
						} else if (this.form() == 'detail') {
							this.forms_msg({'detail': {'hide': true}});
						}
					}
				});
			},200);
		});
	}

	/**
	 * Обработка сообщений, генерируемых формами
	 */
	private messageForms(forms_msg) {
		_.each(forms_msg, (messages: any, form) => {
			switch (form) {
				case 'list': {
					if (messages.show) this.form('list');
					if (messages.update) this.list.updateList();
					break;
				}
				case 'detail': {
					if (messages.show) this.form('detail');
					if (messages.hide) this.list.detail.closeForm();
					break;
				}
				case 'spec': {
					if (messages.add) {
						var result = this.list.detail.spec.add( messages.add );
						if (! result) this.positions.show();
					}
					break;
				}
				case 'positions': {
					if (messages.show) this.positions.show();
					if (messages.hide) this.positions.hide();
					break;
				}
				case 'subtypes': {
					if (messages.show) this.positions.subtypes.show(messages.show);
					if (messages.hide) this.positions.subtypes.hide();
					break;
				}
			}
		});
	}
}

export = OrdersPage;