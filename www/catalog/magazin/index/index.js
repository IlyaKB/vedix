var __extends = this.__extends || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    __.prototype = b.prototype;
    d.prototype = new __();
};
define(["require", "exports", 'underscore', 'jquery', 'knockout', './../magazin', './js/list', './js/detail'], function (require, exports, _, $, ko, MagazinPage, MagList, MagDetail) {
    /**
     * Index page
     */
    var IndexPage = (function (_super) {
        __extends(IndexPage, _super);
        function IndexPage(config) {
            var _this = this;
            _super.call(this, config);
            if (config.is_list) {
                this.list = new MagList(config);
            }
            else {
                this.detail = new MagDetail(config);
            }
            $(document).ready(function () {
                _.delay(function () {
                    ko.applyBindings(_this);
                }, 200);
            });
            this.bindHandlers(config);
        }
        IndexPage.prototype.bindHandlers = function (config) {
            // TODO: решить на уровне верстки и CSS
            $(window).resize(function () {
                var elLeft = $('div.left-layer');
                var elLeftBlocks = elLeft.find('div.block');
                var h1 = 0;
                elLeftBlocks.each(function (i, e) {
                    h1 += $(e).height();
                });
                var elMiddle = $('div.middle-layer');
                var h2 = Math.max(elMiddle.height() - 5, parseInt(elMiddle.css('min-height')) - 7);
                elLeft.height(Math.max(h1, h2));
            }).resize();
        };
        IndexPage.prototype.addClick = function (obj, _ev) {
            obj.order_count(obj.order_count() + 1);
        };
        IndexPage.prototype.delClick = function (obj, _ev) {
            obj.order_count(Math.max(0, obj.order_count() - 1));
            return false;
        };
        IndexPage.prototype.preOrderClick = function (_obj, _ev) {
            // TODO
        };
        return IndexPage;
    })(MagazinPage);
    return IndexPage;
});
