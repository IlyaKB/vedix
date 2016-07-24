define(["require", "exports"], function (require, exports) {
    var Analytics = (function () {
        function Analytics() {
        }
        /*private static __constructor = (()=>{});*/
        Analytics.trackEvent = function (category, action, label, value) {
            if (typeof ga !== 'undefined') {
                ga('send', 'event', category, action, label, value, { nonInteraction: 1 });
            }
            if (typeof yaCounter !== 'undefined') {
                yaCounter.reachGoal(category + '_' + action + '_' + label);
            }
        };
        Analytics.trackEventOnce = function (category, action, label, value) {
            var key = category + ':' + action;
            if (!Analytics.trackedOnce.hasOwnProperty(key)) {
                Analytics.trackEvent(category, action, label, value);
                Analytics.trackedOnce[key] = true;
            }
        };
        Analytics.trackVirtualPageView = function (pagePath) {
            if (typeof ga !== 'undefined') {
                ga('send', 'pageview', pagePath);
            }
        };
        Analytics.trackedOnce = {};
        return Analytics;
    })();
    return Analytics;
});
