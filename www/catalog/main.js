/// <reference path="../core/libs/_def/jquery.d.ts"/>
define(["require", "exports", 'jquery', './_common/js/user'], function (require, exports, $, User) {
    /**
     * Page base class
     */
    var BasePage = (function () {
        function BasePage(config) {
            if (config === void 0) { config = {}; }
            this.user = new User(config);
            $(document).ready(function () {
                //...
            });
        }
        return BasePage;
    })();
    return BasePage;
});
