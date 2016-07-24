/// <reference path="../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../core/libs/_def/knockout.d.ts"/>
define(["require", "exports", 'underscore', 'jquery'], function (require, exports, _, $) {
    var HashStateControls = (function () {
        function HashStateControls() {
        }
        HashStateControls.hashChange = function () {
            if (!HashStateControls.isValueChanged) {
                if (HashStateControls.isHashChanged)
                    return;
                HashStateControls.isHashChanged = true;
                var values = HashStateControls.parseHash();
                _.each(HashStateControls.controls, function (control) {
                    if (values.hasOwnProperty(control.code)) {
                        control.value(values[control.code]);
                    }
                    else {
                        control.value(control.defValue);
                    }
                });
                //HashStateControls.isHashChanged = false;
                _.delay(function () {
                    HashStateControls.isHashChanged = false;
                }, 0);
            }
        };
        HashStateControls.parseHash = function () {
            var _hash = window.location.hash || '';
            while (_hash.length > 0 && (_hash[0] == '!' || _hash[0] == '#')) {
                _hash = _hash.slice(1);
            }
            var hash = decodeURI(_hash);
            var hashA = hash.split('&');
            var values = {};
            _.each(hashA, function (part) {
                if (!part)
                    return true;
                var partA = part.split('=');
                values[partA[0]] = (partA.length == 2 ? partA[1] : undefined);
            });
            return values;
        };
        HashStateControls.addControl = function (code, value, defValue) {
            var values = HashStateControls.parseHash();
            if (values.hasOwnProperty(code)) {
                value(values[code]);
            }
            else {
                value(defValue);
            }
            var control = {
                code: code,
                value: value,
                defValue: defValue
            };
            HashStateControls.controls.push(control);
            value.subscribe(this._valueChanged);
        };
        HashStateControls._valueChanged = function (value) {
            HashStateControls.isValueChanged = true;
            var values = {};
            _.each(HashStateControls.controls, function (control) {
                /*if ( (control.value()) && (control.value() != '0') ) {
                  if ( (control.defValue) && (_.isEqual(control.defValue, control.value())) ) { // TODO: нужно?
                    return true;
                  }
                  values[control.code] = control.value();
                }*/
                if (control.value() == control.defValue)
                    return true;
                values[control.code] = control.value();
            });
            if (_.isEmpty(values)) {
                if (window.location.hash) {
                    history.pushState('', document.title, window.location.pathname + window.location.search);
                }
            }
            else {
                window.location.hash = '!' + $.param(values);
            }
            //HashStateControls.isValueChanged = false;
            _.delay(function () {
                HashStateControls.isValueChanged = false;
            }, 0);
        };
        HashStateControls.controls = [];
        HashStateControls.isValueChanged = false;
        HashStateControls.isHashChanged = false;
        HashStateControls.__constructor = (function () {
            $(document).ready(function () {
                $(window).bind('hashchange', HashStateControls.hashChange);
            });
        })();
        return HashStateControls;
    })();
    return HashStateControls;
});
