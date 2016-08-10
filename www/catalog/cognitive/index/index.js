var __extends = this.__extends || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    __.prototype = b.prototype;
    d.prototype = new __();
};
define(["require", "exports", 'underscore', 'jquery', 'knockout', './../cognitive', './js/purse', './js/vm'], function (require, exports, _, $, ko, CognitivePage, Purse, VM) {
    /**
     * Index page
     */
    var IndexPage = (function (_super) {
        __extends(IndexPage, _super);
        function IndexPage(config) {
            var _this = this;
            _super.call(this, config);
            // Ключ - номинал монеты
            this.client_purse = new Purse(config, 10, 30, 20, 15);
            this.vm = new VM(config);
            _.bindAll(this, 'clickMonet');
            $(document).ready(function () {
                _.delay(function () {
                    ko.applyBindings(_this);
                }, 200);
            });
        }
        /**
         * Положить монету в VM
         * @param obj
         * @param ev
         */
        IndexPage.prototype.clickMonet = function (obj, ev) {
            var nominal = $(ev.target).closest('span').data('nominal');
            if (!nominal)
                return;
            if (this.client_purse.remove(nominal)) {
                this.vm.putMonet(nominal);
            }
        };
        /**
         * Вернуть сдачу
         * @param obj
         * @param ev
         */
        IndexPage.prototype.clickShortChange = function (obj, ev) {
            this.client_purse.add(this.vm.shortChange());
        };
        return IndexPage;
    })(CognitivePage);
    return IndexPage;
});
