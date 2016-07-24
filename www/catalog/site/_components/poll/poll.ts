/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>

import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import Utils = require('../../../_common/js/utils');
import Notifications = require('../../../_common/js/notifications');
import Analytics = require('../../../_common/js/analytics');

/**
 * Poll entity class
 */
class Poll {

  private elPoll: JQuery;
  private poll_id: number;
  private mode: KnockoutObservable<string>;
  private max_votes: number = 1;
  private items_checked: Array<number> = [];
  private voted: KnockoutObservable<boolean>;

  constructor(idSelector: string, private config: any) {

    this.elPoll = $(idSelector);
    this.poll_id = this.elPoll.data('id');
    this.max_votes = this.elPoll.data('max-votes');
    this.voted = ko.observable( this.elPoll.data('voted') ? true : false);
    this.mode = ko.observable( this.voted() ? 'results' : 'voting');

    this.elPoll.find('.items input').each((i, e)=>{
      var input = $(e);
      var elTR = input.closest('tr');
      if ((<any>input.get(0)).checked) {
        elTR.addClass('selected');
      }
    });
  }

  private itemClick(_obj, ev) {
    var elTR = $(ev.target).closest('tr');
    if (elTR.hasClass('selected')) {
      elTR.removeClass('selected');
      (<any>elTR.find('input').get(0)).checked = false;
    } else {
      elTR.addClass('selected');
      (<any>elTR.find('input').get(0)).checked = true;
    }
  }

  private voteClick(_obj, _ev) {

    Analytics.trackEvent(this.config.entity.type, 'poll', 'vote');

    this.items_checked = this.getCheckedItems();
    if (! this.items_checked.length) {
      Notifications.show('Выберите хотя бы один вариант!', Notifications.Types.ntWarning);
      return false;
    }

    $.post('', {
      jx: 'poll',
      poll_id: this.poll_id,
      items_checked: this.items_checked
    }, (response)=>{
      var data = Utils.getJSONbyText(response);
      if ((data) && (data.success) ) {
        this.voted(true);
        this.elPoll.find('div.results > div').each((i, e)=>{
          var elDiv = $(e);
          var item_id = elDiv.data('id');
          var item: any = _.findWhere(data.items, { id: item_id });
          var elText = elDiv.find('div.text > span.votes');
          elText.text('('+item.votes+')');
          var elLine = elDiv.find('div.line > div');
          var w = (item.votes_per ? item.votes_per + '%' : '1px')
          elLine.css('width', w);
        });
        this.mode('results');
      }
    }).fail((data)=>{
      Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
    });
  }

  private getCheckedItems() {
    var items_checked = [];
    this.elPoll.find('table.items tr').each((i, e)=>{
      var elTR = $(e);
      var input = elTR.find('input');
      if ((<any>input.get(0)).checked) {
        items_checked.push(parseInt(elTR.data('id')));
      }
    });
    return items_checked;
  }

  private votingClick(_obj, _ev) {
    Analytics.trackEventOnce(this.config.entity.type, 'poll', 'voting');
    this.mode('voting');
  }

  private resultsClick(_obj, _ev) {
    Analytics.trackEventOnce(this.config.entity.type, 'poll', 'results');
    this.mode('results');
  }
}
export = Poll;