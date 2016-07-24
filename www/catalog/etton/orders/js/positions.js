/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>
define(["require", "exports", 'underscore', 'jquery', 'knockout', './../../../_common/js/utils', './../../../_common/js/notifications', './subtypes'], function (require, exports, _, $, ko, Utils, Notifications, EttonSubtypes) {
    /**
     * Positions table
     */
    var EttonPositions = (function () {
        function EttonPositions(config, forms_msg) {
            this.isInitialized = false;
            this.positionRow = {};
            this.forms_msg = forms_msg;
            this.visible = ko.observable(false);
            this.loading = ko.observable(false);
            this.items = ko.observableArray([]);
            this.subtypes = new EttonSubtypes(config, forms_msg);
            _.bindAll(this, 'clickRow');
            $(document).ready(function () {
                //
            });
        }
        EttonPositions.prototype.show = function () {
            if (!this.isInitialized) {
                this.updateList();
            }
            $('#back_modal_layer').show();
            this.visible(true);
        };
        EttonPositions.prototype.hide = function () {
            $('#back_modal_layer').hide();
            this.visible(false);
        };
        EttonPositions.prototype.displayed = function () {
            return this.visible();
        };
        EttonPositions.prototype.buildParameters = function () {
            return {
                jx: 'positions',
                method: 'get'
            };
        };
        EttonPositions.prototype.updateList = function () {
            var _this = this;
            var self = this;
            var parameters = this.buildParameters();
            this.loading(true);
            return $.post('', parameters, function (response) {
                _this.loading(false);
                var data = Utils.getJSONbyText(response); // TODO: JSON-RPC 2.0
                if (data) {
                    if (data.success) {
                        _this.items(data.positions);
                        _this.isInitialized = true;
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
        EttonPositions.prototype.cancelClick = function (obj, ev) {
            this.hide();
        };
        EttonPositions.prototype.clickRow = function (obj, ev) {
            this.positionRow = obj;
            if (obj.subtypes_count != 0) {
                this.hide();
                this.forms_msg({ 'subtypes': { 'show': this.positionRow } });
            }
            else {
                this.hide();
                this.forms_msg({ 'spec': { 'add': this.positionRow } });
            }
        };
        return EttonPositions;
    })();
    return EttonPositions;
});
