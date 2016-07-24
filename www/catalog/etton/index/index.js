var __extends = this.__extends || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    __.prototype = b.prototype;
    d.prototype = new __();
};
define(["require", "exports", 'underscore', 'jquery', 'knockout', './../etton'], function (require, exports, _, $, ko, EttonPage) {
    /**
     * Index page
     */
    var IndexPage = (function (_super) {
        __extends(IndexPage, _super);
        function IndexPage(config) {
            var _this = this;
            _super.call(this, config);
            $(document).ready(function () {
                _.delay(function () {
                    ko.applyBindings(_this);
                }, 200);
            });
            this.bindHandlers(config);
        }
        IndexPage.prototype.bindHandlers = function (config) {
            //
        };
        IndexPage.prototype.clickAuth = function (obj, ev) {
            this.user.auth();
        };
        return IndexPage;
    })(EttonPage);
    return IndexPage;
});
