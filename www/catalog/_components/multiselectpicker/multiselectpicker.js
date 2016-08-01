/// <reference path="./../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="./../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="./../../../core/libs/_def/d3.d.ts"/>
define(["require", "exports"], function (require, exports) {
    /**
     * Компонент Multi Select Picker со встроенной фильтрацией записей
     * Использует библиотеки: KnockoutJS, UnderscoreJS, jQuery
     * Версия: 1.0
     * Последнее обновление: 2014.04.08
     */
    var MultiSelectPicker = (function () {
        function MultiSelectPicker(config) {
            this.config = config;
            var self = this;
            _.bindAll(this, 'search', 'optionClick');
            this.cntId = this.config.cntId;
            this.listId = this.cntId + '_list';
            this.filterId = this.cntId + '_filter';
            this.elCnt = $('#' + this.cntId);
            this.elList = $('#' + this.listId);
            this.elFilter = $('#' + this.filterId);
            this.options = ko.observableArray();
            this.searchValue = ko.observable('');
            this.searchValue.subscribe(this.search);
            this.value = ko.observable(this.config.value);
            // Clear key
            this.elFilter.keyup(function (ev) {
                if (ev.which == 27) {
                    self.searchValue('');
                }
            });
            //ko.applyBindings(this, this.elCnt[0]); // TODO?
            this.setData(this.config.data || []);
            this.initCheckboxes();
        }
        MultiSelectPicker.prototype.setData = function (data) {
            this.options(data);
        };
        MultiSelectPicker.prototype.initCheckboxes = function () {
            var self = this;
            _.each(this.value(), function (it) {
                var li = $('li[data-url-name="' + it + '"]', self.elList);
                li.addClass('active');
                $('input', li).prop('checked', true);
            });
        };
        MultiSelectPicker.prototype.optionClick = function (obj, ev) {
            var li = $(ev.target).closest('li');
            var input = $('input', li);
            if (ev.target.tagName != 'INPUT') {
                input.prop('checked', !input.prop('checked'));
            }
            var checked = input.prop('checked');
            if (String(li.data('url-name')) == 'multiselect-all') {
                $('li', this['ul']).each(function (index, e) {
                    if (!index)
                        return true;
                    $('input', e).prop('checked', checked);
                });
                if (checked) {
                    $('li', this.elList).addClass('active');
                }
                else {
                    $('li', this.elList).removeClass('active');
                }
            }
            else if (!checked) {
                $('li:first input', this.elList).prop('checked', false);
                $('li:first', this.elList).removeClass('active');
                li.removeClass('active');
            }
            else if (checked) {
                li.addClass('active');
            }
            this.updateKnockoutArray();
            return true;
        };
        MultiSelectPicker.prototype.updateKnockoutArray = function () {
            var arr = [];
            $('li', this.elList).each(function (index, e) {
                if (!index)
                    return true;
                if ($('input', e).prop('checked'))
                    arr.push($(e).data('url-name'));
            });
            this.value(arr);
        };
        MultiSelectPicker.prototype.search = function (searchValue) {
            var li0 = $('li:first', this.elList);
            if (searchValue) {
                var input0 = $('input', li0);
                input0.prop('checked', false);
                li0.hide();
            }
            else {
                li0.show();
            }
            var altSearchValue1 = this.layoutEnToRu(searchValue);
            var altSearchValue2 = this.layoutRuToEn(searchValue);
            var altSearchValue3 = this.similarEnToRu(searchValue);
            var altSearchValue4 = this.similarRuToEn(searchValue);
            var re = new RegExp(this.prepareMatch(searchValue) + '|' + this.prepareMatch(altSearchValue1) + '|' + this.prepareMatch(altSearchValue2) + '|' + this.prepareMatch(altSearchValue3) + '|' + this.prepareMatch(altSearchValue4), 'ig');
            $('li', this.elList).each(function (index, e) {
                if (!index)
                    return true;
                if ($('span', e).text().match(re)) {
                    $(e).show();
                }
                else {
                    $(e).hide();
                }
            });
            this.elList.scrollTop(0);
        };
        MultiSelectPicker.prototype.layoutEnToRu = function (str) {
            var replacer = {
                'q': 'й',
                'w': 'ц',
                'e': 'у',
                'r': 'к',
                't': 'е',
                'y': 'н',
                'u': 'г',
                'i': 'ш',
                'o': 'щ',
                'p': 'з',
                '[': 'х',
                ']': 'ъ',
                'a': 'ф',
                's': 'ы',
                'd': 'в',
                'f': 'а',
                'g': 'п',
                'h': 'р',
                'j': 'о',
                'k': 'л',
                'l': 'д',
                ';': 'ж',
                '\'': 'э',
                'z': 'я',
                'x': 'ч',
                'c': 'с',
                'v': 'м',
                'b': 'и',
                'n': 'т',
                'm': 'ь',
                ',': 'б',
                '.': 'ю',
                '/': '.'
            };
            return str.replace(/[A-z/,.;\'\]\[]/g, function (x) {
                return x == x.toLowerCase() ? replacer[x] : replacer[x.toLowerCase()].toUpperCase();
            });
        };
        MultiSelectPicker.prototype.layoutRuToEn = function (str) {
            var replacer = {
                'й': 'q',
                'ц': 'w',
                'у': 'e',
                'к': 'r',
                'е': 't',
                'н': 'y',
                'г': 'u',
                'ш': 'i',
                'щ': 'o',
                'з': 'p',
                'х': '[',
                'ъ': ']',
                'ф': 'a',
                'ы': 's',
                'в': 'd',
                'а': 'f',
                'п': 'g',
                'р': 'h',
                'о': 'j',
                'л': 'k',
                'д': 'l',
                'ж': ';',
                'э': '\'',
                'я': 'z',
                'ч': 'x',
                'с': 'c',
                'м': 'v',
                'и': 'b',
                'т': 'n',
                'ь': 'm',
                'б': ',',
                'ю': '.',
                '.': '/'
            };
            return str.replace(/[А-Яа-я/,.;\'\]\[]/g, function (x) {
                return x == x.toLowerCase() ? replacer[x] : replacer[x.toLowerCase()].toUpperCase();
            });
        };
        // TODO: улучшить
        MultiSelectPicker.prototype.similarEnToRu = function (str) {
            var replacer = {
                'q': 'ю',
                'w': 'в',
                'e': 'е',
                'r': 'р',
                't': 'т',
                'y': 'й',
                'u': 'у',
                'i': 'ы',
                'o': 'о',
                'p': 'п',
                'a': 'а',
                's': 'с',
                'd': 'д',
                'f': 'ф',
                'g': 'г',
                'h': 'х',
                'j': 'ж',
                'k': 'к',
                'l': 'л',
                'z': 'з',
                'x': 'кс',
                'c': 'ц',
                'v': 'в',
                'b': 'б',
                'n': 'н',
                'm': 'м'
            };
            return str.replace(/[A-z]/g, function (x) {
                return x == x.toLowerCase() ? replacer[x] : replacer[x.toLowerCase()].toUpperCase();
            });
        };
        // TODO: улучшить
        MultiSelectPicker.prototype.similarRuToEn = function (str) {
            var replacer = {
                'й': 'y',
                'ц': 'c',
                'у': 'u',
                'к': 'k',
                'е': 'e',
                'н': 'n',
                'г': 'g',
                'ш': 'sh',
                'щ': 'sch',
                'з': 'z',
                'х': 'h',
                'ъ': '',
                'ф': 'f',
                'ы': 'i',
                'в': 'v',
                'а': 'a',
                'п': 'n',
                'р': 'p',
                'о': 'o',
                'л': 'l',
                'д': 'd',
                'ж': 'j',
                'э': 'e',
                'я': 'ya',
                'ч': 'ch',
                'с': 's',
                'м': 'm',
                'и': 'i',
                'т': 't',
                'ь': '',
                'б': 'b',
                'ю': 'yu'
            };
            return str.replace(/[А-Яа-я]/g, function (x) {
                return x == x.toLowerCase() ? replacer[x] : replacer[x.toLowerCase()].toUpperCase();
            });
        };
        MultiSelectPicker.prototype.prepareMatch = function (value) {
            var replacer = [['[', '\\['], [']', '\\]'], ['.', '\\.'], ['(', '\\('], [')', '\\)'], ['|', '\\|'], ['+', '\\+'], ['*', '\\*'], ['^', '\\^'], ['$', '\\$']];
            for (var i = 0; i < replacer.length; i++) {
                value = value.replace(replacer[i][0], replacer[i][1]);
            }
            return value;
        };
        MultiSelectPicker.prototype.clearSelect = function () {
            this.value([]);
            $('li', this['ul']).removeClass('active');
            $('li input', this['ul']).prop('checked', false);
        };
        return MultiSelectPicker;
    })();
    return MultiSelectPicker;
});
