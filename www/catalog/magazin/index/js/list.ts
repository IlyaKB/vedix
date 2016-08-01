/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>

import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
//import Utils = require('./../../../_common/js/utils');
//import Notifications = require('./../../../_common/js/notifications');
//import Analytics = require('./../../../_common/js/analytics');
import HashStateControls = require('./../../../_common/js/hash-state-controls');
import KnockoutExt = require('./../../../_common/js/knockout_ext');

import MagFilters = require('./filters');
import MagSorting = require('./sorting');
import Pagination = require('./../../../_components/pagination/pagination');

/**
 * List
 */
class MagList {

	private positions: KnockoutObservableArray<any>;
	private filters: MagFilters;
	private sorting: MagSorting;
	private pagination: Pagination;

	constructor(config: any) {

		KnockoutExt.initSourceData();

		this.positions = ko.observableArray([]);
		this.filters = new MagFilters(config);
		this.sorting = new MagSorting(config);
		this.pagination = new Pagination('magazin/');

		$(document).ready(()=> {
			//
		});

		this.bindHandlers(config);
	}

	private bindHandlers(config: any) {
		//
	}
}

export = MagList;