define(["require", "exports"], function (require, exports) {
    var Numbers = (function () {
        function Numbers() {
        }
        /**
         * Format the money value
         */
        Numbers.formatMoney = function (number, precision, dot, delimiter) {
            if (precision === void 0) { precision = 0; }
            if (dot === void 0) { dot = ','; }
            if (delimiter === void 0) { delimiter = '&thinsp;'; }
            if (!number)
                number = 0;
            if (precision != undefined) {
                number = parseFloat(number).toFixed(precision);
            }
            if (!dot)
                dot = ',';
            if (!delimiter)
                delimiter = ' ';
            return String(number).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1' + delimiter).replace('.', dot);
        };
        /**
         * Round the number
         */
        Numbers.roundTo = function (value, precision, floorOrCeil) {
            if (precision === void 0) { precision = 0; }
            if (floorOrCeil === void 0) { floorOrCeil = ''; }
            if (precision == 0)
                return Math.round(value);
            var f = (!floorOrCeil ? Math.round : floorOrCeil == 'floor' ? Math.floor : Math.ceil);
            if (precision < 0) {
                var roundingMultiplier = Math.pow(10, -precision);
                return f(value / roundingMultiplier) * roundingMultiplier;
            }
            else {
                var roundingMultiplier = Math.pow(10, precision);
                return f(value * roundingMultiplier) / roundingMultiplier;
            }
        };
        Numbers.__constructor = (function () {
        })();
        return Numbers;
    })();
    return Numbers;
});
