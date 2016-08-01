import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import KnockoutExt = require('./../../_common/js/knockout_ext');
import Utils = require('./../../_common/js/utils');
import Notifications = require('./../../_common/js/notifications');
import Analytics = require('./../../_common/js/analytics');
//import HashStateControls = require('./../../_common/js/hash-state-controls');

import SitePage = require('./../site');
import Voting = require('./../_components/voting/voting');
import Comments = require('./../../_components/comments/comments');

/**
 * Blog page
 */
class BlogPage extends SitePage {

	private posts: KnockoutObservableArray<any>;

	private voting: Voting;
	private hits: KnockoutObservable<number>;
	private hitsPrint: KnockoutComputed<string>;
	private comments: Comments;

	constructor(config: any) {

		super(config);

		KnockoutExt.initSourceData();

		if (config.is_list) {
			this.posts = ko.observableArray([]);
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
			//HashStateControls.hashChange();
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

		// TODO: решить на уровне верстки и CSS
		$(window).resize(()=>{
			var elLeft = $('div.left-layer');
			var elLeftBlocks = elLeft.find('div.block');
			var h1 = 0;
			elLeftBlocks.each((i, e)=>{
				h1 += $(e).height();
			});
			var elMiddle = $('div.middle-layer');
			var h2 = Math.max(elMiddle.height() - 5, parseInt(elMiddle.css('min-height')) - 7);
			elLeft.height( Math.max(h1, h2));
		}).resize();
	}

	private showPostsInCategory(_obj, ev) {

		function showOrHide(elTR, doShow) {
			while (elTR) {
				elTR = elTR.next('tr');
				if (! elTR.hasClass('post')) break;
				if (doShow) {
					elTR.show();
				} else {
					elTR.hide();
				}
			}
		}

		var elTR = $(ev.target).closest('tr');

		if (! elTR.data('expand')) {
			var elTable = elTR.closest('table');
			elTable.find('tr.post').hide();
			elTable.find('tr.category').data('expand', '').find('img').attr('src', '/data/site/blog/ft_plus.gif');
			elTR.data('expand', 1);
			elTR.find('img').attr('src', '/data/site/blog/ft_minus.gif');
			showOrHide(elTR, true);
		} else {
			elTR.data('expand', '').find('img').attr('src', '/data/site/blog/ft_plus.gif');
			showOrHide(elTR, false);
		}

		return true;
	}

	private categoryMouseOver(_obj, ev) {
		var elImg = $(ev.target);
		if (elImg.closest('tr').data('expand')) {
			elImg.attr('src', '/data/site/blog/ft_minus_over.gif');
		} else {
			elImg.attr('src', '/data/site/blog/ft_plus_over.gif');
		}
	}

	private categoryMouseOut(_obj, ev) {
		var elImg = $(ev.target);
		if (elImg.closest('tr').data('expand')) {
			elImg.attr('src', '/data/site/blog/ft_minus.gif');
		} else {
			elImg.attr('src', '/data/site/blog/ft_plus.gif');
		}
	}
}

export = BlogPage;