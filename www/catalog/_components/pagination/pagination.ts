import ko = require('knockout');
import KnockoutExt = require('./../../_common/js/knockout_ext');

class Pagination {

	private previous_page: KnockoutObservable<string>;
	private next_page: KnockoutObservable<string>;
	private items: KnockoutObservableArray<any>;

	private previousPageUrl: KnockoutComputedFunctions<string>;
	private nextPageUrl: KnockoutComputedFunctions<string>;

	constructor(config: any) {//}, urlAggregate: KnockoutObservable<string>) {

		KnockoutExt.initSourceData();

		this.previous_page = ko.observable('');
		this.next_page = ko.observable('');
		this.items = ko.observableArray([]);

		this.previousPageUrl = ko.computed(()=>{
			var _pg = this.previous_page();
			return (/p=\d+/.test(_pg) ? _pg : '?p=1'); // TODO

			/*var _urlAggregate: any = urlAggregate();
			_urlAggregate.p = this.previous_page();*/

			/*return urlAggregate().replace(/([?&]p=)[\d]+/i, '\\1'+this.previous_page());*/
		}, this);

		this.nextPageUrl = ko.computed(()=> {
			return '?p=' + this.next_page();
		}, this);
	}

	public rebuild() {
		// TODO
	}
}

export = Pagination;