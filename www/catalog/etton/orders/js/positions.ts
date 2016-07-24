/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>

import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import Utils = require('./../../../_common/js/utils');
import Notifications = require('./../../../_common/js/notifications');

import EttonSubtypes = require('./subtypes');

/**
 * Positions table
 */
class EttonPositions {

	private forms_msg:any; // *KnockoutObservable<any>;
	private visible: KnockoutObservable<boolean>;
	private isInitialized: boolean = false;
	private loading: KnockoutObservable<boolean>;
	private items:KnockoutObservableArray<any>;
	private positionRow: any = {};

	public subtypes: EttonSubtypes;

	constructor(config:any, forms_msg:KnockoutObservable<any>) {

		this.forms_msg = forms_msg;
		this.visible = ko.observable(false);
		this.loading = ko.observable(false);

		this.items = ko.observableArray([]);

		this.subtypes = new EttonSubtypes(config, forms_msg);

		_.bindAll(this, 'clickRow');

		$(document).ready(()=> {
			//
		});
	}

	public show() {
		if (! this.isInitialized) {
			this.updateList();
		}
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
			jx: 'positions',
			method: 'get'
		};
	}

	public updateList() {

		var self = this;

		var parameters = this.buildParameters();

		this.loading(true);
		return $.post('', parameters, (response)=> {
			this.loading(false);
			var data = Utils.getJSONbyText(response); // TODO: JSON-RPC 2.0
			if (data) {
				if (data.success) {
					this.items(data.positions);
					this.isInitialized = true;
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
	}

	private clickRow(obj, ev) {
		this.positionRow = obj;
		if (obj.subtypes_count != 0) {
			this.hide();
			this.forms_msg({'subtypes': {'show': this.positionRow}});
		} else {
			this.hide();
			this.forms_msg({'spec': {'add': this.positionRow}});
		}
	}
}

export = EttonPositions;