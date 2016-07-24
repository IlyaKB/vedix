/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>
define(["require", "exports", 'underscore', 'jquery', 'knockout', './../../../_common/js/utils', './../../../_common/js/notifications', './spec'], function (require, exports, _, $, ko, Utils, Notifications, EttonSpec) {
    /**
     * Order detail
     */
    var EttonDetail = (function () {
        function EttonDetail(config, forms_msg) {
            this.forms_msg = forms_msg;
            this.loading = ko.observable(false);
            this.id = ko.observable(null);
            this.number = ko.observable('');
            this.createdate = ko.observable('');
            this.customer = ko.observable('');
            this.isChange = ko.observable(false);
            this.number.subscribe(this.changeData, this); // отслеживаем изменения
            this.customer.subscribe(this.changeData, this); // отслеживаем изменения
            this.spec = new EttonSpec(config, forms_msg);
            _.bindAll(this, 'cancelClick', 'saveClick');
            $(document).ready(function () {
                //
            });
        }
        EttonDetail.prototype.loadDetail = function (id) {
            var _this = this;
            var parameters = {
                jx: 'detail',
                method: 'get',
                id: id
            };
            var self = this;
            this.spec.clear();
            this.loading(true);
            return $.post('', parameters, function (response) {
                _this.loading(false);
                var data = Utils.getJSONbyText(response); // TODO: JSON-RPC 2.0
                if (data) {
                    if (data.success) {
                        _this.id(data.order.id);
                        _this.number('' + data.order.number);
                        _this.createdate('' + data.order.createdate);
                        _this.customer('' + data.order.customer);
                        _this.spec.setSpec(data.order.spec); //items( _.map(data.spec, (row, index)=>{ return _.extend(row, { index: index+1 }); }) );
                        _this.isChange(false);
                        _this.forms_msg({ 'detail': { 'show': true } });
                    }
                    else if (data.error) {
                    }
                }
                else {
                    Notifications.show('Error: ajax data empty!', Notifications.Types.ntError);
                }
            }).fail(function (data) {
                _this.loading(false);
                Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
            });
        };
        EttonDetail.prototype.createOrder = function () {
            this.id(null);
            this.number('');
            this.createdate('Сегодня');
            this.customer('');
            this.spec.clear();
            this.isChange(false);
        };
        EttonDetail.prototype.cancelClick = function (obj, ev) {
            this.closeForm();
        };
        EttonDetail.prototype.closeForm = function () {
            if (((!this.isChange()) && (!this.spec.isChange())) || confirm('Вы точно хотите закрыть окно заказа без сохранения изменений?')) {
                this.forms_msg({ 'list': { 'show': true } });
            }
        };
        EttonDetail.prototype.changeData = function () {
            this.isChange(true);
        };
        EttonDetail.prototype.saveClick = function (obj, ev) {
            var _this = this;
            var isAdd = !this.id();
            // Проверки
            if ((!this.number().length) || (this.number().length > 20)) {
                // TODO: Автоматизировать: выделить поле красным бордюром, выделить текст, снизу мелким шрифтом отобразить краткое сообщение об ошибке
                return alert('Номер должен быть не пустым и не больше 20 символов!');
            }
            if ((!this.customer().length) || (this.customer().length > 128)) {
                // TODO: Автоматизировать: выделить поле красным бордюром, выделить текст, снизу мелким шрифтом отобразить краткое сообщение об ошибке
                return alert('Название заказчика должен быть не пустым и не больше 128 символов!');
            }
            var parameters = {
                jx: 'detail',
                method: 'save',
                id: this.id(),
                number: this.number(),
                customer: this.customer(),
                spec: _.map(this.spec.items(), function (item) {
                    return {
                        id: item.id,
                        position_id: item.position_id,
                        subtype_id: item.subtype_id,
                        quantity: item.quantity
                    };
                })
            };
            return $.post('', parameters, function (response) {
                var data = Utils.getJSONbyText(response); // TODO: JSON-RPC 2.0
                if (data) {
                    if (data.success) {
                        _this.isChange(false);
                        if (isAdd) {
                            _this.id(data.id);
                            _this.forms_msg({ 'list': { 'update': true } }); // Обновляем список заказов
                        }
                        else {
                            _this.forms_msg({ 'list': { 'update': true, 'show': true } }); // Обновляем список заказов и переходим на форму списка заказов
                        }
                    }
                    else {
                    }
                }
                else {
                    Notifications.show('Error: ajax response empty!', Notifications.Types.ntError);
                }
            }).fail(function (data) {
                Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
            });
        };
        return EttonDetail;
    })();
    return EttonDetail;
});
