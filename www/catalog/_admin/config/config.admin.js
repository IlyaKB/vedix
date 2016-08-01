var ConfigAdmin = (function() {
	
	var self = null;
	var admin = null;
	
	function ConfigAdmin(admin) {
		
		self = this;
		self.admin = admin;
		
		this.saved = ko.observable(true);
		this.configBody = ko.observable('');
		this.configBody.subscribe( function() {
			self.saved(false);
		});
	}
	
	ConfigAdmin.prototype.init = function() {
		self.load();
		
		$(window).resize( function() {
			$('#ini_file_body').height( $('body').height() - $('header').height() - $('footer').height() - 90);
		}).resize();
	};
	
	ConfigAdmin.prototype.load = function() {
		self.admin.ajax('configLoad', {}, null, function(data) {
			self.configBody(data.body);
			self.saved(true);
		}, null, '/admin/config' );
	};
	
	ConfigAdmin.prototype.save = function() {
		var body = $('#ini_file_body').val();
		self.admin.ajax('configSave', {
			body: body
		}, null, function(data) {
			self.saved(true);
		}, null, '/admin/config' );
	};
	
	return ConfigAdmin;
})();