/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
define(["require", "exports", 'jquery', 'knockout', './../../../_common/js/hash-state-controls'], function (require, exports, $, ko, HashStateControls) {
    //import KnockoutExt = require('./../../../_common/js/knockout_ext');
    /**
     * Sorting
     */
    var MagSorting = (function () {
        function MagSorting(config) {
            this.uid = 'mag-sorting-popup';
            this.elPopup = $('#mag_sorting_popup');
            //this.urlName = ko.observable(null); // %FIELD%-%DIR%
            /*this.urlName = ko.computed(()=>{
                if (! this.field()) return null;
                return this.field() + '-' + this.dir();
            }, this);*/
            this.field = ko.observable(null);
            this.dir = ko.observable(null);
            this.fieldName = ko.observable('не задана');
            this.urlName = ko.computed({
                read: function () {
                    if (!this.field())
                        return null;
                    return this.field() + '-' + this.dir();
                },
                write: function (value) {
                    if (!value) {
                        this.field(null);
                        this.dir(null);
                    }
                    else {
                        var field = value.replace(/\-.*/, '');
                        var dir = value.replace(/.*\-/, '');
                        if (field) {
                            this.field(field);
                            this.setFieldName(field);
                            if (dir) {
                                this.dir(dir);
                            }
                            else {
                                this.dir('asc');
                            }
                        }
                    }
                },
                owner: this
            });
            /*this.field = ko.computed(()=>{
                if (this.urlName()) {
                    return this.urlName().replace(/\-.*           /, '');
                } else {
                    return null;
                }
            }, this);
    
            this.dir = ko.computed(()=>{
                if (this.urlName()) {
                    return this.urlName().replace(/.*           \-/, '');
                } else {
                    return null;
                }
            }, this);*/
            HashStateControls.addControl('sort', this.urlName, null);
            this.bindHandlers(config);
        }
        MagSorting.prototype.bindHandlers = function (config) {
            //
        };
        MagSorting.prototype.show = function (_obj, _ev) {
            var _this = this;
            if (this.elPopup.css('display') != 'block') {
                var top = $('#mag_sorting_caller').get(0).offsetTop + $('#mag_sorting_caller').height() + 2;
                var left = $('#mag_sorting_caller').get(0).offsetLeft + $('#mag_sorting_caller').width() - this.elPopup.width() - 5;
                this.elPopup.css('top', top);
                this.elPopup.css('left', left);
                this.elPopup.show();
                var firstClick = true;
                $(document).bind('click.' + this.uid, function (e) {
                    if (!firstClick && $(e.target).closest(_this.elPopup).length == 0) {
                        _this.close();
                    }
                    firstClick = false;
                });
            }
        };
        MagSorting.prototype.close = function () {
            this.elPopup.hide();
            $(document).unbind('click.' + this.uid);
        };
        MagSorting.prototype.apply = function (obj, ev) {
            var el = $(ev.target).closest('div');
            var field = el.data('sort-field');
            if (field == this.field()) {
                if (this.dir() == 'asc') {
                    this.dir('desc');
                }
                else {
                    this.dir('asc');
                }
            }
            else {
                this.field(field);
                this.dir('asc');
                this.setFieldName(field);
            }
            this.close();
        };
        MagSorting.prototype.setFieldName = function (field) {
            var _this = this;
            this.elPopup.find('div').each(function (i, e) {
                var el = $(e);
                if (el.data('sort-field') == field) {
                    var fieldName = el.text().trim();
                    _this.fieldName(fieldName.toLocaleLowerCase());
                    return false;
                }
                return true;
            });
        };
        return MagSorting;
    })();
    return MagSorting;
});
