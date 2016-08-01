define(["require", "exports", 'knockout', './../../_common/js/knockout_ext'], function (require, exports, ko, KnockoutExt) {
    var Pagination = (function () {
        function Pagination(config) {
            var _this = this;
            KnockoutExt.initSourceData();
            this.previous_page = ko.observable('');
            this.next_page = ko.observable('');
            this.items = ko.observableArray([]);
            this.previousPageUrl = ko.computed(function () {
                var _pg = _this.previous_page();
                return (/p=\d+/.test(_pg) ? _pg : '?p=1'); // TODO
                /*var _urlAggregate: any = urlAggregate();
                _urlAggregate.p = this.previous_page();*/
                /*return urlAggregate().replace(/([?&]p=)[\d]+/i, '\\1'+this.previous_page());*/
            }, this);
            this.nextPageUrl = ko.computed(function () {
                return '?p=' + _this.next_page();
            }, this);
        }
        Pagination.prototype.rebuild = function () {
            // TODO
        };
        return Pagination;
    })();
    return Pagination;
});
