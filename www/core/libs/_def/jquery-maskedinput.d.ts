/// <reference path="jquery.d.ts"/>

interface JQuery {
  maskedinput: any;
}

interface JQueryStatic {
  maskedinput: any;
}

declare module "jquery-maskedinput" {
  export = $;
}