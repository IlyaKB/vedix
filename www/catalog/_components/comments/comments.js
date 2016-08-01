/// <reference path="../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../core/libs/_def/knockout.d.ts"/>
/// <reference path="../../../core/libs/_def/moment.d.ts"/>
define(["require", "exports", 'underscore', 'jquery', 'knockout', 'moment', 'moment-ru', './../../_common/js/knockout_ext', './../../_common/js/utils', './../../_common/js/user', './../../_common/js/notifications', './../../_common/js/analytics'], function (require, exports, _, $, ko, moment, momentru, KnockoutExt, Utils, User, Notifications, Analytics) {
    /**
     * Comment entity class
     */
    var Comments = (function () {
        function Comments(config) {
            var _this = this;
            this.config = config;
            momentru;
            moment.locale('ru');
            _.bindAll(this, 'onSubmit', 'votingMouseOver', 'votingMouseOut', 'vote');
            this.items = ko.observableArray([]);
            // Преобразование даты и времени
            this.itemsCallback = function () {
                _.each(_this.items(), function (item) {
                    var dt = moment(item.creation_date());
                    item.creation_date(dt.fromNow());
                    _.each(item.replies(), function (reply) {
                        var dt = moment(reply.creation_date());
                        reply.creation_date(dt.fromNow());
                    });
                });
            };
            KnockoutExt.initSourceData();
            this.authorName = ko.observable(User.id() ? User.fullname() : '');
            this.email = ko.observable('');
            this.text = ko.observable('');
            this.errorText = ko.observable('');
            this.authorNameError = ko.observable(false);
            this.emailError = ko.observable(false);
            this.textError = ko.observable(false);
        }
        Comments.prototype.onSubmit = function (obj, ev) {
            var _this = this;
            this.errorText('');
            this.authorNameError(false);
            this.emailError(false);
            this.textError(false);
            var fields = [];
            if (!this.authorName()) {
                this.authorNameError(true);
                fields.push('Имя');
            }
            if ((!User.id()) && (!this.email())) {
                this.emailError(true);
                fields.push('Email');
            }
            if (!this.text()) {
                this.textError(true);
                fields.push('Текст комментария');
            }
            if (fields.length) {
                this.errorText('Заполните поля (' + fields.join(', ') + ')');
                return false;
            }
            Analytics.trackEvent(this.config.entity.type, 'comments', 'send');
            $.post('', {
                jx: 'comments',
                action: 'add',
                //entity_type: this.config.entity.type,
                entity_id: this.config.entity.id,
                author_name: this.authorName(),
                email: this.email(),
                text: this.text()
            }, function (response) {
                var data = Utils.getJSONbyText(response);
                if (data) {
                    _this.text('');
                    _.each(_this.items(), function (item) {
                        var index = item.index();
                        item.index(++index);
                    });
                    var _comment = data.comment;
                    var dt = moment(_comment.creation_date);
                    var comment = {
                        author_id: _comment.author_id,
                        author_name: _comment.author_name,
                        photo: _comment.photo,
                        creation_date: dt.fromNow(),
                        text: _comment.text,
                        counter_like: _comment.counter_like,
                        counter_dislike: _comment.counter_dislike,
                        replies_visible: ko.observable(false),
                        replies: ko.observableArray([]),
                        index: ko.observable(1)
                    };
                    _this.items.splice(0, 0, comment);
                }
            }, 'text');
        };
        Comments.prototype.showReplyForm = function (obj, ev) {
            alert('TODO: showReplyForm(...)');
        };
        Comments.prototype.votingMouseOver = function (obj, ev) {
            var el = $(ev.target).closest('.voting');
            //if (! el.hasClass('voting')) return true;
            el.addClass('hover');
            var src = el.find('img').attr('src');
            el.find('img').attr('src', src.replace(/\.png/, '_hover.png'));
        };
        Comments.prototype.votingMouseOut = function (obj, ev) {
            var el = $(ev.target).closest('.voting');
            //if (! el.hasClass('voting')) return true;
            el.removeClass('hover');
            var src = el.find('img').attr('src');
            el.find('img').attr('src', src.replace(/_hover\.png/, '.png'));
        };
        Comments.prototype.vote = function (obj, ev) {
            Analytics.trackEvent(this.config.entity.type, 'comments', 'vote');
            var el = $(ev.target).closest('.voting');
            if (!el.length)
                return console.error('Comments.vote(..): error!');
            var data = { jx: 'comments', action: 'vote', id: obj.id, type: el.hasClass('js-like') ? 'like' : 'dislike' };
            $.post('', data, function (response) {
                var data = Utils.getJSONbyText(response);
                obj.counter_like(data.counter_like);
                obj.counter_dislike(data.counter_dislike);
            }).fail(function (data) {
                Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
            });
        };
        return Comments;
    })();
    return Comments;
});
