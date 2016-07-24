/// <reference path="../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../core/libs/_def/knockout.d.ts"/>
define(["require", "exports", 'jquery', 'knockout'], function (require, exports, $, ko) {
    //export enum NotificationsType { ntError, ntSuccess, ntWarning };
    //export enum NotificationsFlag { ntAnimate, ntFast };
    var Notifications = (function () {
        function Notifications() {
        }
        Notifications.show = function (text, type) {
            if (type === void 0) { type = Notifications.Types.ntSuccess; }
            if (Notifications.tmr) {
                clearTimeout(Notifications.tmr);
                Notifications.hide(Notifications.Flags.ntFast);
            }
            Notifications.tmr = setTimeout(Notifications.hide, Notifications.wait);
            var el = null;
            switch (type) {
                case Notifications.Types.ntError:
                    {
                        el = Notifications.errorEl;
                        break;
                    }
                case Notifications.Types.ntSuccess:
                    {
                        el = Notifications.successEl;
                        break;
                    }
                case Notifications.Types.ntWarning:
                    {
                        el = Notifications.warningEl;
                        break;
                    }
            }
            el.html(text);
            el.height(0).show().animate({ height: '+=30px' }, 250);
        };
        Notifications.hide = function (flag) {
            if (flag === void 0) { flag = Notifications.Flags.ntAnimate; }
            var el = Notifications.rootEl.find('.notification:visible');
            if (!el.length)
                return;
            if (flag == Notifications.Flags.ntFast) {
                el.hide();
            }
            else {
                Notifications.rootEl.find('.notification:visible').animate({ height: '-=30px' }, 250, 'swing', function () {
                    el.hide();
                });
            }
        };
        Notifications.Types = { ntError: 1, ntSuccess: 2, ntWarning: 3 };
        Notifications.Flags = { ntAnimate: 1, ntFast: 2 };
        Notifications.wait = 3000;
        Notifications.tmr = null;
        Notifications.top = 0;
        Notifications.__constructor = (function () {
            Notifications.text = ko.observable('');
            ko.bindingHandlers.notifications = {
                init: function (element, valueAccessor, allBindingsAccessor, viewModel) {
                    Notifications.rootEl = $(element);
                    Notifications.top = Notifications.rootEl.offset().top;
                    Notifications.errorEl = Notifications.rootEl.find('.error');
                    Notifications.warningEl = Notifications.rootEl.find('.warning');
                    Notifications.successEl = Notifications.rootEl.find('.success');
                    Notifications.rootEl.on('click', function () {
                        Notifications.hide();
                    });
                    $(window).scroll(function () {
                        var _scrollTop = $(document).scrollTop();
                        if (_scrollTop < Notifications.top) {
                            $('.notification').css('position', 'relative');
                        }
                        else {
                            $('.notification').css('position', 'fixed');
                        }
                    }).scroll();
                }
            };
        })();
        return Notifications;
    })();
    return Notifications;
});
