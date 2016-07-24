/// <reference path="../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../core/libs/_def/knockout.d.ts"/>

import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');

class HashStateControls {
  
  private static controls: any[] = [];
  private static isValueChanged: boolean = false;
  private static isHashChanged: boolean = false;

	private static __constructor = (()=>{
	  $(document).ready(()=>{
	    $(window).bind('hashchange', HashStateControls.hashChange);
	  });
	})();
	
	public static hashChange() {
	  if (! HashStateControls.isValueChanged) {
	    
	    if (HashStateControls.isHashChanged) return;
	    
	    HashStateControls.isHashChanged = true;
	  
      var values: Object = HashStateControls.parseHash();

      _.each(HashStateControls.controls, (control)=>{
        if (values.hasOwnProperty(control.code)) {
          control.value(values[control.code]);
        } else {
          control.value(control.defValue);
        }
      });

      //HashStateControls.isHashChanged = false;
      _.delay(()=> {
        HashStateControls.isHashChanged = false;
      },0);
	  }
	}

  private static parseHash() {
    var _hash: string = window.location.hash || '';
    while (_hash.length > 0 && (_hash[0] == '!' || _hash[0] == '#')) {
      _hash = _hash.slice(1);
    }
    var hash = decodeURI(_hash);

    var hashA: string[] = hash.split('&');
    var values: Object = {};
    _.each(hashA, (part: string)=>{
      if (! part) return true;
      var partA: string[] = part.split('=');
      values[partA[0]] = (partA.length == 2 ? partA[1] : undefined);
    });
    return values;
  }
	
	public static addControl(code: string, value: any, defValue?: any) {

    var values: Object = HashStateControls.parseHash();
    if (values.hasOwnProperty(code)) {
      value(values[code]);
    } else {
      value(defValue);
    }

	  var control = {
	    code: code,
	    value: value,
	    defValue: defValue
	  };
	  HashStateControls.controls.push(control);
	  value.subscribe(this._valueChanged);
	}
	
	private static _valueChanged(value: any) {
    HashStateControls.isValueChanged = true;
    var values: Object = {};
    _.each(HashStateControls.controls, (control)=>{
      /*if ( (control.value()) && (control.value() != '0') ) {
        if ( (control.defValue) && (_.isEqual(control.defValue, control.value())) ) { // TODO: нужно?
          return true;
        }
        values[control.code] = control.value();
      }*/
      if (control.value() == control.defValue) return true;
      values[control.code] = control.value();
    });
    if (_.isEmpty(values)) {
      if (window.location.hash) {
        history.pushState('', document.title, window.location.pathname+window.location.search);
      }
    } else {
      window.location.hash = '!' + $.param(values);
    }

    //HashStateControls.isValueChanged = false;
    _.delay(()=> {
      HashStateControls.isValueChanged = false;
    },0)
	}
}

export = HashStateControls;