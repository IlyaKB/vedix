/// <reference path="../../../../core/libs/_def/jquery-ui-draggable.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery-ui-slider.d.ts"/>
define(["require", "exports", 'underscore', 'jquery', 'jquery-ui-draggable', 'jquery-ui-slider', 'knockout', './../../../_common/js/numbers', './../../../_common/js/hash-state-controls'], function (require, exports, _, $, jquery_ui_draggable, jquery_ui_slider, ko, Numbers, HashStateControls) {
    //import KnockoutExt = require('./../../../_common/js/knockout_ext');
    /**
     * Filters
     */
    var MagFilters = (function () {
        function MagFilters(config) {
            var _this = this;
            this.uid = 'mag-filters-popup';
            jquery_ui_draggable;
            jquery_ui_slider;
            _.bindAll(this, 'clear', 'apply', 'close', 'buildTags', 'removeTag');
            this.elPopup = $('#mag_filters_popup');
            var priceMin = config.filters.priceMinMin;
            var priceMax = config.filters.priceMaxMax;
            if ((!config.manufacturers) || (!_.isArray(config.manufacturers)))
                config.manufacturers = [];
            config.manufacturers.unshift({ url_name: null, name: '- все -' });
            this._filters = {
                keywords: { value: ko.observable(), defaultValue: null, tagText: 'Ключевые слова: %VALUE%' },
                pre_order: { value: ko.observable(), defaultValue: true, tagText: 'Скрыть с предзаказом' },
                not_available: { value: ko.observable(), defaultValue: false, tagText: 'Не в наличии' },
                manufacturer: {
                    value: ko.observable(null),
                    items: config.manufacturers,
                    defaultValue: null,
                    tagText: 'Изготовитель: %VALUE%'
                },
                with_photo: { value: ko.observable(), defaultValue: false, tagText: 'С фото' },
                with_specification: { value: ko.observable(), defaultValue: false, tagText: 'С описанием состава' },
                with_comments: { value: ko.observable(), defaultValue: false, tagText: 'С отзывами' },
                price_min: { value: ko.observable(priceMin), defaultValue: priceMin, manualBuildTag: true },
                price_max: { value: ko.observable(priceMax), defaultValue: priceMax, manualBuildTag: true }
            };
            this.tags = ko.observableArray([]);
            this.priceText = ko.computed(function () {
                var priceMin = Numbers.formatMoney(_this._filters.price_min.value());
                var priceMax = Numbers.formatMoney(_this._filters.price_max.value());
                return priceMin + '&ndash;' + priceMax;
            }, this);
            for (var p in this._filters) {
                HashStateControls.addControl(p, this._filters[p].value, this._filters[p].defaultValue);
                this._filters[p].value.subscribe(this.buildTags);
            }
            this.buildTags();
            this.bindHandlers(config);
        }
        MagFilters.prototype.bindHandlers = function (config) {
            var _this = this;
            $('#mag_filters_popup').draggable({ handle: '#mag_filters_popup_draggable' });
            var priceMinMin = config.filters.priceMinMin || 0;
            var priceMaxMax = config.filters.priceMaxMax || 10000;
            if (priceMinMin == priceMaxMax)
                priceMaxMax = 1000;
            $('#slider_range').slider({
                range: true,
                min: priceMinMin,
                max: priceMaxMax,
                step: Math.max(50, Math.ceil(2 * (priceMaxMax - priceMinMin) / 110) / 2),
                values: [this._filters.price_min.value(), this._filters.price_max.value()],
                slide: function (event, ui) {
                    var priceMin = ui.values[0];
                    var priceMax = ui.values[1];
                    _this._filters.price_min.value(priceMin);
                    _this._filters.price_max.value(priceMax);
                }
            });
        };
        MagFilters.prototype.buildTags = function () {
            var tags = [];
            // Цена: от XXX до XXX
            var prices = '';
            var priceMin = this._filters.price_min;
            var priceMax = this._filters.price_max;
            if (priceMin.value() != priceMin.defaultValue) {
                prices += 'от ' + Numbers.formatMoney(priceMin.value());
            }
            if (priceMax.value() != priceMax.defaultValue) {
                if (prices)
                    prices += ' ';
                prices += 'до ' + Numbers.formatMoney(priceMax.value());
            }
            if (prices) {
                tags.push({
                    url_name: 'prices',
                    text: 'Цена: ' + prices
                });
            }
            for (var p in this._filters) {
                var filter = this._filters[p];
                if (filter.manualBuildTag) {
                    continue;
                }
                if (filter.value() != filter.defaultValue) {
                    var text = filter.value();
                    if (filter.items) {
                        var item = _.findWhere(filter.items, { url_name: filter.value() });
                        text = item ? item.name : '???';
                    }
                    tags.push({
                        url_name: p,
                        text: filter.tagText.replace(/%VALUE%/, text) // TODO: не url_name, а text
                    });
                }
            }
            this.tags([]);
            ko.utils.arrayPushAll(this.tags, tags);
        };
        MagFilters.prototype.removeTag = function (obj, ev) {
            if (!obj.url_name)
                return;
            if (obj.url_name == 'prices') {
                this._filters.price_min.value(this._filters.price_min.defaultValue);
                this._filters.price_max.value(this._filters.price_max.defaultValue);
                $('#slider_range').slider({ values: [this._filters.price_min.value(), this._filters.price_max.value()] });
                return;
            }
            for (var p in this._filters) {
                var filter = this._filters[p];
                if (p == obj.url_name) {
                    filter.value(filter.defaultValue);
                }
            }
        };
        MagFilters.prototype.show = function (_obj, _ev) {
            var _this = this;
            if (this.elPopup.css('display') != 'block') {
                var top = $('#mag_filters_popup_caller').offset().top + $('#mag_filters_popup_caller').height() + 5;
                var left = $('#mag_filters_popup_caller').offset().left + $('#mag_filters_popup_caller').width() - this.elPopup.width() - 10;
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
        MagFilters.prototype.close = function () {
            this.elPopup.hide();
            $(document).unbind('click.' + this.uid);
        };
        MagFilters.prototype.clear = function () {
            _.each(this._filters, function (filter) {
                filter.value(filter.defaultValue);
            });
            $('#slider_range').slider({ values: [this._filters.price_min.value(), this._filters.price_max.value()] });
        };
        MagFilters.prototype.apply = function () {
            // TODO
            this.close();
        };
        return MagFilters;
    })();
    return MagFilters;
});
