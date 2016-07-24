/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>
define(["require", "exports", 'underscore', 'jquery', 'knockout', './../../../_common/js/utils', './../../../_common/js/notifications', './detail'], function (require, exports, _, $, ko, Utils, Notifications, EttonDetail) {
    /**
     * List
     */
    var EttonList = (function () {
        function EttonList(config, forms_msg) {
            var _this = this;
            this.isInitialized = false;
            this.inputDelayMs = 250; // сколько ждём мс перед отправкой ajax-запроса после key/mouse-событий (сортировка)
            this.forms_msg = forms_msg;
            this.loading = ko.observable(false);
            this.items = ko.observableArray([]);
            this.sorting_field = ko.observable('');
            this.sorting_dir = ko.observable('');
            this.sorting_dir_text = ko.computed(function () {
                return (_this.sorting_dir() == 'asc' ? '^' : 'v');
            }, this);
            ko.computed(this.updateList, this).extend({ throttle: this.inputDelayMs });
            this.detail = new EttonDetail(config, this.forms_msg);
            _.bindAll(this, 'updateList', 'clickTR', 'clickEdit', 'clickAdd', 'clickDelete');
            $(document).ready(function () {
                //
            });
            this.isInitialized = true;
        }
        EttonList.prototype.sortClick = function (obj, ev) {
            var f = $(ev.target).closest('th').data('clm');
            if (f) {
                if (this.sorting_field() == f) {
                    var s = this.sorting_dir();
                    this.sorting_dir(s == 'asc' ? 'desc' : 'asc');
                }
                else {
                    this.sorting_field(f);
                    this.sorting_dir('asc');
                }
            }
        };
        EttonList.prototype.buildParameters = function () {
            return {
                jx: 'list',
                sortfield: this.sorting_field(),
                sortdir: this.sorting_dir(),
            };
        };
        EttonList.prototype.updateList = function () {
            var _this = this;
            var self = this;
            var parameters = this.buildParameters();
            if (!this.isInitialized)
                return; // Первое отображение данных получаем обычным GET, требовалось лишь инициализировать вложенные ko-объекты в this.updateList
            this.loading(true);
            return $.ajax({
                url: '',
                type: 'POST',
                data: $.param(parameters),
                success: function (response) {
                    _this.loading(false);
                    var data = Utils.getJSONbyText(response); // TODO: JSON-RPC 2.0
                    if (data) {
                        if (data.success) {
                            _this.items(data.orders);
                        }
                    }
                    else {
                        Notifications.show('Error: ajax data empty!', Notifications.Types.ntError);
                    }
                },
                error: function (response) {
                    _this.loading(false);
                    Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
                }
            });
        };
        EttonList.prototype.clickRefresh = function (obj, ev) {
            this.updateList();
        };
        EttonList.prototype.clickTR = function (obj, ev) {
            if ($(ev.target).closest('.js-no-tr-click').length)
                return false;
            this.clickEdit(obj, ev);
        };
        EttonList.prototype.clickEdit = function (obj, ev) {
            this.detail.loadDetail(obj.id).done(function (response) {
            });
        };
        EttonList.prototype.clickDelete = function (obj, ev) {
            var _this = this;
            if (!confirm('Вы точно хотите удалить заказ со всей спецификацией?'))
                return;
            var parameters = {
                jx: 'detail',
                method: 'delete',
                id: obj.id
            };
            return $.post('', parameters, function (response) {
                var data = Utils.getJSONbyText(response); // TODO: JSON-RPC 2.0
                if (data) {
                    if (data.success) {
                        _this.updateList();
                    }
                }
                else {
                    Notifications.show('Error: ajax response empty!', Notifications.Types.ntError);
                }
            }).fail(function (data) {
                Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
            });
        };
        EttonList.prototype.clickAdd = function (obj, ev) {
            this.detail.createOrder();
            this.forms_msg({ 'detail': { 'show': true } });
        };
        return EttonList;
    })();
    return EttonList;
});
