import _ = require('underscore');
import $ = require('jquery');
import ko = require('knockout');
import Utils = require('./../../../_common/js/utils');
import Notifications = require('./../../../_common/js/notifications');

/**
 * Purse class
 */
class Purse {

	public static nominals = [1, 2, 5, 10];
	private monets: any;

	constructor(config: any, one: number, two: number, five: number, ten: number) {

		// Ключ - номинал монеты
		this.monets = [];
		this.monets[1] = ko.observable(one);
		this.monets[2] = ko.observable(two);
		this.monets[5] = ko.observable(five);
		this.monets[10] = ko.observable(ten);
	}

	/**
	 * Получить кол-во монет одного номинала (остаток)
	 * @param nominal
	 * @returns {number}
	 */
	public get(nominal): number {
		return this.monets[nominal]();
	}

	/**
	 * Отдать монету
	 * @param nominal
	 * @returns {boolean}
	 */
	public remove(nominal: number): boolean {
		var m = this.monets[nominal];
		if (! m) {
			alert('Ошибка E001!');
			return false;
		}
		if (m() == 0) {
			alert('У вас нет монет этого номинала!');
			return false;
		}
		m(m() - 1);
		return true;
	}

	/**
	 * Вернуть/получить монеты
	 * @param monets Номинал одной монеты или массив номиналов с кол-ом
	 * @returns {boolean}
	 */
	public add(monets: any): boolean {
		if ((typeof monets == 'number') || (typeof monets == 'string')) {
			monets = parseInt(monets);
			var _m = this.monets[monets];
			if (! _m) {
				alert('Ошибка E002!');
				return false;
			}
			_m( _m() + 1 );
			return true;
		} else if (typeof monets == 'object') {
			_.each(monets, (val: number, key)=>{
				for (var i = 1; i <= +val; i++) {
					this.add(key);
				}
			});
		}
	}
}

export = Purse;