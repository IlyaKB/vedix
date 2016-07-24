/// <reference path="../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../core/libs/_def/knockout.d.ts"/>

import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');

//export enum NotificationsType { ntError, ntSuccess, ntWarning };
//export enum NotificationsFlag { ntAnimate, ntFast };

class Notifications {
  
  public static Types = { ntError: 1, ntSuccess: 2, ntWarning: 3 };
  public static Flags = { ntAnimate: 1, ntFast: 2 };
  
  public static wait: number = 3000;

  private static rootEl: JQuery;
  private static errorEl: JQuery;
  private static warningEl: JQuery;
  private static successEl: JQuery;
  
  private static tmr: any = null;
  private static top: number = 0;
  private static text: KnockoutObservable<string>;
  
  private static __constructor = (()=>{
    
    Notifications.text = ko.observable('');

    (<any>ko.bindingHandlers).notifications = {
		  init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
			  Notifications.rootEl = $(element);
			  Notifications.top = Notifications.rootEl.offset().top;
			  Notifications.errorEl = Notifications.rootEl.find('.error');
			  Notifications.warningEl = Notifications.rootEl.find('.warning');
			  Notifications.successEl = Notifications.rootEl.find('.success');
			  Notifications.rootEl.on('click', ()=>{
			    Notifications.hide();
			  });
			  $(window).scroll(()=>{
			    var _scrollTop = $(document).scrollTop();
          if (_scrollTop < Notifications.top) {
            $('.notification').css('position', 'relative');
          } else {
            $('.notification').css('position', 'fixed');
          }
			  }).scroll();
		  }
    };
  })();
  
  public static show(text: string, type = Notifications.Types.ntSuccess) {
    if (Notifications.tmr) {
      clearTimeout(Notifications.tmr);
      Notifications.hide(Notifications.Flags.ntFast);
    }
    Notifications.tmr = setTimeout(Notifications.hide, Notifications.wait);
    var el: JQuery = null;
    switch (type) {
      case Notifications.Types.ntError: {
        el = Notifications.errorEl;
        break;
      }
      case Notifications.Types.ntSuccess: {
        el = Notifications.successEl;
        break;
      }
      case Notifications.Types.ntWarning: {
        el = Notifications.warningEl;
        break;
      }
    }
    el.html(text);
    el.height(0).show().animate({ height: '+=30px' }, 250);
  }
  
  public static hide(flag = Notifications.Flags.ntAnimate) {
    
    var el = Notifications.rootEl.find('.notification:visible');
    if (! el.length) return;
    
    if (flag == Notifications.Flags.ntFast) {
      el.hide();
    } else {
      Notifications.rootEl.find('.notification:visible').animate({ height: '-=30px' }, 250, 'swing', ()=>{
        el.hide();
      });
    }
  }
}

export = Notifications;