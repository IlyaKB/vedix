/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>
define(["require", "exports", 'jquery', 'knockout', './../../../_common/js/knockout_ext', './filters', './sorting', './../../../_components/pagination/pagination'], function (require, exports, $, ko, KnockoutExt, MagFilters, MagSorting, Pagination) {
    /**
     * List
     */
    var MagList = (function () {
        function MagList(config) {
            KnockoutExt.initSourceData();
            this.positions = ko.observableArray([]);
            this.filters = new MagFilters(config);
            this.sorting = new MagSorting(config);
            this.pagination = new Pagination('magazin/');
            $(document).ready(function () {
                //
            });
            this.bindHandlers(config);
        }
        MagList.prototype.bindHandlers = function (config) {
            //
        };
        return MagList;
    })();
    return MagList;
});
