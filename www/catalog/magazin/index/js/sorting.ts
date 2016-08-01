/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>

import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
//import Utils = require('./../../../_common/js/utils');
//import Notifications = require('./../../../_common/js/notifications');
//import Analytics = require('./../../../_common/js/analytics');
import HashStateControls = require('./../../../_common/js/hash-state-controls');
//import KnockoutExt = require('./../../../_common/js/knockout_ext');

/**
 * Sorting
 */
class MagSorting {

	private uid = 'mag-sorting-popup';
	private urlName: KnockoutComputedFunctions<string>;//KnockoutObservable<string>;
	private field: KnockoutObservable<string>;//KnockoutComputedFunctions<string>;
	private dir: KnockoutObservable<string>;//KnockoutComputedFunctions<string>;
	private fieldName: KnockoutObservable<string>;
	private elPopup: JQuery;

	constructor(config: any) {

		this.elPopup = $('#mag_sorting_popup');

		//this.urlName = ko.observable(null); // %FIELD%-%DIR%
		/*this.urlName = ko.computed(()=>{
			if (! this.field()) return null;
			return this.field() + '-' + this.dir();
		}, this);*/

		this.field = ko.observable(null);
		this.dir = ko.observable(null);

		this.fieldName = ko.observable('не задана');

		this.urlName = ko.computed({
			read: function () {
				if (! this.field()) return null;
				return this.field() + '-' + this.dir();
			},
			write: function (value: string) {
				if (! value) {
					this.field(null);
					this.dir(null);
				} else {
					var field = value.replace(/\-.*/, '');
					var dir = value.replace(/.*\-/, '');
					if (field) {
						this.field(field);
						this.setFieldName(field);
						if (dir) {
							this.dir(dir);
						} else {
							this.dir('asc');
						}
					}
				}
			},
			owner: this
		});

		/*this.field = ko.computed(()=>{
			if (this.urlName()) {
				return this.urlName().replace(/\-.*           /, '');
			} else {
				return null;
			}
		}, this);

		this.dir = ko.computed(()=>{
			if (this.urlName()) {
				return this.urlName().replace(/.*           \-/, '');
			} else {
				return null;
			}
		}, this);*/

		HashStateControls.addControl('sort', this.urlName, null);

		this.bindHandlers(config);
	}

	private bindHandlers(config: any) {
		//
	}

	private show(_obj?, _ev?) {

		if (this.elPopup.css('display') != 'block') {
			var top = $('#mag_sorting_caller').get(0).offsetTop + $('#mag_sorting_caller').height() + 2;
			var left = $('#mag_sorting_caller').get(0).offsetLeft + $('#mag_sorting_caller').width() - this.elPopup.width() - 5;
			this.elPopup.css('top', top);
			this.elPopup.css('left', left);
			this.elPopup.show();
			var firstClick = true;
			$(document).bind('click.' + this.uid, (e)=>{
				if (!firstClick && $(e.target).closest(this.elPopup).length == 0) {
					this.close();
				}
				firstClick = false;
			});
		}
	}

	private close() {
		this.elPopup.hide();
		$(document).unbind('click.' + this.uid);
	}

	private apply(obj, ev) {
		var el = $(ev.target).closest('div');
		var field = el.data('sort-field');
		if (field == this.field()) {
			if (this.dir() == 'asc') {
				this.dir('desc');
			} else {
				this.dir('asc');
			}
		} else {
			this.field(field);
			this.dir('asc');
			this.setFieldName(field);
		}
		this.close();
	}

	private setFieldName(field) {
		this.elPopup.find('div').each((i, e)=>{
			var el = $(e);
			if (el.data('sort-field') == field) {
				var fieldName = el.text().trim();
				this.fieldName(fieldName.toLocaleLowerCase());
				return false;
			}
			return true;
		});
	}
}

export = MagSorting;