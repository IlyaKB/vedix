/// <reference path="../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../core/libs/_def/knockout.d.ts"/>
/// <reference path="../../../core/libs/_def/moment.d.ts"/>

import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import moment = require('moment');
import momentru = require('moment-ru');
import KnockoutExt = require('./../../_common/js/knockout_ext');
import Utils = require('./../../_common/js/utils');
import User = require('./../../_common/js/user');
import Notifications = require('./../../_common/js/notifications');
import Analytics = require('./../../_common/js/analytics');

/**
 * Comment entity class
 */
class Comments {
  
  private items: KnockoutObservableArray<any>;
  private itemsCallback: ()=>void;
  
  private authorName: KnockoutObservable<string>;
  private email: KnockoutObservable<string>;
  private text: KnockoutObservable<string>;
  
  private errorText: KnockoutObservable<string>;
  
  private authorNameError: KnockoutObservable<boolean>;
  private emailError: KnockoutObservable<boolean>;
  private textError: KnockoutObservable<boolean>;

  constructor(private config: any) {

    momentru;

    moment.locale('ru');

    _.bindAll(this, 'onSubmit', 'votingMouseOver', 'votingMouseOut', 'vote');

    this.items = ko.observableArray([]);
    // Преобразование даты и времени
    this.itemsCallback = ()=>{
      _.each(this.items(), (item)=>{
        var dt = moment(item.creation_date());
        item.creation_date(dt.fromNow());
        _.each(item.replies(), (reply: any)=>{
          var dt = moment(reply.creation_date());
          reply.creation_date(dt.fromNow());
        });
      });
    };
    
    KnockoutExt.initSourceData();

    this.authorName = ko.observable( User.id() ? User.fullname() : '' );
    this.email = ko.observable('');
    this.text = ko.observable('');
    
    this.errorText = ko.observable('');
    
    this.authorNameError = ko.observable(false);
    this.emailError = ko.observable(false);
    this.textError = ko.observable(false);
  }
  
  private onSubmit(obj, ev) {
    
    this.errorText('');
    this.authorNameError(false);
    this.emailError(false);
    this.textError(false);
    
    var fields = [];
    
    if (! this.authorName()) {
      this.authorNameError(true);
      fields.push('Имя');
    }
    if ( (! User.id()) && (! this.email()) ) {
      this.emailError(true);
      fields.push('Email');
    }
    if (! this.text()) {
      this.textError(true);
      fields.push('Текст комментария');
    }
    
    if (fields.length) {
      this.errorText('Заполните поля ('+fields.join(', ')+')');
      return false;
    }

    Analytics.trackEvent(this.config.entity.type, 'comments', 'send');
    
    $.post('', {
      jx: 'comments',
      action: 'add',
      //entity_type: this.config.entity.type,
      entity_id: this.config.entity.id,
      author_name: this.authorName(),
      email: this.email(),
      text: this.text()
    }, (response)=>{
      var data = Utils.getJSONbyText(response);
      if (data) {
        this.text('');
        _.each(this.items(), (item)=>{
          var index = item.index();
          item.index(++index);
        });
        var _comment = data.comment;
        var dt = moment(_comment.creation_date);
        var comment = {
          author_id: _comment.author_id,
          author_name: _comment.author_name,
          photo: _comment.photo,
          creation_date: dt.fromNow(),
          text: _comment.text,
          counter_like: _comment.counter_like,
          counter_dislike: _comment.counter_dislike,
          replies_visible: ko.observable(false),
          replies: ko.observableArray([]),
          index: ko.observable(1)
        };
        this.items.splice(0, 0, comment);
      }
    }, 'text');
  }
  
  private showReplyForm(obj, ev) {
    alert('TODO: showReplyForm(...)');
  }
  
  private votingMouseOver(obj, ev) {
    
    var el = $(ev.target).closest('.voting');
    //if (! el.hasClass('voting')) return true;
    
    el.addClass('hover');
    var src = el.find('img').attr('src');
    el.find('img').attr('src', src.replace(/\.png/, '_hover.png'));
  }
  
  private votingMouseOut(obj, ev) {
    
    var el = $(ev.target).closest('.voting');
    //if (! el.hasClass('voting')) return true;
    
    el.removeClass('hover');
    var src = el.find('img').attr('src');
    el.find('img').attr('src', src.replace(/_hover\.png/, '.png'));
  }

  private vote(obj, ev) {

    Analytics.trackEvent(this.config.entity.type, 'comments', 'vote');

    var el = $(ev.target).closest('.voting');
    if (! el.length) return console.error('Comments.vote(..): error!');

    var data = { jx: 'comments', action: 'vote', id: obj.id, type: el.hasClass('js-like') ? 'like' : 'dislike' };

    $.post('', data, (response)=>{
      var data = Utils.getJSONbyText(response);
      obj.counter_like(data.counter_like);
      obj.counter_dislike(data.counter_dislike);
    }).fail((data)=>{
      Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
    });
  }
}
export = Comments;