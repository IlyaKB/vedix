/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>
define(["require", "exports", 'underscore', 'jquery', 'knockout'], function (require, exports, _, $, ko) {
    //import SelectControl = require('./../../../_components/selectcontrol/select-control');
    /**
     * Specifications
     */
    var EttonSpec = (function () {
        function EttonSpec(config, forms_msg) {
            this.forms_msg = forms_msg;
            this.items = ko.observableArray([]);
            this.isChange = ko.observable(false);
            /*TODO: SelectControl для выбора подтипа
            this.subtype = new SelectControl({
                selInputName: 'subtype_name',
                selPopup: '#subtypes_popup',
                loadListFunc: this.loadSubtypesList,
                itemSelectedFunc: this.subtypeSelected
            });*/
            _.bindAll(this, 'clickDelete');
            $(document).ready(function () {
                //
            });
        }
        EttonSpec.prototype.setSpec = function (_spec) {
            var _this = this;
            var index = 1;
            this.items(_.map(_spec, function (spec) {
                var item = {
                    index: ko.observable(index++),
                    id: spec.id,
                    position_id: spec.position_id,
                    name: spec.name,
                    subtype_id: spec.subtype_id,
                    subtype_name: spec.subtype_name,
                    quantity: ko.observable(spec.quantity)
                };
                item.quantity.subscribe(function () {
                    _this.isChange(true);
                }, _this);
                return item;
            }));
            this.isChange(false);
        };
        EttonSpec.prototype.add = function (positionRow) {
            var isDetected = false;
            _.each(this.items(), function (item) {
                if ((item.position_id == positionRow.id) && ((positionRow.subtypes_count == 0) || (positionRow.subtype_id == item.subtype_id))) {
                    alert('Этот товар уже добавлен в заказ!');
                    isDetected = true;
                    return true;
                }
            });
            if (isDetected)
                return false;
            var id = Math.min(0, _.min(this.items(), function (item) {
                return (item.id ? item.id : 0);
            }).id);
            //this.items.unshift({
            this.items.push({
                id: (id ? id - 1 : -1),
                index: ko.observable(),
                position_id: positionRow.id,
                name: positionRow.name,
                subtype_id: positionRow.subtype_id,
                subtype_name: positionRow.subtype_name,
                quantity: ko.observable(1)
            });
            this.reIndex();
            this.isChange(true);
            return true;
        };
        EttonSpec.prototype.reIndex = function () {
            var index = 1;
            _.each(this.items(), function (item) {
                item.index(index++);
            });
        };
        EttonSpec.prototype.clickAdd = function (obj, ev) {
            this.forms_msg({ 'positions': { 'show': true } });
        };
        EttonSpec.prototype.clickDelete = function (obj, ev) {
            this.items.remove(function (item) {
                return (item.id == obj.id);
            });
            this.reIndex();
            this.isChange(true);
        };
        EttonSpec.prototype.clear = function () {
            this.items([]);
        };
        EttonSpec.prototype.quantityDeincClick = function (obj, ev) {
            obj.quantity(Math.max(1, +obj.quantity() - 1));
        };
        EttonSpec.prototype.quantityIncClick = function (obj, ev) {
            obj.quantity(+obj.quantity() + 1);
        };
        return EttonSpec;
    })();
    return EttonSpec;
});
