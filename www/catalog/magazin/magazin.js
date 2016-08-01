/// <reference path="../../core/libs/_def/jquery.d.ts"/>
define(["require", "exports", 'jquery', './../_common/js/user'], function (require, exports, $, User) {
    /**
     * Page base class
     */
    var MagazinPage = (function () {
        function MagazinPage(config) {
            if (config === void 0) { config = {}; }
            this.config = config;
            this.user = new User(config);
            $(document).ready(function () {
                $('#admin_link').attr('href', '/admin');
                $('#demo_off').attr('href', '?demo=0');
                $('#demo_on').attr('href', '?demo=1');
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
        return MagazinPage;
    })();
    return MagazinPage;
});
