/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>
define(["require", "exports", 'underscore', 'jquery', 'knockout', '../../../_common/js/utils', '../../../_common/js/notifications', '../../../_common/js/analytics'], function (require, exports, _, $, ko, Utils, Notifications, Analytics) {
    /**
     * Poll entity class
     */
    var Poll = (function () {
        function Poll(idSelector, config) {
            this.config = config;
            this.max_votes = 1;
            this.items_checked = [];
            this.elPoll = $(idSelector);
            this.poll_id = this.elPoll.data('id');
            this.max_votes = this.elPoll.data('max-votes');
            this.voted = ko.observable(this.elPoll.data('voted') ? true : false);
            this.mode = ko.observable(this.voted() ? 'results' : 'voting');
            this.elPoll.find('.items input').each(function (i, e) {
                var input = $(e);
                var elTR = input.closest('tr');
                if (input.get(0).checked) {
                    elTR.addClass('selected');
                }
            });
        }
        Poll.prototype.itemClick = function (_obj, ev) {
            var elTR = $(ev.target).closest('tr');
            if (elTR.hasClass('selected')) {
                elTR.removeClass('selected');
                elTR.find('input').get(0).checked = false;
            }
            else {
                elTR.addClass('selected');
                elTR.find('input').get(0).checked = true;
            }
        };
        Poll.prototype.voteClick = function (_obj, _ev) {
            var _this = this;
            Analytics.trackEvent(this.config.entity.type, 'poll', 'vote');
            this.items_checked = this.getCheckedItems();
            if (!this.items_checked.length) {
                Notifications.show('Выберите хотя бы один вариант!', Notifications.Types.ntWarning);
                return false;
            }
            $.post('', {
                jx: 'poll',
                poll_id: this.poll_id,
                items_checked: this.items_checked
            }, function (response) {
                var data = Utils.getJSONbyText(response);
                if ((data) && (data.success)) {
                    _this.voted(true);
                    _this.elPoll.find('div.results > div').each(function (i, e) {
                        var elDiv = $(e);
                        var item_id = elDiv.data('id');
                        var item = _.findWhere(data.items, { id: item_id });
                        var elText = elDiv.find('div.text > span.votes');
                        elText.text('(' + item.votes + ')');
                        var elLine = elDiv.find('div.line > div');
                        var w = (item.votes_per ? item.votes_per + '%' : '1px');
                        elLine.css('width', w);
                    });
                    _this.mode('results');
                }
            }).fail(function (data) {
                Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
            });
        };
        Poll.prototype.getCheckedItems = function () {
            var items_checked = [];
            this.elPoll.find('table.items tr').each(function (i, e) {
                var elTR = $(e);
                var input = elTR.find('input');
                if (input.get(0).checked) {
                    items_checked.push(parseInt(elTR.data('id')));
                }
            });
            return items_checked;
        };
        Poll.prototype.votingClick = function (_obj, _ev) {
            Analytics.trackEventOnce(this.config.entity.type, 'poll', 'voting');
            this.mode('voting');
        };
        Poll.prototype.resultsClick = function (_obj, _ev) {
            Analytics.trackEventOnce(this.config.entity.type, 'poll', 'results');
            this.mode('results');
        };
        return Poll;
    })();
    return Poll;
});
