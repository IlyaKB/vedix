var Admin = (function() {
	
	var self = null;
	
	function Admin(config) {
		
		self = this;
		
		this.config = (typeof config == undefined ? {} : config);
		
		this.sections = ko.observableArray([]);
		
		this.statusTextDict = {};
		this.statusesTextInit();
		this.status = ko.observable(0);
		this.statusText = ko.observable();
		this.jxStatusUpdate('wait');
		
		this.tabSection = null; // Таб выбранной формы для контекстного меню
		
		this.menuInit();
		this.contextMenuInit();
		
		ko.applyBindings(this);
		
		// Открыть форму сразу
		var sctCode = this.getSectionByURI();
		if ( (sctCode) && (typeof this.config.section != 'undefined') ) {
			this.sectionObjectCreate(this.config.section);
		}
	}
	
	/**
	 * Получить код раздела по текущему URL
	 * @returns {string}
	 */
	Admin.prototype.getSectionByURI = function(href) {
		if (! href) href = location.pathname + location.hash;
		var hash = href.match(/#.*$/g); // TODO: учесть URI типа ...#users&group_id=123&user_id=333
		if (hash) {
			return hash[0].replace(/#&/, '');
		} else {
			var parts = href.match(/\/[^\/]+/g);
			if (parts.length == 2) {
				return parts[1].replace(/\//, '');
			} else {
				return null;
			}
		}
	};
	
	/**
	 * Инициализация списка статусных сообщений
	 * @returns {void}
	 */
	Admin.prototype.statusesTextInit = function() {
		this.statusTextDict = {
			authenticate: 'Вход под своей учётной записью...',
			logout: 'Выход из учётной записи...',
			wait: 'Ожидание команд',
			formOpen: 'Открытие формы...',
			entityLoadData: 'Загрузка данных...',
			entityAdd: 'Добавление записи...',
			entitySaveData: 'Сохранение данных...',
			entityDelete: 'Удаление записи...',
			
			mainmenuLoad: 'Загрузка данных...',
			mainmenuAdd: 'Добавление записи...',
			mainmenuSave: 'Сохранение записи...',
			mainmenuDelete: 'Удаление записи...',
			mainmenuUp: 'Изменение порядка следования записи...',
			mainmenuDown: 'Изменение порядка следования записи...',
			mainmenuGetPages: 'Загрузка списка страниц...',
			
			configLoad: 'Загрузка конифга...',
			configSave: 'Сохранение конфига...',
			
			error: 'Команда обработана с ошибкой!',
			errorFormLoading: 'Ошибка при загрузке формы!',
			errorJSLoading: 'Ошибка при загрузке Javascript-файла!',
			errorJSRunning: 'Произошла ошибка при выполнении js-кода формы раздела!',
			errorCSSLoading: 'Ошибка при загрузке CSS-файла!',
		};
	};
	
	/**
	 * Инициализация меню системы
	 * @returns {void}
	 */
	Admin.prototype.menuInit = function() {
		
		$('#admin_menu')
			.jqxMenu({ height: '30px', showTopLevelArrows: true })
			.css('visibility', 'visible')
			.jqxButton({ theme: 'web' });
		
		/*// TODO: Это говно (jqxMenu) иногда не работает как надо - itemclick выполняется, но return false не помогает => страница перегружается
		$('#admin_menu').on('itemclick', function (ev) {
			
			ev.preventDefault();
			
			var a = $(ev.target);
		
			var href = a.attr('href');
			var sctCode = self.getSectionByURI(href);

			if ($('#tabs li[id="tab_'+sctCode+'"]').length) {
				$('#tabs li[id="tab_'+sctCode+'"] a').click();
			} else {
				self.formAjaxOpen(sctCode);
			}
			
			return false;
		});*/
	};
	
	/**
	 * Инициализировать нижнее (табы) контекстное меню
	 * @returns {void}
	 */
	Admin.prototype.contextMenuInit = function() {
		
		var source = [
			{
				id: 'close',
				html: '<img style="vertical-align: top;" src="/admin/img/close.png"/><span style="position: relative; left: 3px;">Закрыть</span>'
			}/*,
			{
				id: 'test',
				html: "<img src='../../images/folder.png'/><span style='position: relative; left: 3px; top: -2px;'>Test</span>",
				items: [
					{id: 'test1', html: "<img src='../../images/folder.png'/><span style='position: relative; left: 3px; top: -2px;'>Test 1</span>"}
				]
			}*/
		];
		var contextMenu = $("#jqxMenuTabs").jqxMenu({
			source: source,
			width: '120px',
			height: '30px',
			autoOpenPopup: false,
			mode: 'popup'
		}).on('itemclick', function (ev) {
			switch (ev.args.id) {
				case 'close': {
					self.formClose(self.tabSection);
					break;
				}
			}
		});
		$('#tabs').on('mousedown', function (ev) {
			var el = $(ev.target);
			if (el.prop('tagName') == 'UL') return true;
			self.tabSection = $(ev.target).closest('li');
			if (! self.tabSection.data('section')) return false;
			var rightClick = 0;
			if (! ev) var ev = window.event;
			if (ev.which) rightClick = (ev.which == 3); else if (ev.button) rightClick = (ev.button == 2);
			if (rightClick) {
				var scrollTop = $(window).scrollTop();
				var scrollLeft = $(window).scrollLeft();
				contextMenu.jqxMenu('open', parseInt(ev.clientX) + scrollLeft, parseInt(ev.clientY) + scrollTop - 30);
				return false;
			}
		}).on('contextmenu', function(ev) {
			if ($(ev.target).prop('tagName') == 'UL') return true;
			return false;
		});
	};
	
	/**
	 * Закрыть форму
	 * @param {jQuery Element} elTab
	 * @returns {void}
	 */
	Admin.prototype.formClose = function(elTab) {

		if (elTab.hasClass('active')) {
			if (elTab.prev().length) {
				$('a', elTab.prev()).click();
			} else {
				$('a', elTab.next()).click();
			}
		}

		var sctCode = elTab.attr('data-section');

		this.sections.remove( function(section) {
			return (section.code == sctCode);
		});
	};
	
	/**
	 * Открыть главную форму раздела
	 * @param {string} sctCode Код раздела
	 * @returns {void}
	 */
	Admin.prototype.formAjaxOpen = function(sctCode) {
		
		this.ajax(
			'formOpen',
			{},
			null, function(response) {
				self.resourcesFormAjaxLoad(response.section, response.cssFile, response.jsFile).then( function(data) {
					self.sectionObjectCreate(response);
				});
			},
			null, '/admin/' + sctCode
		);
	};
	
	/**
	 * Загрузить динамически css/js-файлы формы
	 * @param {string} sctCode
	 * @param {string} cssFile
	 * @param {string} jsFile
	 * @returns {void}
	 */
	Admin.prototype.resourcesFormAjaxLoad = function(sctCode, cssFile, jsFile) {
		var deferCSS = $.Deferred();
		var deferJS = $.Deferred();
		deferCSS.then( function() {
			if (jsFile) {
				$.ajax({
					type: 'GET',
					url: jsFile,
					dataType: 'script'
				}).done(function ( data ) {
					deferJS.resolve(true);
				}).fail( function( data ) {
					self.jxStatusUpdate('errorJSLoading');
				});
			} else {
				deferJS.resolve(true);
			}
		});

		if (cssFile) {
			$.ajax({
				type: 'GET',
				url: cssFile,
				dataType: 'text',
				success: function(data) {
					$('head').append('<style>' + data + '</style>');
					deferCSS.resolve(true);
				},
				error: function(data) {
					self.jxStatusUpdate('errorCSSLoading');
				}
			});
		} else {
			deferCSS.resolve(true);
		}
		return deferJS;
	};
	
	/**
	 * Выполнить при наличии объявления класса: this.sections.push( objSection );
	 * @param {string} sectionInfo Данные формы раздела
	 * @returns {void}
	 */
	Admin.prototype.sectionObjectCreate = function(sectionInfo) {
		//try { // :TODO On
			var sctCode = sectionInfo.section;
			
			var className = sctCode.replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			}) + 'Admin';
			
			var section = {};
			
			var isDetect = false;
			eval('isDetect = (typeof ' + className + ' != \'undefined\');');
			if (isDetect) {
				eval('section = new ' + className + '(this);');
			}
			
			section.code = sctCode;
			section.application = sectionInfo.application;
			section.title = sectionInfo.title;
			
			section.formHtml = {
				attrId: 'form_' + sctCode,
				cssClass: 'form-' + sctCode
			};
			
			section.tabHtml = {
				attrId: 'tab_' + sctCode,
				href: '#form_' + sctCode
			};
			
			if ( (! $('head > script#template_' + sctCode).length) && (sectionInfo.html) ) {
				var match = sectionInfo.html.match(/(<script[\s\S]*?<\/script>)/ig);
				if ( (match) && (match.length) ) {
					for (var i = 0; i < match.length; i++) {
						$('head').append(match[i]);
					}
				}
				sectionInfo.html = sectionInfo.html.replace(/<script[\s\S]*?<\/script>/ig, '');
				$('head').append('<script type="text/html" id="template_' + sctCode + '">' + sectionInfo.html + '</script>');
			}
			
			this.sections.push( section );
			
			_.delay( function() {
				if (typeof section.init != 'undefined') {
					section.init();
				}
				$('#tabs a:last').tab('show');
				
				if (typeof section.resize != 'undefined') {
					$(window).resize( section.resize ).resize();
				}
			}, 100);
		//} catch(e) {
		//	this.jxStatusUpdate('errorJSRunning');
		//	console.log('Error: ' + e.message);
		//}
	};
	
	Admin.prototype.logout = function() {
		this.ajax(
			'logout',
			{},
			null, function(response) {
				location.href = location.href.replace(/#.*/, '');
			},
			null, '/admin/'
		);
		return false;
	};

	/**
	 * Обновить статус (код и сообщение)
	 * @param {string} jx Код команды
	 * @returns {void}
	 */
	Admin.prototype.jxStatusUpdate = function(jx) {
		//if (! jx) jx = 'wait';
		if (! this.statusTextDict.hasOwnProperty(jx)) {
			this.statusText('Ошибка! Передана неизвестная команда jx=' + jx);
		} else {
			this.statusText( this.statusTextDict[jx] );
			if (jx == 'wait') {
				this.status(0);
			} else if (String(jx).match('error')) {
				this.status(-1);
			} else {
				this.status(1);
			}
		}
	};
	
	/**
	 * Отправить ajax-запрос
	 * @param {string} jx Команда
	 * @param {object} params Параметры
	 * @param {function} funcAlways=null callback-функция, вызываемая всегда, в т.ч. при ошибке ajax-запроса
	 * @param {function} funcSuccess=null callback-функция, вызываемая при успешном ответе сервера
	 * @param {function} funcError=null callback-функция, вызываемая при получении ошибки со стороны сервера
	 * @param {string} uri URI
	 * @returns {Boolean}
	 */
	Admin.prototype.ajax = function(jx, params, funcAlways, funcSuccess, funcError, uri) {
		
		if (this.status() > 0) return false;
		
		if (! uri) uri = '/admin/';
		if (! params) params = {};
		if (! funcAlways) funcAlways = function(response){ return true;};
		if (! funcSuccess) funcSuccess = function(response){ return true;};
		if (! funcError) funcError = function(response){ return true;};
		
		params.jx = jx;
		
		this.jxStatusUpdate(jx);
		if (! $('#modal_loading').is(':visible')) {
			$('#modal_loading').modal('show');
		}
		
		var deffered = $.post(
			uri, 
			params
		).then(function(response, status) {
			funcAlways(response);
			self.jxStatusUpdate('wait');
			$('#modal_loading').modal('hide');
			var jsonData = Utils.getJSONbyText(response);
			if (jsonData) {
				funcSuccess(jsonData);
			} else {
				self.jxStatusUpdate('errorFormLoading');
			}
		}, function(response) {
			funcAlways(response);
			self.jxStatusUpdate('errorFormLoading');
			funcError(response);
		});
	};
	
	return Admin;
})();