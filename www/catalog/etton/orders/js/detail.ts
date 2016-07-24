/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>

import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import Utils = require('./../../../_common/js/utils');
import Notifications = require('./../../../_common/js/notifications');

import EttonSpec = require('./spec');

/**
 * Order detail
 */
class EttonDetail {

	private forms_msg: any; // *KnockoutObservable<any>;
	private loading: KnockoutObservable<boolean>;

	private id: KnockoutObservable<number>;
	private number: KnockoutObservable<string>;
	private createdate: KnockoutObservable<string>;
	private customer: KnockoutObservable<string>;
	private isChange: KnockoutObservable<boolean>; // отслеживание изменения данных в заголовке заказа

	public spec: EttonSpec;

	constructor(config: any, forms_msg: KnockoutObservable<any>) {

		this.forms_msg = forms_msg;
		this.loading = ko.observable(false);

		this.id = ko.observable(null);
		this.number = ko.observable('');
		this.createdate = ko.observable('');
		this.customer = ko.observable('');

		this.isChange = ko.observable(false);
		this.number.subscribe(this.changeData, this); // отслеживаем изменения
		this.customer.subscribe(this.changeData, this); // отслеживаем изменения

		this.spec = new EttonSpec(config, forms_msg);

		_.bindAll(this, 'cancelClick', 'saveClick');

		$(document).ready(()=> {
			//
		});
	}

	public loadDetail(id) {

		var parameters = {
			jx: 'detail',
			method: 'get',
			id: id
		};

		var self = this;

		this.spec.clear();

		this.loading(true);
		return $.post('', parameters, (response)=> {
			this.loading(false);
			var data = Utils.getJSONbyText(response); // TODO: JSON-RPC 2.0
			if (data) {
				if (data.success) {
					this.id(data.order.id);
					this.number(''+data.order.number);
					this.createdate(''+data.order.createdate);
					this.customer(''+data.order.customer);
					this.spec.setSpec(data.order.spec);//items( _.map(data.spec, (row, index)=>{ return _.extend(row, { index: index+1 }); }) );

					this.isChange(false);

					this.forms_msg({ 'detail': {'show': true } });

				} else if (data.error) {
					// error
				}
			} else {
				Notifications.show('Error: ajax data empty!', Notifications.Types.ntError);
			}
		}).fail((data)=>{
			this.loading(false);
			Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
		});
	}

	public createOrder() {
		this.id(null);
		this.number('');
		this.createdate('Сегодня');
		this.customer('');
		this.spec.clear();

		this.isChange(false);
	}

	private cancelClick(obj, ev) {
		this.closeForm();
	}

	public closeForm() {
		if (((! this.isChange()) && (! this.spec.isChange())) || confirm('Вы точно хотите закрыть окно заказа без сохранения изменений?')) {
			this.forms_msg({'list': {'show': true}});
		}
	}

	private changeData() {
		this.isChange(true);
	}

	private saveClick(obj, ev): any {

		var isAdd = ! this.id();

		// Проверки
		if ((! this.number().length) || (this.number().length > 20)) {
			// TODO: Автоматизировать: выделить поле красным бордюром, выделить текст, снизу мелким шрифтом отобразить краткое сообщение об ошибке
			return alert('Номер должен быть не пустым и не больше 20 символов!');
		}
		if ((! this.customer().length) || (this.customer().length > 128)) {
			// TODO: Автоматизировать: выделить поле красным бордюром, выделить текст, снизу мелким шрифтом отобразить краткое сообщение об ошибке
			return alert('Название заказчика должен быть не пустым и не больше 128 символов!');
		}

		var parameters = {
			jx: 'detail',
			method: 'save',
			id: this.id(),
			number: this.number(),
			customer: this.customer(),
			spec: _.map(this.spec.items(), (item: any) => {
				return {
					id: item.id,
					position_id: item.position_id,
					subtype_id: item.subtype_id,
					quantity: item.quantity
				};
			})
		};

		return $.post('', parameters, (response)=> {
			var data = Utils.getJSONbyText(response); // TODO: JSON-RPC 2.0
			if (data) {
				if (data.success) {
					this.isChange(false);
					if (isAdd) {
						this.id(data.id);
						this.forms_msg({ 'list': { 'update': true } }); // Обновляем список заказов
					} else {
						this.forms_msg({ 'list': { 'update': true, 'show': true} }); // Обновляем список заказов и переходим на форму списка заказов
					}
				} else {
					// TODO: Автоматизировать: выделить поля с ошибками в данных красным бордюром, выделить текст в полях(?), снизу под каждым полем мелким шрифтом отобразить краткое сообщение об ошибке
				}
			} else {
				Notifications.show('Error: ajax response empty!', Notifications.Types.ntError);
			}
		}).fail((data)=>{
			Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
		});
	}
}

export = EttonDetail;