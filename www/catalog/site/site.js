/// <reference path="../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../core/libs/_def/knockout.d.ts"/>
/// <reference path="../../core/libs/_def/underscore.d.ts"/>
var __extends = this.__extends || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    __.prototype = b.prototype;
    d.prototype = new __();
};
define(["require", "exports", 'jquery', './../main', './_components/poll/poll'], function (require, exports, $, BasePage, Poll) {
    /**
     * Page base class
     */
    var SitePage = (function (_super) {
        __extends(SitePage, _super);
        function SitePage(config) {
            if (config === void 0) { config = {}; }
            _super.call(this, config);
            var elPoll1 = $('#poll_first');
            this.poll_first = (elPoll1.length ? new Poll('#poll_first', config) : null);
            var elPoll2 = $('#poll_second');
            this.poll_second = (elPoll2.length ? new Poll('#poll_second', config) : null);
            var elPollR = $('#poll_random');
            this.poll_random = (elPollR.length ? new Poll('#poll_random', config) : null);
            $(document).ready(function () {
                $('#admin_link').attr('href', '/admin');
                $('#id_uagent').hide().click(function () {
                    $('#id_uagent').hide();
                    $('#id_uagent_but').show();
                });
            });
            $(window).resize(function () {
                var h = $(window).height() - $('.header-layer').height() - $('.footer-layer').height();
                $('.middle-layer').css('min-height', h);
            }).resize();
        }
        return SitePage;
    })(BasePage);
    return SitePage;
});
