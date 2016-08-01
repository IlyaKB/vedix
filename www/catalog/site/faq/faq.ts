import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import KnockoutExt = require('./../../_common/js/knockout_ext');
import Utils = require('./../../_common/js/utils');
import Notifications = require('./../../_common/js/notifications');
import Analytics = require('./../../_common/js/analytics');
import HashStateControls = require('./../../_common/js/hash-state-controls');

import SitePage = require('./../site');

/**
 * FAQ page
 */
class FaqPage extends SitePage {
  
  private categories: KnockoutObservableArray<any>;
  private currentCategory: KnockoutObservable<number>;
  //private autoExpanding: boolean;
  private question: any;

  constructor(config: any) {
	  
    super(config);

    KnockoutExt.initSourceData();
    KnockoutExt.initToggle();
    
    _.bindAll(this, 'questionLike', 'questionDislike', 'categoryClick');
    
    this.categories = ko.observableArray([]);
    this.currentCategory = ko.observable(0);
    HashStateControls.addControl('category', this.currentCategory);
    
    this.currentCategory.subscribe((id)=>{
      Analytics.trackEvent('faq', 'category', 'view');
    });
    
    this.question = {
      category: ko.observable(''),
      text: ko.observable(''),
      onSubmit: (obj, ev)=>{
        var category = this.question.category();
        var data = {
          jx: 'addquestion',
          category_id: category ? category.id : null,
          text: this.question.text()
        };
        Analytics.trackEvent('faq', 'question', 'send');
        $.post('', data, (response)=>{
          var data = Utils.getJSONbyText(response);
        }).fail((data)=>{
          Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
        });
      }
    };

	$(document).ready(()=>{
      ko.applyBindings(this);
      HashStateControls.hashChange();
    });
  }
	
  private categoryClick(obj, ev) {
    var currentId = this.currentCategory();
    this.currentCategory( currentId == obj.id ? null : obj.id );
  }

  private questionLike(obj, ev) {
    Analytics.trackEvent('faq', 'question', 'like');
    this.vote(obj, ev, { id: obj.id, type: 'like'});
  }

  private questionDislike(obj, ev) {
    Analytics.trackEvent('faq', 'question', 'dislike');
    this.vote(obj, ev, { id: obj.id, type: 'dislike'});
  }

  private vote(obj, ev, data) {
    data.jx = 'vote';
    $.post('', data, (response)=>{
      var data = Utils.getJSONbyText(response);
      obj.counter_like(data.counter_like);
      obj.counter_dislike(data.counter_dislike);
    }).fail((data)=>{
      Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
    });
  }
}

export = FaqPage;