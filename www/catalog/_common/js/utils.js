/// <reference path="../../../core/libs/_def/jquery.d.ts"/>
define(["require", "exports", 'jquery', './notifications'], function (require, exports, $, Notifications) {
    /*
    function SomeClass(startValue) {
        var privateCount = startValue;
        function creator() {
            return (++privateCount)+'/'+(++creator.publicCounter);
        };
        creator.publicCounter = startValue;
        return creator;
    }
    console.log(SomeClass(20)() );
    var counter1 = SomeClass(0);
    var counter2 = SomeClass(10);
    console.log( 'counter1()=' + counter1() + ', counter2()=' + counter2() );
    console.log( 'counter1()=' + counter1() + ', counter2()=' + counter2() );
    console.log( 'counter1.privateCount='+counter1.privateCount +', counter2.privateCount='+counter2.privateCount);
    console.log( 'counter1.publicCounter='+counter1.publicCounter +', counter2.publicCounter='+counter2.publicCounter);
    /**/
    /*
    var value = 0;
    function f() {
        value = true;
        if (1) {
    
        } else {
            //value = false;
            var value = false;
        }
        console.log( 'f(), value='+value );
    }
    f();
    console.log('value='+value);
    /**/
    var fullname = 'Вася';
    var obj = {
        fullname: 'Петя',
        prop: {
            fullname: 'Маша',
            getFullname: function () {
                return this.fullname;
            }
        }
    };
    //console.log(obj.prop.getFullname());
    var test = obj.prop.getFullname;
    console.log(test());
    for (var i = 0; i < 10; i++) {
        setTimeout(function () {
            console.log(i);
        }, 100);
    }
    var Utils = (function () {
        function Utils() {
        }
        Utils.str_replace = function (search, replace, subject) {
            return subject.split(search).join(replace);
        };
        /**
         * Преобразовать текстовый ответ сервера в объект (JSON)
         */
        Utils.getJSONbyText = function (_data, w, h) {
            try {
                var data;
                if (typeof _data == "object")
                    data = _data;
                else
                    eval('data = ' + _data + ';');
                if (data.warning) {
                    Notifications.show(data.warning, Notifications.Types.ntWarning);
                }
                if ((data.error) || (data.err)) {
                    Notifications.show(data.error ? data.error : '' + data.err ? data.err : '', Notifications.Types.ntError);
                }
                if (((data.success) && (data.success_showmsg)) || (data.msg)) {
                    Notifications.show(data.success ? data.success : '' + data.msg ? data.msg : '', Notifications.Types.ntSuccess);
                }
                return data;
            }
            catch (e) {
                var id_winerr = 'id_winerr' + (++Utils.uid);
                if (!w)
                    w = 580;
                if (!h)
                    h = 400;
                var body = '';
                if (_data.match(/<html>/)) {
                    _data = _data.replace(/<html>/, '').replace(/<\/html>/, '').replace(/<title>[^>]*>/, '').replace(/<style>[^>]*>/, '');
                    body = '\
<div id="' + id_winerr + '" class="popup-system-error" style="z-index: ' + (Utils.uid + 1000) + '; position: fixed; border: 2px outset #fff; background-color: #f7f7f6; display: none; left: 50px; width: ' + w + 'px">\n\
	' + _data + '\n\
</div>';
                }
                else {
                    body = '\
<div id="' + id_winerr + '" class="popup-system-error" style="z-index: ' + (Utils.uid + 1000) + '; position: fixed; border: 2px outset #fff; background-color: #f7f7f6; display: none; left: 50px; width: ' + w + 'px">\n\
	<table id="' + id_winerr + '_head" cellspacing="0px" width="100%" style="border: 2px ouset #aaa; background-color: #eee">\n\
		<tr style="cursor: move">\n\
			<td width="100%" class="f10b cn" style="padding: 3px">Произошла ошибка!</td>\n\
			<td class="cn" onclick="$(\'#' + id_winerr + '\').remove(); winErr = null;"><input type="button" style="width: 20px" value="X"/></td>\n\
		</tr>\n\
	</table>\n\
	<div style="padding: 7px; height: ' + h + 'px; overflow: auto">' + _data + '</div>\n\
	<div>\n\
		<table>\n\
			<tr>\n\
				<td width="50%"></td>\n\
				<td>\n\
					<div class="cn">\n\
						<input type="button" style="width: 70px; margin-top: 10px; margin-bottom: 10px" value="Закрыть" onclick="$(\'#' + id_winerr + '\').remove(); winErr = null;"/>\n\
					</div>\n\
				</td>\n\
				<td width="50%"></td>\n\
			</tr>\n\
		</table>\n\
	</div>\n\
</div>';
                }
                Utils.winErr = $(body);
                Utils.winErr.appendTo('body'); //.draggable({handle: '#'+id_winerr+'_head', opacity:0.5});
                var win = $('#' + id_winerr);
                win.css('left', Math.floor($('body').width() / 2 - w / 2));
                win.css('top', Math.floor(h / 4) + 'px');
                win.fadeIn(250);
            }
            return 0;
        };
        Utils.winErr = null;
        Utils.uid = 0;
        Utils.__constructor = (function () {
            $(document).on('click', function (ev) {
                if ($(ev.target).closest('.popup-system-error').length)
                    return;
                $('.popup-system-error').remove();
            });
        })();
        return Utils;
    })();
    return Utils;
});
