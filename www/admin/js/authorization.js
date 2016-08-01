var AuthorizationForm = (function() {
	
	var self = null;
	
	function AuthorizationForm(config) {
		
		self = this;
	}
	
	AuthorizationForm.prototype.login = function(token) {
		var login = $('#inputEmail').val();
		var pw = $('#inputPassword').val();
		if ( (! login) || (! pw) ) {
			$('#messages p').html('Введите логин и пароль!');
			$('#messages').fadeIn();
			return false;
		}
		$('#messages').hide();
		$.post(
			location.href,
			{
				jx: 'authenticate',
				user_login: login,
				user_password: pw
			}
		).then(function(response, status) {
			var data = Utils.getJSONbyText(response);
			if (data.success) {
				location.href = location.href.replace(/#/, '');
			} else {
				$('#messages p').html(data.error);
				$('#messages').fadeIn();
			}
		}, function(response) {
			alert('Ошибка!');
		});
	};
	
	AuthorizationForm.prototype.ulogin = function(token) {
		$('#messages').hide();
		$.post(
			location.href,
			{
				jx: 'authenticate',
				token: token
			}
		).then(function(response, status) {
			var data = Utils.getJSONbyText(response);
			if (data.success) {
				location.href = location.href.replace(/#/, '');
			} else {
				$('#messages p').html(data.error);
				$('#messages').fadeIn();
			}
		}, function(response) {
			alert('Ошибка!');
		});
	};
	
	return AuthorizationForm;
})();

var form = new AuthorizationForm();

function loginEmail(token) {
	form.login();
}

function uloginCallback(token) {
	form.ulogin(token);
}