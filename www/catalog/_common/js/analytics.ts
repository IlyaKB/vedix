
declare var ga: any;
declare var yaCounter: any;

class Analytics {
  
  private static trackedOnce = {};
  
  /*private static __constructor = (()=>{});*/
  
  public static trackEvent(category: string, action: string, label?: string, value?: string): void {
    if (typeof ga !== 'undefined') {
      ga('send', 'event', category, action, label, value, { nonInteraction: 1});
    }
    if (typeof yaCounter !== 'undefined') {
      yaCounter.reachGoal(category + '_' + action + '_' + label);
    }
  }
  
  public static trackEventOnce(category: string, action: string, label?: string, value?: string): void {
    var key: string = category + ':' + action;
    if (! Analytics.trackedOnce.hasOwnProperty(key)) {
      Analytics.trackEvent(category, action, label, value);
      Analytics.trackedOnce[key] = true;
    }
  }
  
  public static trackVirtualPageView(pagePath: string): void {
    if (typeof ga !== 'undefined') {
      ga('send', 'pageview', pagePath);
    }
  }
}
export = Analytics;