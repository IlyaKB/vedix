define(["require", "exports", 'underscore', 'knockout'], function (require, exports, _, ko) {
    /**
     * Purse class
     */
    var Purse = (function () {
        function Purse(config, one, two, five, ten) {
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
        Purse.prototype.get = function (nominal) {
            return this.monets[nominal]();
        };
        /**
         * Отдать монету
         * @param nominal
         * @returns {boolean}
         */
        Purse.prototype.remove = function (nominal) {
            var m = this.monets[nominal];
            if (!m) {
                alert('Ошибка E001!');
                return false;
            }
            if (m() == 0) {
                alert('У вас нет монет этого номинала!');
                return false;
            }
            m(m() - 1);
            return true;
        };
        /**
         * Вернуть/получить монеты
         * @param monets Номинал одной монеты или массив номиналов с кол-ом
         * @returns {boolean}
         */
        Purse.prototype.add = function (monets) {
            var _this = this;
            if ((typeof monets == 'number') || (typeof monets == 'string')) {
                monets = parseInt(monets);
                var _m = this.monets[monets];
                if (!_m) {
                    alert('Ошибка E002!');
                    return false;
                }
                _m(_m() + 1);
                return true;
            }
            else if (typeof monets == 'object') {
                _.each(monets, function (val, key) {
                    for (var i = 1; i <= +val; i++) {
                        _this.add(key);
                    }
                });
            }
        };
        Purse.nominals = [1, 2, 5, 10];
        return Purse;
    })();
    return Purse;
});
