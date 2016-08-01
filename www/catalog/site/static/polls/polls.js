/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>
define(["require", "exports", 'jquery', 'knockout'], function (require, exports, $, ko) {
    //import HashStateControls = require('./../../_common/js/hash-state-controls');
    /**
     * Blog page
     */
    var PollsPage = (function () {
        function PollsPage(config) {
            //KnockoutExt.initSourceData();
            var _this = this;
            $(document).ready(function () {
                ko.applyBindings(_this);
                //HashStateControls.hashChange();
            });
        }
        return PollsPage;
    })();
    return PollsPage;
});
