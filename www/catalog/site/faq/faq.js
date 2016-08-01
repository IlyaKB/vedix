var __extends = this.__extends || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    __.prototype = b.prototype;
    d.prototype = new __();
};
define(["require", "exports", 'underscore', 'jquery', 'knockout', './../../_common/js/knockout_ext', './../../_common/js/utils', './../../_common/js/notifications', './../../_common/js/analytics', './../../_common/js/hash-state-controls', './../site'], function (require, exports, _, $, ko, KnockoutExt, Utils, Notifications, Analytics, HashStateControls, SitePage) {
    /**
     * FAQ page
     */
    var FaqPage = (function (_super) {
        __extends(FaqPage, _super);
        function FaqPage(config) {
            var _this = this;
            _super.call(this, config);
            KnockoutExt.initSourceData();
            KnockoutExt.initToggle();
            _.bindAll(this, 'questionLike', 'questionDislike', 'categoryClick');
            this.categories = ko.observableArray([]);
            this.currentCategory = ko.observable(0);
            HashStateControls.addControl('category', this.currentCategory);
            this.currentCategory.subscribe(function (id) {
                Analytics.trackEvent('faq', 'category', 'view');
            });
            this.question = {
                category: ko.observable(''),
                text: ko.observable(''),
                onSubmit: function (obj, ev) {
                    var category = _this.question.category();
                    var data = {
                        jx: 'addquestion',
                        category_id: category ? category.id : null,
                        text: _this.question.text()
                    };
                    Analytics.trackEvent('faq', 'question', 'send');
                    $.post('', data, function (response) {
                        var data = Utils.getJSONbyText(response);
                    }).fail(function (data) {
                        Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
                    });
                }
            };
            $(document).ready(function () {
                ko.applyBindings(_this);
                HashStateControls.hashChange();
            });
        }
        FaqPage.prototype.categoryClick = function (obj, ev) {
            var currentId = this.currentCategory();
            this.currentCategory(currentId == obj.id ? null : obj.id);
        };
        FaqPage.prototype.questionLike = function (obj, ev) {
            Analytics.trackEvent('faq', 'question', 'like');
            this.vote(obj, ev, { id: obj.id, type: 'like' });
        };
        FaqPage.prototype.questionDislike = function (obj, ev) {
            Analytics.trackEvent('faq', 'question', 'dislike');
            this.vote(obj, ev, { id: obj.id, type: 'dislike' });
        };
        FaqPage.prototype.vote = function (obj, ev, data) {
            data.jx = 'vote';
            $.post('', data, function (response) {
                var data = Utils.getJSONbyText(response);
                obj.counter_like(data.counter_like);
                obj.counter_dislike(data.counter_dislike);
            }).fail(function (data) {
                Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
            });
        };
        return FaqPage;
    })(SitePage);
    return FaqPage;
});
