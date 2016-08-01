var __extends = this.__extends || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    __.prototype = b.prototype;
    d.prototype = new __();
};
define(["require", "exports", 'jquery', 'knockout', './../../_common/js/knockout_ext', './../../_common/js/utils', './../../_common/js/notifications', './../site', './../_components/voting/voting', './../../_components/comments/comments'], function (require, exports, $, ko, KnockoutExt, Utils, Notifications, SitePage, Voting, Comments) {
    /**
     * Blog page
     */
    var BlogPage = (function (_super) {
        __extends(BlogPage, _super);
        function BlogPage(config) {
            var _this = this;
            _super.call(this, config);
            KnockoutExt.initSourceData();
            if (config.is_list) {
                this.posts = ko.observableArray([]);
            }
            else if (config.is_record) {
                this.voting = new Voting(config);
                this.comments = new Comments(config);
                this.hits = ko.observable(config.detail ? config.detail.hits : 0);
                this.hitsPrint = ko.computed(function () {
                    return _this.hits() < 10 ? 'менее 10' : String(_this.hits());
                });
            }
            $(document).ready(function () {
                ko.applyBindings(_this);
                //HashStateControls.hashChange();
            });
            if ((config.is_record) && (!config.detail.viewed)) {
                $.post('', {
                    jx: 'visit'
                }, function (response) {
                    var data = Utils.getJSONbyText(response);
                    _this.hits(data.hits);
                }, 'text').fail(function (data) {
                    Notifications.show('Error: ajax failed!', Notifications.Types.ntError);
                });
            }
            // TODO: решить на уровне верстки и CSS
            $(window).resize(function () {
                var elLeft = $('div.left-layer');
                var elLeftBlocks = elLeft.find('div.block');
                var h1 = 0;
                elLeftBlocks.each(function (i, e) {
                    h1 += $(e).height();
                });
                var elMiddle = $('div.middle-layer');
                var h2 = Math.max(elMiddle.height() - 5, parseInt(elMiddle.css('min-height')) - 7);
                elLeft.height(Math.max(h1, h2));
            }).resize();
        }
        BlogPage.prototype.showPostsInCategory = function (_obj, ev) {
            function showOrHide(elTR, doShow) {
                while (elTR) {
                    elTR = elTR.next('tr');
                    if (!elTR.hasClass('post'))
                        break;
                    if (doShow) {
                        elTR.show();
                    }
                    else {
                        elTR.hide();
                    }
                }
            }
            var elTR = $(ev.target).closest('tr');
            if (!elTR.data('expand')) {
                var elTable = elTR.closest('table');
                elTable.find('tr.post').hide();
                elTable.find('tr.category').data('expand', '').find('img').attr('src', '/data/site/blog/ft_plus.gif');
                elTR.data('expand', 1);
                elTR.find('img').attr('src', '/data/site/blog/ft_minus.gif');
                showOrHide(elTR, true);
            }
            else {
                elTR.data('expand', '').find('img').attr('src', '/data/site/blog/ft_plus.gif');
                showOrHide(elTR, false);
            }
            return true;
        };
        BlogPage.prototype.categoryMouseOver = function (_obj, ev) {
            var elImg = $(ev.target);
            if (elImg.closest('tr').data('expand')) {
                elImg.attr('src', '/data/site/blog/ft_minus_over.gif');
            }
            else {
                elImg.attr('src', '/data/site/blog/ft_plus_over.gif');
            }
        };
        BlogPage.prototype.categoryMouseOut = function (_obj, ev) {
            var elImg = $(ev.target);
            if (elImg.closest('tr').data('expand')) {
                elImg.attr('src', '/data/site/blog/ft_minus.gif');
            }
            else {
                elImg.attr('src', '/data/site/blog/ft_plus.gif');
            }
        };
        return BlogPage;
    })(SitePage);
    return BlogPage;
});
