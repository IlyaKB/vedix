/// <reference path="../core/libs/_def/jquery.d.ts"/>

import $ = require('jquery');
import User = require('./_common/js/user');

/**
 * Page base class 
 */
class BasePage {

  public user: User;
    
	constructor(config: any = {}) {

		this.user = new User(config);

		$(document).ready(()=> {
			//...
		});
	}
}

export = BasePage;