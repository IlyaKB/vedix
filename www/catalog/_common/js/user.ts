import _ = require('underscore');
import ko = require('knockout');
import Utils = require('./utils');
import Notifications = require('./notifications');

class User {
  
  // For global access
  public static id: KnockoutObservable<number>;
  public static login: KnockoutObservable<string>;
  public static fullname: KnockoutObservable<string>;
  public static email: KnockoutObservable<string>;
  public static phone: KnockoutObservable<string>;
  
  // for HTML-binding
  private id: KnockoutObservable<number>;
  private login: KnockoutObservable<string>;
  private fullname: KnockoutObservable<string>;
  private email: KnockoutObservable<string>;
  private phone: KnockoutObservable<string>;

  private psw: KnockoutObservable<string>;
  private isValidLoginPsw: KnockoutObservable<boolean>;

  constructor(private config: any) {

    User.id = ko.observable( config.user.id || 0);
    User.login = ko.observable( config.user.login || '' );
    User.fullname = ko.observable( config.user.fullname || '' );
    User.email = ko.observable( config.user.email || '' );
    User.phone = ko.observable( config.user.phone || '' );

    this.id = User.id;
    this.login = User.login;
    this.fullname = User.fullname;
    this.email = User.email;
    this.phone = User.phone;

    this.psw = ko.observable('');
    this.isValidLoginPsw = ko.computed(()=>{
      return (this.login().length >= 3) && (this.psw().length >= 1);
    }, this);
  }

  public auth() {

    if ( (! this.login()) || (! this.psw()) ) {
      return false;
    }

    var parameters = {
      jx: 'authenticate',
      user_login: this.login(),
      user_password: this.psw()
    };

    $.post('/', parameters).then(function(response, status) {
        var data = Utils.getJSONbyText(response);
        if (data.success) {
          location.href = location.href.replace(/#/, '');
        } else {
          //
        }
      }, function(response) {
        //
      });
  }
}

export = User;