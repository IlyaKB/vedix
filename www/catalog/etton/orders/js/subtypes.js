/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>
define(["require", "exports", 'underscore', 'jquery', 'knockout', './../../../_common/js/utils', './../../../_common/js/notifications'], function (require, exports, _, $, ko, Utils, Notifications) {
    /**
     * Subtypes table
     */
    var EttonSubtypes = (function () {
        function EttonSubtypes(config, forms_msg) {
            this.positionRow = {};
            this.forms_msg = forms_msg;
            this.visible = ko.observable(false);
            this.loading = ko.observable(false);
            this.items = ko.observableArray([]);
            _.bindAll(this, 'clickRow');
            $(document).ready(function () {
                //
            });
        }
        EttonSubtypes.prototype.show = function (positionRow) {
            this.positionRow = positionRow;
            this.updateList();
            $('#back_modal_layer').show();
            this.visible(true);
        };
        EttonSubtypes.prototype.hide = function () {
            $('#back_modal_layer').hide();
            this.visible(false);
        };
        EttonSubtypes.prototype.displayed = function () {
            return this.visible();
        };
        EttonSubtypes.prototype.buildParameters = function () {
            return {
                jx: 'subtypes',
                method: 'get',
                position_id: this.positionRow.id
            };
        };
        EttonSubtypes.prototype.updateList = function () {
            var _this = this;
            var self = this;
            this.items([]);
            var parameters = this.buildParameters();
            this.loading(true);
            return $.post('', parameters, function (response) {
                _this.loading(false);
                var data = Utils.getJSONbyText(response); // TODO: JSON-RPC 2.0
                if (data) {
                    if (data.success) {
                        _this.items(data.subtypes);
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
        EttonSubtypes.prototype.cancelClick = function (obj, ev) {
            this.hide();
            this.forms_msg({ 'positions': { 'show': true } });
        };
        EttonSubtypes.prototype.clickRow = function (obj, ev) {
            this.positionRow.subtype_id = obj.id;
            this.positionRow.subtype_name = obj.name;
            this.hide();
            this.forms_msg({ 'spec': { 'add': this.positionRow } });
        };
        return EttonSubtypes;
    })();
    return EttonSubtypes;
});
