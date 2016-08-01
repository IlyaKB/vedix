import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import KnockoutExt = require('./../../_common/js/knockout_ext');
import Utils = require('./../../_common/js/utils');
import Notifications = require('./../../_common/js/notifications');
import Analytics = require('./../../_common/js/analytics');
import HashStateControls = require('./../../_common/js/hash-state-controls');

import SitePage = require('./../site');
import Voting = require('./../_components/voting/voting');
import Comments = require('./../../_components/comments/comments');

/**
 * News page
 */
class NewsPage extends SitePage {

	private news: KnockoutObservableArray<any>;

	private voting: Voting;
	private hits: KnockoutObservable<number>;
	private hitsPrint: KnockoutComputed<string>;
	private comments: Comments;

	constructor(config: any) {

		super(config);

		KnockoutExt.initSourceData();

		if (config.is_list) {
			this.news = ko.observableArray([]);
		} else if (config.is_record) {
			this.voting = new Voting(config);
			this.comments = new Comments(config);
			this.hits = ko.observable(config.detail ? config.detail.hits : 0);
			this.hitsPrint = ko.computed(()=> {
				return this.hits() < 10 ? 'менее 10' : String(this.hits());
			});
		}

		$(document).ready(()=>{
			ko.applyBindings(this);
			HashStateControls.hashChange();
		});

		if ( (config.is_record) && (! config.detail.viewed) ) {
			$.post('', {
				jx: 'visit'
			}, (response)=>{
				var data = Utils.getJSONbyText(response);
				this.hits(data.hits);
			}, 'text').fail((data)=>{
				Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
			});
		}
	}
}

export = NewsPage;