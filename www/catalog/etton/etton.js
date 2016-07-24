/// <reference path="../../core/libs/_def/jquery.d.ts"/>
var __extends = this.__extends || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    __.prototype = b.prototype;
    d.prototype = new __();
};
define(["require", "exports", 'jquery', './../main'], function (require, exports, $, BasePage) {
    /**
     * Page base class
     */
    var EttonPage = (function (_super) {
        __extends(EttonPage, _super);
        function EttonPage(config) {
            if (config === void 0) { config = {}; }
            _super.call(this, config);
            $(document).ready(function () {
                //...
            });
            // TODO: решить на уровне верстки и CSS
            $(window).resize(function () {
                var h = $(window).height() - $('.header-layer').height() - $('.footer-layer').height();
                $('.middle-layer').css('min-height', h);
            });
            _.delay(function () {
                $(window).resize();
            }, 400);
        }
        return EttonPage;
    })(BasePage);
    return EttonPage;
});
