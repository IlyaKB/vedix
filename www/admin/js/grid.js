var Grid = (function() {
	
	var self = null;
	
	function Grid(config) {
		
		self = this;
		
		//return; // TODO: DELETE
		
		this.config = config;
		
		this.code = 'grid1';
		this.action = '';
		this.currentId = 0;
		
		this.tmrRightClick = null;
		
		this.source = {
			datatype: 'json',
			url: '/admin/' + this.config.section + '?jx=loadData',
			root: 'Rows',
			beforeprocessing: function (data) {
				self.source.totalrecords = data[0].TotalRows;
			},
			sort: function () {
				$('#pages_grid').jqxGrid('updatebounddata'); // update the grid and send a request to the server.
			},
			datafields: [
				{ name: 'id', type: 'number' },
				{ name: 'caption', type: 'string' },
				{ name: 'url_name', type: 'string' },
				{ name: 'docdate', type: 'date' },
				{ name: 'upddate', type: 'date' },
				{ name: 'hits', type: 'number' }
			],
			id: 'id',
			addrow: function (rowid, rowdata, position, commit) {
				admin.ajax('entityAdd', rowdata, function() {
					$('#customWindow').jqxWindow('close');
				}, function(response) {
					rowdata.id = response.id;
					rowdata.docdate = response.docdate;
					rowdata.upddate = response.upddate;
					rowdata.hits = response.hits;
					rowdata.caption = rowdata.caption = '<a href="/pages/' + rowdata.url_name + '" target="_blank">' + rowdata.caption + '</a>';
					commit(true);//, rowdata.id);
					self.grid.jqxGrid('updatebounddata'); // TODO: оптимизировать - надо добавлять без полной загрузки datasource
					//var rows = self.grid.jqxGrid('getboundrows');
					//var rowindex = self.grid.jqxGrid('getrowboundindexbyid', rowdata.id);
					//self.grid.jqxGrid('ensurerowvisible', rowindex);
				}, function() {
					commit(false);
				}, '/admin/pages');
			},
			updaterow: function (rowid, rowdata, commit) {
				admin.ajax('entitySaveData', rowdata, function() {
					$('#customWindow').jqxWindow('close');
				}, function(response) {
					//self.grid.jqxGrid('ensurerowvisible', rowindex);
					rowdata.docdate = response.docdate;
					rowdata.upddate = response.upddate;
					rowdata.hits = response.hits;
					rowdata.caption = rowdata.caption = '<a href="/pages/' + rowdata.url_name + '" target="_blank">' + rowdata.caption + '</a>';
					commit(true);
					self.grid.jqxGrid('updatebounddata'); // TODO: оптимизировать - надо редактировать без полной загрузки datasource
				}, function() {
					commit(false);
				}, '/admin/pages');
			},
			deleterow: function (rowid, commit) {
				
				if (! confirm('Вы точно хотите удалить эту запись')) return commit(false);
				
				admin.ajax('entityDelete', { id: rowid }, null, function(response) {
					commit(true);
					self.grid.jqxGrid('updatebounddata'); // TODO: оптимизировать - надо удалять без полной загрузки datasource
				}, function() {
					commit(false);
				}, '/admin/pages');
				
			},
		};
		
		this.dataadapter = new $.jqx.dataAdapter(this.source);
		
		this.grid = $('#pages_grid').jqxGrid({
			source: this.dataadapter,
			sortable: true,
			//filterable: true,
			//showfiltermenuitems: true,
			columnsresize: true,
			pageable: true,
			pagermode: 'simple',
			pagesize: 20,
			virtualmode: true,
			showsortcolumnbackground: false,
			showsortmenuitems: true,
			selectionmode: 'checkbox', //selectionmode: 'multiplerowsextended',
			localization: this.localization(),
			showtoolbar: true,
			rendertoolbar: this.renderToolBar,
			rendergridrows: function () {
				_.each(self.dataadapter.records, function(it) {
					it.caption = '<a href="/pages/' + it.url_name + '" target="_blank">' + it.caption + '</a>';
				});
				return self.dataadapter.records;
			},
			theme: 'web',
			columns: [
				{ text: '', datafield: '', width: 0, pinned: true, sortable: false, maxwidth: 0 },
				{ text: 'ИД', datafield: 'id', width: 50, minwidth: 30 },
				{ text: 'Название', datafield: 'caption', width: 400, minwidth: 100 },
				{ text: 'Добавлена', datafield: 'docdate', width: 85, minwidth: 30, cellsformat: 'dd.MM.yyyy' },
				{ text: 'Обновлена', datafield: 'upddate', width: 125, minwidth: 30, cellsformat: 'dd.MM.yyyy HH:mm' },
				{ text: 'Визитов', datafield: 'hits', width: 80, minwidth: 30, cellsalign: 'right' }
			]
		});
		
		this.grid.on('rowdoubleclick', function (ev) { 
			if (self.tmrRightClick) return false;
			self.actionEdit();
		});
		this.grid.on('rowclick', function (ev) {
			var args = ev.args;
			
			function selectOne() {
				var rows = self.grid.jqxGrid('getselectedrowindexes');
				var l = rows.length;
				for (var i = l; i--; i > 0) {
					self.grid.jqxGrid('unselectrow', rows[i]);
				}
				self.grid.jqxGrid('selectrow', args.rowindex);
			}

			var row = self.grid.jqxGrid('getrowdata', args.rowindex);
			self.currentId = row.id;
			
			if (args.rightclick) {
				if (self.tmrRightClick) clearTimeout(self.tmrRightClick);
				self.tmrRightClick = setTimeout( function(){ self.tmrRightClick = null; }, 300);
				selectOne();
				self.grid.jqxGrid('selectrow', args.rowindex);
				var scrollTop = $(window).scrollTop();
				var scrollLeft = $(window).scrollLeft();
				contextMenu.jqxMenu('open', parseInt(args.originalEvent.clientX) + 5 + scrollLeft, parseInt(args.originalEvent.clientY) + 5 + scrollTop);
				return false;
			}
			
			var rows = self.grid.jqxGrid('getselectedrowindexes');
			if (key() == 17) { // Ctrl
				if (rows.indexOf(row) != -1) {
					self.grid.jqxGrid('unselectrow', args.rowindex);
				} else {
					self.grid.jqxGrid('selectrow', args.rowindex);
				}
			} else {
				selectOne();
			}
		});
		
		var source = [
			//{ html: "<img src='/admin/img/rec_show.gif'/><span style='position: relative; left: 5px; top: 1px;'>Просмотр</span>" },
			{ html: '<span id="grid_contextmenu_add" class="grid-context-menu-item"><img src="/admin/img/rec_add.gif"/><span>Добавить</span></span>' },
			{ html: '<span id="grid_contextmenu_edit" class="grid-context-menu-item"><img src="/admin/img/rec_edit.gif"/><span>Изменить</span></span>' },
			//{ html: "<img src='/admin/img/rec_copy.gif'/><span style='position: relative; left: 5px; top: 1px;'>Копировать</span>" },
			{ html: '<span id="grid_contextmenu_delete" class="grid-context-menu-item"><img src="/admin/img/rec_del.gif"/><span>Удалить</span></span>' },
			/*{ html: "&nbsp;" },
			{ html: "<img src='/admin/img/useraction.gif'/><span style='position: relative; left: 5px; top: 1px;'>Другие действия</span>",
				items: [
					{ html: "<img src='/admin/img/useraction.gif'/><span style='position: relative; left: 5px; top: 1px;'>Действие 1</span>" },
					{ html: "<img src='/admin/img/useraction.gif'/><span style='position: relative; left: 5px; top: 1px;'>Действие 2</span>" },
					{ html: "<img src='/admin/img/useraction.gif'/><span style='position: relative; left: 5px; top: 1px;'>Действие 3</span>" }
				]
			}*/
		];
		var contextMenu = $('#jqxGridMenu').jqxMenu({ source: source, width: 130, height: 85, autoOpenPopup: false, mode: 'popup', theme: 'web'});
		
		$('li', contextMenu).each( function(i, e) {
			if ($(e).html() == '&nbsp;') {
				$(e).attr('type', 'separator');
				$(e).removeClass('jqx-item').removeClass('jqx-menu-item-top').addClass('jqx-menu-item-separator').addClass('jqx-menu-item-separator-web');
			}
		});
		
		contextMenu.on('itemclick', function (ev) {
			//var args = ev.args;
			//var rowindex = self.grid.jqxGrid('getselectedrowindex');
			//self.currentId = self.grid.jqxGrid('getrowid', rowindex);
			var span = $(ev.target).closest('span.grid-context-menu-item');
			if (! span.length) {
				span = $('span.grid-context-menu-item', ev.target);
			}
			if (span.length) {
				switch (span.attr('id')) {
					case 'grid_contextmenu_add': self.actionAdd(); break;
					case 'grid_contextmenu_edit': self.actionEdit(); break;
					case 'grid_contextmenu_delete': self.actionDelete(); break;
				}
			}
		});
		this.grid.on('contextmenu', function () { return false; });
		
		$('#saveButton').click( function() {
			
			var body = $('#body_wysiwyg iframe')[0].contentWindow.getHTML();
			
			var rowdata = {
				id: (self.action == 'edit' ? self.currentId : 0),
				caption: $('#customWindow #caption').val(),
				url_name: $('#customWindow #url_name').val(),
				body: body, //$('#customWindow #body').val(),
				status: ( $('#customWindow #status')[0].checked ? 1 : 0)
			};
			
			if (! rowdata.caption) return alert('Не указан заголовок страницы!');
			if (! rowdata.url_name) return alert('Не указан код страницы!');
			
			if (self.action == 'add') {
				self.grid.jqxGrid('addrow', null, rowdata);
			} else {
				self.grid.jqxGrid('updaterow', self.currentId, rowdata); // RowIndex
			}
		});
		
		$(document).keyup(function (ev) {
			if (ev.which == 27) {
				$('#customWindow').jqxWindow('close');
			}
		});
	} // ---------------- End constructor ----------------
	
	
	Grid.prototype.renderToolBar = function(toolbar) {
		
		var div = $('#grid_toolbar').clone().show();
		div.attr('id', 'grid_toolbar_' + self.code);
		$('button#grid_toolbar_add', div).click( self.actionAdd );
		$('button#grid_toolbar_edit', div).click( self.actionEdit );
		$('button#grid_toolbar_delete', div).click( self.actionDelete );
		$('button#grid_toolbar_refresh', div).click( self.actionRefresh );
		div.appendTo(toolbar);
		
		self.initEditingForm();
	};
		
	Grid.prototype.initEditingForm = function() {
		
		$('#customWindow').jqxWindow({
			width: 1200,
			height: 650,
			zIndex: 1000,
			isModal: true,
			modalOpacity: 0.1,
			autoOpen: false,
			resizable: true,
			cancelButton: $('#cancelButton'),
			initContent: function() {
				$('#saveButton').jqxButton({width: '80px'});
				$('#cancelButton').jqxButton({width: '80px'});
			},
			theme: 'web'
		});
		$('#customWindow').on('resizing', function(ev) {
			self.resizing();
		});
		//$('#jqxDockPanel').jqxDockPanel({ width: 1200, height: 350, theme: 'web'}); // ?
		$('#jqxTabs').jqxTabs({ width: '100%', height: '100%', position: 'top', selectionTracker: true, theme: 'web'});
	};
	
	Grid.prototype.resizing = function() {
		var w = $('#customWindow').width() - 2;
		var h = $('#customWindow').height() - 26;
		$('#jqxDockPanel').jqxDockPanel({ width: w + 'px', height: h + 'px'});
	};
	
	Grid.prototype.actionAdd = function() {
		self.action = 'add';
		$('#captureContainer').text('Добавление новой страницы');
		
		$('#customWindow #caption').val('');
		$('#customWindow #url_name').val('');
		$('#customWindow #status')[0].checked = true;
		$('#customWindow #body').val('<p>Строка1...</p>\n<p>Строка2...</p>');
		
		$('#customWindow').jqxWindow('width',  1000);
		$('#customWindow').jqxWindow('open');
		
		$('#customWindow').css('z-index', 1000);
		$('div.jqx-window-modal').css('z-index', 999);
		
		// TODO: какого-то фига грид расширяется за пределы окна вправо!!!
		var w = $('#customWindow').jqxWindow('width');
		_.delay( function() {
			$('#customWindow').jqxWindow('width',  w+10);
			self.resizing();
		}, 100);
		
		_.delay( function() {
			$('#body_wysiwyg iframe')[0].contentWindow.clearHTML();
		}, 500);
	};
	
	Grid.prototype.actionEdit = function(id) {
		
		if (! self.currentId) return alert('Выберите запись!');
		
		self.action = 'edit';
		
		$('#captureContainer').text('Редактирование страницы с ИД=' + self.currentId);
		
		admin.ajax('entityLoadData', { id: self.currentId }, null, function(response) {
			
			$('#customWindow #caption').val( response.caption );
			$('#customWindow #url_name').val( response.url_name );
			$('#customWindow #status')[0].checked = parseInt(response.status);
			//$('#customWindow #body').val( response.body );
			_.delay( function() {
				$('#body_wysiwyg iframe')[0].contentWindow.setHTML( response.body );
			}, 500);
			
			$('#customWindow').jqxWindow('width',  1000);
			$('#customWindow').jqxWindow('open');
			
			$('#customWindow').css('z-index', 1000);
			$('div.jqx-window-modal').css('z-index', 999);
		
			// TODO: какого-то фига грид расширяется за пределы окна вправо!!!
			var w = $('#customWindow').jqxWindow('width');
			_.delay( function() {
				$('#customWindow').jqxWindow('width',  w+10);
				self.resizing();
			}, 100);
		}, null, '/admin/pages');
	};
	
	Grid.prototype.actionDelete = function() {
		self.grid.jqxGrid('deleterow', self.currentId);
	};
	
	Grid.prototype.actionRefresh = function() {
		self.grid.jqxGrid('updatebounddata');
	};
	
	Grid.prototype.localization = function() {
		return {
			pagergotopagestring: 'Перейти на',
			pagershowrowsstring: 'Записей',
			pagerrangestring: ' из ',
			pagernextbuttonstring: 'Следующая',
			pagerpreviousbuttonstring: 'Предыдущая',
			sortascendingstring: 'Сортировать по возрастанию',
			sortdescendingstring: 'Сортировать по убыванию',
			sortremovestring: 'Отменить сортировку',
			emptydatastring: 'Данных нет'
		};
	};
	
	return Grid;
})();