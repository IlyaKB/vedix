var PagesAdmin = (function() {
	
	var self = null;
	var admin = null;
	
	function PagesAdmin(admin) {
		
		self = this;
		this.admin = admin;
	}
	
	// Инициализация элементов на странице
	PagesAdmin.prototype.init = function() {
		
		this.grid = new Grid( { section: 'pages' } );
		
		$('#customWindow #caption').keyup( function(ev) {
			$('#customWindow #url_name').val( self.convertUrlName( $('#customWindow #caption').val() ));
		});
		
		$('#toolbarpages_grid').css('visibility', 'visible'); // fix bug
		var table = $('div#pages_cnt div#form_edit table.tbl-fields');
		$('td', table).css('padding', '5px');
		$('label.checkbox input', table).css('margin-top', '3px');
		
		/*jQuery.browser = {
			mozilla: /mozilla/.test(navigator.userAgent.toLowerCase()) && !/webkit/.test(navigator.userAgent.toLowerCase()),
			webkit: /webkit/.test(navigator.userAgent.toLowerCase()),
			opera: /opera/.test(navigator.userAgent.toLowerCase()),
			msie: /msie/.test(navigator.userAgent.toLowerCase())
		}*/
		//$('#body').elrte({lang: 'ru', styleWithCSS: false, height: 373, toolbar: 'maxi', fmAllow: true });
		/*$('#body_wysiwyg').elrte({
			cssClass : 'el-rte',
			cssfiles : ['/core/libs/elrte-1.3/css/elrte-inner.css'],
			lang: 'ru',
			styleWithCSS: false,
			height: 400,
			toolbar: 'maxi',
			fmAllow: true,
			fmOpen : function(callback) {
				if (typeof dialog === 'undefined') {
					dialog = $('#elfinder').dialogelfinder({
						url: '/core/libs/elfinder-2.0/php/connector.php',
						lang: 'ru',
						customData: {
							 // Здесь данные, передающиеся в коннектор например 
							x : 'пример передачи данных в коннектор с помощью elfinder 2.0'
						},
						 commandsOptions: {
							getfile: {
								oncomplete : 'close'
							}
						},
						getFileCallback: callback
					});
				} else {
					dialog.dialogelfinder('open');
				}
			 }
		});*/
	};
	
	PagesAdmin.prototype.resize = function() {
		//return; // TODO: DELETE
		$('#pages_grid').jqxGrid({ height: $('body').height() - 60 - 56 - 2 });
		$('#pages_grid').jqxGrid({ width: $('body').width() - 2 });
	};
	
	PagesAdmin.prototype.convertUrlName = function(f) {
		
		var rus = ['А','а','Б','б','В','в','Г','г','Д','д','Е','е','Ё','ё','Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м','Н','н','О','о','П','п','Р','р','С','с','Т','т','У','у','Ф','ф','Х','х','Ц','ц','Ч','ч','Ш','ш','Щ','щ','Ъ','ъ','Ы','ы','Ь','ь','Э','э','Ю','ю','Я','я'];
		var eng = ['A','a','B','b','V','v','G','g','D','d','E','e','E','e','Zh','zh','Z','z','I','i','Y','y','K','k','L','l','M','m','N','n','O','o','P','p','R','r','S','s','T','t','U','u','F','f','H','h','C','c','Ch','ch','Sh','sh','Sh','sh','','','I','i','','','E','e','Yu','yu','Ya','ya'];
		for (var i = 0; i < rus.length; i++) {
			var re = new RegExp(rus[i], 'g');
			f = f.replace(re, eng[i]);
		}
		f = f.replace(/[^\w]/ig, '_');
		f = f.replace(/(__)+/ig, '_');
		f = f.replace(/^_+|_+$/ig, '');
		return f.toLowerCase();
	}
	
	return PagesAdmin;
})();