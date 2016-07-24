/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>

import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import Utils = require('./../../../_common/js/utils');
import Notifications = require('./../../../_common/js/notifications');

/**
 * Subtypes table
 */
class EttonSubtypes {

	private forms_msg:any; // *KnockoutObservable<any>;
	private positionRow: any = {};
	private visible: KnockoutObservable<boolean>;
	private loading: KnockoutObservable<boolean>;
	private items:KnockoutObservableArray<any>;

	constructor(config:any, forms_msg: KnockoutObservable<any>) {

		this.forms_msg = forms_msg;
		this.visible = ko.observable(false);
		this.loading = ko.observable(false);

		this.items = ko.observableArray([]);

		_.bindAll(this, 'clickRow');

		$(document).ready(()=> {
			//
		});
	}

	public show(positionRow) {
		this.positionRow = positionRow;
		this.updateList();
		$('#back_modal_layer').show();
		this.visible(true);
	}

	public hide() {
		$('#back_modal_layer').hide();
		this.visible(false);
	}

	public displayed() {
		return this.visible();
	}

	private buildParameters() {
		return {
			jx: 'subtypes',
			method: 'get',
			position_id: this.positionRow.id
		};
	}

	public updateList() {

		var self = this;

		this.items([]);
		var parameters = this.buildParameters();

		this.loading(true);
		return $.post('', parameters, (response)=> {
			this.loading(false);
			var data = Utils.getJSONbyText(response); // TODO: JSON-RPC 2.0
			if (data) {
				if (data.success) {
					this.items(data.subtypes);
				}
			} else {
				Notifications.show('Error: ajax data empty!', Notifications.Types.ntError);
			}
		}).fail((data)=> {
			this.loading(false);
			Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
		});
	}

	private cancelClick(obj, ev) {
		this.hide();
		this.forms_msg({'positions': {'show': true}});
	}

	private clickRow(obj, ev) {
		this.positionRow.subtype_id = obj.id;
		this.positionRow.subtype_name = obj.name;
		this.hide();
		this.forms_msg({'spec': {'add': this.positionRow}});
	}
}

export = EttonSubtypes;