/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>

import _ = require('underscore');
import ko = require('knockout');
import Utils = require('./../../../_common/js/utils');
import Notifications = require('./../../../_common/js/notifications');

class Voting {
  
  private votes: KnockoutObservableArray<Object>;
  private votesCount: KnockoutObservable<number>;
  private votesCountPrint: KnockoutComputed<string>;
  private hoverVote: KnockoutObservable<number>;
  
  constructor(private config: any) {
    
    _.bindAll(this, 'imgMouseOver', 'imgMouseOut', 'clickVote');
    
    var votes: Object[] = this.parseVotes(config.detail ? config.detail.votes : 0);
    this.votes = ko.observableArray(votes);
    this.votesCount = ko.observable(config.detail ? config.detail.votesCount : 0);
    this.votesCountPrint = ko.computed(()=>{
        return this.votesCount() < 10 ? 'менее 10' : String(this.votesCount());
    });
    this.hoverVote = ko.observable(-1);
  }
  
  private imgMouseOver(obj, ev) {
    this.hoverVote(obj.index);
  }
  
  private imgMouseOut(obj, ev) {
    this.hoverVote(-1);
  }
  
  private clickVote(obj, ev) {
    $.post('', {
      jx: 'voting',
      entity_type: this.config.entity.type,
      entity_id: this.config.entity.id,
      mark: obj.index + 1
    }, (response)=>{
      var data = Utils.getJSONbyText(response);
      if (data) {
        this.votesCount(data.votes_count);
        this.votes(this.parseVotes(data.votes));
      }
    }).fail((data)=>{
      Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
    });
  }
  
  private parseVotes(votes) {
    var i = 0;
    return _.map(votes, (vote)=>{
      return { mark: vote, index: i++};
    });
  }
}
export = Voting;