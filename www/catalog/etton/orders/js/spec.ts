/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>

import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import Utils = require('./../../../_common/js/utils');
import Notifications = require('./../../../_common/js/notifications');

//import SelectControl = require('./../../../_components/selectcontrol/select-control');

/**
 * Specifications
 */
class EttonSpec {

	private forms_msg: any; // *KnockoutObservable<any>;
	public items: KnockoutObservableArray<any>;
	public isChange: KnockoutObservable<boolean>;

	constructor(config: any, forms_msg: KnockoutObservable<any>) {

		this.forms_msg = forms_msg;
		this.items = ko.observableArray([]);
		this.isChange = ko.observable(false);

		/*TODO: SelectControl для выбора подтипа
		this.subtype = new SelectControl({
			selInputName: 'subtype_name',
			selPopup: '#subtypes_popup',
			loadListFunc: this.loadSubtypesList,
			itemSelectedFunc: this.subtypeSelected
		});*/

		_.bindAll(this, 'clickDelete');

		$(document).ready(()=> {
			//
		});
	}

	public setSpec(_spec) {
		var index = 1;
		this.items(_.map(_spec, (spec: any)=>{
			var item = {
				index: ko.observable(index++),
				id: spec.id,
				position_id: spec.position_id,
				name: spec.name,
				subtype_id: spec.subtype_id,
				subtype_name: spec.subtype_name,
				quantity: ko.observable(spec.quantity)
			};
			item.quantity.subscribe(()=>{ this.isChange(true); }, this);
			return item;
		}));
		this.isChange(false);
	}

	public add(positionRow: any) {

		var isDetected = false;

		_.each(this.items(), (item: any)=>{
			if ((item.position_id == positionRow.id) && (
					(positionRow.subtypes_count == 0) || (positionRow.subtype_id == item.subtype_id))) {
				alert('Этот товар уже добавлен в заказ!');
				isDetected = true;
				return true;
			}
		});

		if (isDetected) return false;

		var id = Math.min(0, _.min(this.items(), (item: any)=>{ return (item.id ? item.id : 0) }).id );

		//this.items.unshift({
		this.items.push({
			id: (id? id - 1 : -1),
			index: ko.observable(),
			position_id: positionRow.id,
			name: positionRow.name,
			subtype_id: positionRow.subtype_id,
			subtype_name: positionRow.subtype_name,
			quantity: ko.observable(1)
		});

		this.reIndex();
		this.isChange(true);

		return true;
	}

	private reIndex() {
		var index = 1;
		_.each(this.items(), (item: any)=>{
			item.index(index++);
		});
	}

	private clickAdd(obj, ev) {
		this.forms_msg({'positions': { 'show': true }});
	}

	private clickDelete(obj, ev) {
		this.items.remove((item: any)=>{
			return (item.id == obj.id);
		});

		this.reIndex();
		this.isChange(true);
	}

	public clear() {
		this.items([]);
	}

	private quantityDeincClick(obj: any, ev) {
		obj.quantity( Math.max(1, +obj.quantity() - 1) );
	}

	private quantityIncClick(obj: any, ev) {
		obj.quantity( +obj.quantity() + 1 );
	}
}

export = EttonSpec;