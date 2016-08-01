/// <reference path="../../../../core/libs/_def/underscore.d.ts"/>
/// <reference path="../../../../core/libs/_def/knockout.d.ts"/>
/// <reference path="../../../../core/libs/_def/jquery.d.ts"/>
define(["require", "exports", 'underscore', 'knockout', './../../../_common/js/utils', './../../../_common/js/notifications'], function (require, exports, _, ko, Utils, Notifications) {
    var Voting = (function () {
        function Voting(config) {
            var _this = this;
            this.config = config;
            _.bindAll(this, 'imgMouseOver', 'imgMouseOut', 'clickVote');
            var votes = this.parseVotes(config.detail ? config.detail.votes : 0);
            this.votes = ko.observableArray(votes);
            this.votesCount = ko.observable(config.detail ? config.detail.votesCount : 0);
            this.votesCountPrint = ko.computed(function () {
                return _this.votesCount() < 10 ? 'менее 10' : String(_this.votesCount());
            });
            this.hoverVote = ko.observable(-1);
        }
        Voting.prototype.imgMouseOver = function (obj, ev) {
            this.hoverVote(obj.index);
        };
        Voting.prototype.imgMouseOut = function (obj, ev) {
            this.hoverVote(-1);
        };
        Voting.prototype.clickVote = function (obj, ev) {
            var _this = this;
            $.post('', {
                jx: 'voting',
                entity_type: this.config.entity.type,
                entity_id: this.config.entity.id,
                mark: obj.index + 1
            }, function (response) {
                var data = Utils.getJSONbyText(response);
                if (data) {
                    _this.votesCount(data.votes_count);
                    _this.votes(_this.parseVotes(data.votes));
                }
            }).fail(function (data) {
                Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
            });
        };
        Voting.prototype.parseVotes = function (votes) {
            var i = 0;
            return _.map(votes, function (vote) {
                return { mark: vote, index: i++ };
            });
        };
        return Voting;
    })();
    return Voting;
});
