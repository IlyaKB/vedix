/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.colorbox.d.ts"/>
define(["require", "exports", 'jquery', 'knockout', 'jquery-colorbox', './../../../_components/comments/comments'], function (require, exports, $, ko, jquery_colorbox, Comments) {
    /**
     * Detail
     */
    var MagDetail = (function () {
        function MagDetail(config) {
            jquery_colorbox;
            this.hitsPrint = ko.observable(0);
            this.order_count = ko.observable(0); // TODO: из кук
            this.comments = new Comments(config);
            $(document).ready(function () {
                //
            });
            this.bindHandlers(config);
        }
        MagDetail.prototype.bindHandlers = function (config) {
            $('#feature_photos a.gallery').colorbox({ opacity: 0.5, rel: 'group1' });
        };
        return MagDetail;
    })();
    return MagDetail;
});
