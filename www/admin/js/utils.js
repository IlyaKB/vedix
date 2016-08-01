/*function clock() {
	var i = 0;
	return (function() {
		return ++i;
	});
}
var tick = clock(), tock = clock();
console.log(''+tick()+tock());

var n = 5.0;
var size = '';
switch (true) {
	case (n < 3): size = 'tiny'; break;
	case (n < 6): size = 'normal'; break;
	default: size = 'big';
}
console.log(size);


(function(){ var a=b=5;})();
console.log(b); //console.log(typeof(window.b));

var runners = new Array();
for (var i = 0; i < 10; ++i) (function(i){
	runners.push( function(){return i;});
})(i);
console.log(runners[0]());

var runners = new Array();
for (var i = 0; i < 10; ++i) {
	runners.push( function(){return i;});
}
console.log(runners[0]());*/

var uid = 0;

function TUtils() {
	
	this.winErr = null;
	
	var self = this;
	
	this.str_replace = function (search, replace, subject) {
		return subject.split(search).join(replace);
	};
	
	this.urldecode = function ( msg ) { return msg; };

	/**
	 * Преобразовать текстовый ответ сервера в объект (JSON)
	 */
	this.getJSONbyText = function (data, w, h) {
		
		try {
			eval('data = '+data+';');
			if (data.msg) {
				alert(self.urldecode(data.msg).replace(/%0D%0A/g, '\n').replace(/%0A/g, '\n'));
			}
			if (data.err) {
				alert(self.urldecode(data.err).replace(/%0D%0A/g, '\n').replace(/%0A/g, '\n'));
				return null;
			}
			return data;
		} catch (e) {
			var id_winerr = 'id_winerr'+(++uid);
			if (! w) w = 580;
			if (! h) h = 400;
			
			var body = '';
			if (data.match(/<html>/)) {
				data = data.replace(/<html>/, '').replace(/<\/html>/, '').replace(/<title>[^>]*>/, '').replace(/<style>[^>]*>/, '');
				body = '\
<div id="'+id_winerr+'" style="z-index: '+(uid+10000)+'; position: fixed; border: 2px outset #fff; background-color: #f7f7f6; display: none; cursor: pointer; left: 50px; width: '+w+'px" onclick="$(this).remove();">\n\
	'+data+'\n\
</div>';
			} else {
				body = '\
<div id="'+id_winerr+'" style="z-index: '+(uid+10000)+'; position: fixed; border: 2px outset #fff; background-color: #f7f7f6; display: none; left: 50px; width: '+w+'px">\n\
	<table id="'+id_winerr+'_head" cellspacing="0px" width="100%" style="border: 2px ouset #aaa; background-color: #eee">\n\
		<tr style="cursor: move">\n\
			<td width="100%" class="f10b cn" style="padding: 3px">Произошла ошибка!</td>\n\
			<td class="cn" onclick="$(\'#'+id_winerr+'\').remove(); winErr = null;"><input type="button" style="width: 20px" value="X"/></td>\n\
		</tr>\n\
	</table>\n\
	<div style="padding: 7px; height: '+h+'px; overflow: auto">'+data+'</div>\n\
	<div>\n\
		<table>\n\
			<tr>\n\
				<td width="50%"></td>\n\
				<td>\n\
					<div class="cn">\n\
						<input type="button" style="width: 70px; margin-top: 10px; margin-bottom: 10px" value="Закрыть" onclick="$(\'#'+id_winerr+'\').remove(); winErr = null;"/>\n\
					</div>\n\
				</td>\n\
				<td width="50%"></td>\n\
			</tr>\n\
		</table>\n\
	</div>\n\
</div>';
			}
			
			
			self.winErr = $(body);
			self.winErr.appendTo('body');
			if ($.draggable) {
				self.winErr.draggable({handle: '#'+id_winerr+'_head', opacity:0.5});
			}
			var win = $('#'+id_winerr);
			win.css('left', Math.floor($('body').width() / 2 - w / 2) );
			win.css('top', Math.floor(h/4)+'px');
			win.fadeIn(250);
		}
		return 0;
	};
}

var Utils = new TUtils();

(function() {
	var keyDown = false;
	window.key = function() {
		return keyDown;
	};
	var addEvent = function(elem, type, handler) {
		if (elem.addEventListener) {
			elem.addEventListener(type, handler, false);
		} else {
			elem.attachEvent('on' + type, handler);
		}
		//return arguments.callee;
	}
	addEvent(window.document, 'keydown', function(ev) {
		ev = (ev) ? ev : window.event;
		var keyCode = (ev.charCode) ? ev.charCode : ev.keyCode;
		keyDown = keyCode;
		/*if (keyCode == 16)
			keyDown = 'Shift (keyCode=' + keyCode + ')';
		else if (keyCode == 17)
			keyDown = 'Ctrl (keyCode=' + keyCode + ')';
		else
			keyDown = 'Another key (keyCode=' + keyCode + ')';*/
	});
	addEvent(window.document, 'keyup', function() {
		keyDown = false;
	});
})();
//<a href="#" onclick="this.innerHTML=isKeyDown()?('кнопка нажата: '+isKeyDown()):'кнопка не нажата';return false;">кнопка не нажата</a>
