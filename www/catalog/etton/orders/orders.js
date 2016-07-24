var __extends = this.__extends || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    __.prototype = b.prototype;
    d.prototype = new __();
};
define(["require", "exports", 'underscore', 'jquery', 'knockout', './../../_common/js/knockout_ext', './../etton', './js/list', './js/positions'], function (require, exports, _, $, ko, KnockoutExt, EttonPage, EttonList, EttonPositions) {
    /**
     * Orders page
     */
    var OrdersPage = (function (_super) {
        __extends(OrdersPage, _super);
        function OrdersPage(config) {
            var _this = this;
            _super.call(this, config);
            KnockoutExt.initSourceData();
            this.form = ko.observable('list'); // Формы: list, detail
            this.forms_msg = ko.observable([]); // Сообщение для реакции других форм на изменения в текущей
            this.forms_msg.subscribe(this.messageForms, this);
            this.list = new EttonList(config, this.forms_msg);
            this.positions = new EttonPositions(config, this.forms_msg);
            $(document).ready(function () {
                _.delay(function () {
                    ko.applyBindings(_this);
                    $(document).keyup(function (ev) {
                        if (ev.which == 27) {
                            if (_this.positions.displayed()) {
                                _this.forms_msg({ 'positions': { 'hide': true } });
                            }
                            else if (_this.positions.subtypes.displayed()) {
                                _this.forms_msg({ 'subtypes': { 'hide': true } });
                            }
                            else if (_this.form() == 'detail') {
                                _this.forms_msg({ 'detail': { 'hide': true } });
                            }
                        }
                    });
                }, 200);
            });
        }
        /**
         * Обработка сообщений, генерируемых формами
         */
        OrdersPage.prototype.messageForms = function (forms_msg) {
            var _this = this;
            _.each(forms_msg, function (messages, form) {
                switch (form) {
                    case 'list':
                        {
                            if (messages.show)
                                _this.form('list');
                            if (messages.update)
                                _this.list.updateList();
                            break;
                        }
                    case 'detail':
                        {
                            if (messages.show)
                                _this.form('detail');
                            if (messages.hide)
                                _this.list.detail.closeForm();
                            break;
                        }
                    case 'spec':
                        {
                            if (messages.add) {
                                var result = _this.list.detail.spec.add(messages.add);
                                if (!result)
                                    _this.positions.show();
                            }
                            break;
                        }
                    case 'positions':
                        {
                            if (messages.show)
                                _this.positions.show();
                            if (messages.hide)
                                _this.positions.hide();
                            break;
                        }
                    case 'subtypes':
                        {
                            if (messages.show)
                                _this.positions.subtypes.show(messages.show);
                            if (messages.hide)
                                _this.positions.subtypes.hide();
                            break;
                        }
                }
            });
        };
        return OrdersPage;
    })(EttonPage);
    return OrdersPage;
});
