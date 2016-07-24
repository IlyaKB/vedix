import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');

import SitePage = require('./../site');

class IndexPage extends SitePage {
    
	constructor(config: any) {
		
		super(config);
		
		var self = this;

		ko.applyBindings(this);
	}
}

export = IndexPage;