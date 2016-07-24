var __extends = this.__extends || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    __.prototype = b.prototype;
    d.prototype = new __();
};
define(["require", "exports", 'knockout', './../site'], function (require, exports, ko, SitePage) {
    var IndexPage = (function (_super) {
        __extends(IndexPage, _super);
        function IndexPage(config) {
            _super.call(this, config);
            var self = this;
            ko.applyBindings(this);
        }
        return IndexPage;
    })(SitePage);
    return IndexPage;
});
