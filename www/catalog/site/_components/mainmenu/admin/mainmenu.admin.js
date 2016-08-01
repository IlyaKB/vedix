
var MainmenuAdmin = (function() {
	
	var self = null;
	var admin = null;
	
	function MainmenuAdmin(admin) {
		
		self = this;
		self.admin = admin;
		
		this.items = ko.observable({ childs: ko.observableArray() });
		this.pages = ko.observableArray([]);
		this.pagesCount = ko.computed( function() { return self.pages().length; });
		this.pagesLoaded = ko.observable(false);
		this.currentId = 0;
	}
	
	MainmenuAdmin.prototype.load = function(parentNode) {
		if (! parentNode) return false;
		var _parentNode = parentNode();
		
		self.admin.ajax('mainmenuLoad', { parent_id: _parentNode.id }, null, function(data) {
			self.onLoad(data, parentNode);
		}, null, '/admin/mainmenu' );
	};
	
	MainmenuAdmin.prototype.onLoad = function(data, parentNode) {
		
		function scanChilds(dataChilds, childs) {
			_.each(dataChilds, function(it) {
				var name = ko.observable(it.name);
				var src = ko.observable(it.src);
				var status = ko.observable(it.status);
				var isdemo = ko.observable(it.isdemo);
				name.subscribe(self._itemSave);
				src.subscribe(self._itemSave);
				status.subscribe(self._itemSave);
				isdemo.subscribe(self._itemSave);
				var item = {
					id: it.id,
					number: it.number,
					name: name,
					src: src,
					status: status,
					isdemo: isdemo,
					childs: ko.observableArray([]),
					changed: ko.observable(false)
				};
				scanChilds(it.childs, item.childs);
				childs.push( ko.observable(item) );
			});
		}
		
		if (parentNode().childs().length) {
			parentNode().childs([]);
		}

		scanChilds(data.rootitem.childs, parentNode().childs);
	};
	
	MainmenuAdmin.prototype.init = function() {
		
		//$('#jqxSplitter').jqxSplitter();
		
		self.load(self.items);
		
		$('#mainmenu_tree').on('click', function(ev) {
			var el = $(ev.target);
			if (! el.closest('.mainmenu-tree').length) return false;
			
			var li = el.closest('li');
			var id = (li.length ? li.data('id') : 0)
			
			if (el.attr('disabled') == 'disabled') return false;
			
			if (el.hasClass('button-selectpage')) self.itemSelectPage(id);
			if (el.hasClass('button-add')) self.itemAdd(id);
			if (el.hasClass('button-up')) self.itemUp(id);
			if (el.hasClass('button-down')) self.itemDown(id);
			if (el.hasClass('button-delete')) self.itemDelete(id);
			if (el.hasClass('button-save')) self.itemSave(id);
		});
		
		$(window).resize( function() {
			$('#mainmenu_cnt').height( $('body').height() - $('header').height() - $('footer').height() - 23);
		}).resize();
	};
	
	MainmenuAdmin.prototype.itemSelectPage = function(id) {
		self.currentId = id;
		//if (! self.pagesLoaded()) {
			self.admin.ajax('mainmenuGetPages', {}, null, function(data) {
				if ( (data) && (data.pages) ) {
					self.pages([]);
					_.each(data.pages, function(it) {
						it.checked = ko.observable(false);
						self.pages.push(it);
					});
				}
				self.pagesLoaded(true);
			}, null, '/admin/mainmenu' );
		//} else {
		//	_.each(self.pages(), function(it) { it.checked(false); });
		//}
	};
	
	MainmenuAdmin.prototype.selectPage = function(obj, ev) {
		_.each(self.pages(), function(it) {
			if (it.id != obj.id) it.checked(false);
		});
		return true;
	};
	
	MainmenuAdmin.prototype.setLink = function(obj, ev) {
		
		var isDetect = false;
		_.each(self.pages(), function(it) {
			if (! it.checked()) return;
			isDetect = true;
			var item = self.getItem(self.currentId);
			if (item) {
				var _item = item();
				_item.src(it.href);
				_item.name(it.caption);
			}
		});
		if (! isDetect) {
			alert('Выберите страницу');
			return false;
		} else {
			$('#pages_list_modal').modal('hide');
		}
	};
	
	MainmenuAdmin.prototype.itemAdd = function(parent_id) {
		var item = self.getItem(parent_id);
		self.admin.ajax('mainmenuAdd', { parent_id: parent_id }, null, function(data) {
			var name = ko.observable(data.name);
			var src = ko.observable(data.src);
			var status = ko.observable(true);
			var isdemo = ko.observable(false);
			name.subscribe(self._itemSave);
			src.subscribe(self._itemSave);
			status.subscribe(self._itemSave);
			isdemo.subscribe(self._itemSave);
			var obj = ko.observable({
				id: data.id, number: data.number, name: name, src: src, status: status, isdemo: isdemo, childs: ko.observableArray([]), changed: ko.observable(false)
			});
			item().childs.push(obj);
		}, null, '/admin/mainmenu' );
	};
	
	MainmenuAdmin.prototype._itemSave = function(v) {
		var input = $(window.event.target);
		var li = input.closest('li');
		var div = li.closest('#mainmenu_tree');
		var id = (div.length ? li.data('id') : self.currentId);
		var item = self.getItem(id);
		if (item) {
			var _item = item();
			_item.changed(true);	
		}
	};
	
	MainmenuAdmin.prototype.itemSave = function(id) {
		var item = self.getItem(id);
		var _item = item();
		self.admin.ajax('mainmenuSave', {
			id: id,
			name: _item.name(),
			src: _item.src(),
			status: _item.status(),
			isdemo: _item.isdemo()
		}, null, function(data) {
			_item.changed(false);
		}, null, '/admin/mainmenu' );
	};
	
	MainmenuAdmin.prototype.itemDelete = function(id) {
		
		if (! confirm('Вы точно хотите удалить запись?') ) return false;
		
		var item = self.getItem(id);
		var _item = item();

		if (_item) {
			if (_item.childs().length) {
				alert('Удалите сначала все вложенные записи!');
				return false;
			}
			self.admin.ajax('mainmenuDelete', {
				id: id
			}, null, function(data) {
				_item.parent().childs.remove(function(it){
					return (it().id == id ? true : false);
				});
			}, null, '/admin/mainmenu' );
		}
	};
	
	MainmenuAdmin.prototype.itemUp = function(id) {
		var item = self.getItem(id);
		if (! item) return false;
		var _item = item();
		self.admin.ajax('mainmenuUp', {
			id: id
		}, null, function(data) {
			if (data) {
				_.delay(function(){
					self.load(_item.parent);
				},500);
			}
		}, null, '/admin/mainmenu' );
	};
	
	MainmenuAdmin.prototype.itemDown = function(id) {
		var item = self.getItem(id);
		if (! item) return false;
		var _item = item();
		self.admin.ajax('mainmenuDown', {
			id: id
		}, null, function(data) {
			if (data) {
				_.delay(function(){
					self.load(_item.parent);
				},500);
			}
		}, null, '/admin/mainmenu' );
	};
	
	MainmenuAdmin.prototype.getItem = function(id, items) {
		if (_.isUndefined(id)) return null;
		if (! items) items = self.items;
		if (! id) return items; // root node
		var _items = items();
		for (var i = 0; i < _items.childs().length; i++) {
			var it = _items.childs()[i];
			var _it = it();
			if (_it.id == id) {
				_it.parent = items;
				_it.index = i;
				return it;
			} else {
				var result = self.getItem(id, it);
				if (result) return result;
			}
		}
		return null;
	};
	
	return MainmenuAdmin;
})();