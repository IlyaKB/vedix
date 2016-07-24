/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>

import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import Utils = require('./../../../_common/js/utils');
import Notifications = require('./../../../_common/js/notifications');

import EttonDetail = require('./detail');

/**
 * List
 */
class EttonList {

	private isInitialized: boolean = false;
	private inputDelayMs: number = 250; // сколько ждём мс перед отправкой ajax-запроса после key/mouse-событий (сортировка)

	private forms_msg: any; // *KnockoutObservable<any>;
	private loading: KnockoutObservable<boolean>;

	private items: KnockoutObservableArray<any>;
	private sorting_field: KnockoutObservable<string>;
	private sorting_dir: KnockoutObservable<string>;
	private sorting_dir_text: any; // KnockoutComputedFunctions<string>;

	public detail: EttonDetail;

	constructor(config: any, forms_msg: KnockoutObservable<any>) {

		this.forms_msg = forms_msg;
		this.loading = ko.observable(false);

		this.items = ko.observableArray([]);
		this.sorting_field = ko.observable('');
		this.sorting_dir = ko.observable('');

		this.sorting_dir_text = ko.computed(()=>{
			return (this.sorting_dir() == 'asc' ? '^' : 'v');
		}, this);

		ko.computed(this.updateList, this).extend({ throttle: this.inputDelayMs });

		this.detail = new EttonDetail(config, this.forms_msg);

		_.bindAll(this, 'updateList', 'clickTR', 'clickEdit', 'clickAdd', 'clickDelete');

		$(document).ready(()=> {
			//
		});

		this.isInitialized = true;
	}

	private sortClick(obj, ev) {
		var f = $(ev.target).closest('th').data('clm');
		if (f) {
			if (this.sorting_field() == f) {
				var s = this.sorting_dir();
				this.sorting_dir( s == 'asc' ? 'desc' : 'asc' );
			} else {
				this.sorting_field(f);
				this.sorting_dir('asc');
			}
		}
	}

	private buildParameters() {
		return {
			jx: 'list',
			sortfield: this.sorting_field(),
			sortdir: this.sorting_dir(),
		};
	}

	public updateList() {

		var self = this;

		var parameters = this.buildParameters();

		if (! this.isInitialized) return; // Первое отображение данных получаем обычным GET, требовалось лишь инициализировать вложенные ko-объекты в this.updateList

		this.loading(true);

		return $.ajax({
			url: '',
			type: 'POST',
			data: $.param(parameters),
			success: (response)=> {
				this.loading(false);
				var data = Utils.getJSONbyText(response); // TODO: JSON-RPC 2.0
				if (data) {
					if (data.success) {
						this.items(data.orders);
					}
				} else {
					Notifications.show('Error: ajax data empty!', Notifications.Types.ntError);
				}
			},
			error: (response)=>{
				this.loading(false);
				Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
			}
		});
	}

	private clickRefresh(obj, ev) {
		this.updateList();
	}

	private clickTR(obj, ev) {
		if ($(ev.target).closest('.js-no-tr-click').length) return false;
		this.clickEdit(obj, ev);
	}

	private clickEdit(obj, ev) {
		this.detail.loadDetail(obj.id).done((response)=>{	});
	}

	private clickDelete(obj, ev) {

		if (! confirm('Вы точно хотите удалить заказ со всей спецификацией?')) return;

		var parameters = {
			jx: 'detail',
			method: 'delete',
			id: obj.id
		};

		return $.post('', parameters, (response)=> {
			var data = Utils.getJSONbyText(response); // TODO: JSON-RPC 2.0
			if (data) {
				if (data.success) {
					this.updateList();
				}
			} else {
				Notifications.show('Error: ajax response empty!', Notifications.Types.ntError);
			}
		}).fail((data)=>{
			Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
		});
	}

	private clickAdd(obj, ev) {
		this.detail.createOrder();
		this.forms_msg({ 'detail': { 'show': true } });
	}
}

export = EttonList;