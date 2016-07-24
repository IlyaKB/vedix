/// <reference path="../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../core/libs/_def/knockout.d.ts"/>
define(["require", "exports", 'underscore', 'jquery', 'knockout'], function (require, exports, _, $, ko) {
    var KnockoutExt = (function () {
        function KnockoutExt() {
        }
        KnockoutExt.initToggle = function () {
            if (ko.bindingHandlers.toggle)
                return;
            ko.bindingHandlers.toggle = {
                init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
                    var $el = $(element);
                    var observableObject = valueAccessor();
                    observableObject(parseInt(observableObject()));
                    $el.on('click', function (ev) {
                        observableObject(parseInt(observableObject()) ? 0 : 1);
                    });
                }
            };
        };
        KnockoutExt.initSourceData = function () {
            if (ko.bindingHandlers.sourcedata)
                return;
            function createMember(type) {
                switch (type.toLowerCase()) {
                    case 'number':
                        return 0;
                    case 'boolean':
                        return false;
                    case 'array':
                        return [];
                    case 'object':
                        return {};
                    case 'observable':
                    case 'observable<string>':
                        return ko.observable('');
                    case 'observable<boolean>':
                        return ko.observable(false);
                    case 'observable<number>':
                        return ko.observable(0);
                    case 'observablearray':
                        return ko.observableArray([]);
                    default:
                        return '';
                }
            }
            function scan(parentObj, parentObjType, rootEl) {
                _.each(rootEl.children(), function (el) {
                    var _memberType = $(el).data('sd-type');
                    var _memberField = $(el).data('sd-field');
                    var memberType = String(_memberType ? _memberType : '').toLowerCase();
                    var memberField = String(_memberField ? _memberField : ''); //.toLowerCase();
                    if ((memberType) || (memberField)) {
                        var memberValue = null;
                        if ((parentObjType == 'object') && (parentObj.hasOwnProperty(memberField))) {
                            memberValue = parentObj[memberField];
                        }
                        else {
                            memberValue = createMember(memberType);
                        }
                        switch (memberType) {
                            case 'number':
                                {
                                    if ($(el).get(0).tagName == 'INPUT') {
                                        memberValue = parseFloat($(el).val());
                                    }
                                    else {
                                        memberValue = parseFloat($(el).text());
                                    }
                                    if (!memberValue)
                                        memberValue = 0;
                                    break;
                                }
                            case 'boolean':
                                {
                                    if ($(el).get(0).tagName == 'INPUT') {
                                        var v = $(el).val();
                                        memberValue = (v == 'true' || v == '1' || v == 'on' ? true : false);
                                    }
                                    break;
                                }
                            case 'array':
                                {
                                    break;
                                }
                            case 'object':
                                {
                                    break;
                                }
                            case 'observable':
                            case 'observable<string>':
                                {
                                    if ($(el).get(0).tagName == 'INPUT') {
                                        memberValue($(el).val());
                                    }
                                    else {
                                        memberValue($(el).html());
                                    }
                                    break;
                                }
                            case 'observable<boolean>':
                                {
                                    if ($(el).get(0).tagName == 'INPUT') {
                                        var v = $(el).val();
                                        memberValue(v == 'true' || v == '1' || v == 'on' ? true : false);
                                    }
                                    break;
                                }
                            case 'observable<number>':
                                {
                                    if ($(el).get(0).tagName == 'INPUT') {
                                        memberValue(parseFloat($(el).val()));
                                    }
                                    else {
                                        memberValue(parseFloat($(el).text()));
                                    }
                                    break;
                                }
                            case 'observablearray':
                                {
                                    break;
                                }
                            default:
                                {
                                    if ($(el).get(0).tagName == 'INPUT') {
                                        memberValue = $(el).val();
                                    }
                                    else {
                                        memberValue = $(el).text();
                                    }
                                }
                        }
                        if ((memberType == 'array') || (memberType == 'object') || (memberType == 'observablearray')) {
                            scan(memberValue, memberType, $(el));
                        }
                        if ((parentObjType == 'array') || (parentObjType == 'observablearray')) {
                            if (parentObj['push']) {
                                parentObj.push(memberValue);
                            }
                            else {
                                console.error('Error in knockoutJS! Use a modern browser.');
                            }
                        }
                        else if (parentObjType == 'object') {
                            parentObj[memberField] = memberValue;
                        }
                    }
                    else {
                        scan(parentObj, parentObjType, $(el));
                    }
                });
            }
            ko.bindingHandlers.sourcedata = {
                init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
                    var $root = $(element);
                    var observableObject = valueAccessor();
                    var fieldType = String($root.data('sd-type')).toLowerCase();
                    scan(observableObject, fieldType, $root);
                    /*var resultObject: any = createMember(fieldType);
                    scan(resultObject, fieldType, $root);
                    var obj: any = ( fieldType == 'object' ? resultObject : resultObject() );
                    if (fieldType == 'observablearray') {
                        _.each(obj, (it)=>{
                            observableObject.push(it);
                        });
                    } else if (fieldType == 'observable') {
                        observableObject(obj); // TODO: проверить
                    } else if (fieldType == 'object') {
                        _.extend(observableObject, obj);
                    }*/
                    $root.remove();
                    if (allBindingsAccessor().hasOwnProperty('sourcedataOnComplete')) {
                        allBindingsAccessor().sourcedataOnComplete();
                    }
                }
            };
        };
        return KnockoutExt;
    })();
    return KnockoutExt;
});
