class Numbers {
	
	private static __constructor = (()=>{})();

	/**
	 * Format the money value
	 */
	public static formatMoney(number, precision = 0, dot = ',', delimiter = '&thinsp;') {

		if(!number) number = 0;

		if(precision != undefined) {
			number = parseFloat(number).toFixed(precision);
		}
		if(!dot)
			dot = ',';

		if(!delimiter)
			delimiter = ' ';

		return String(number).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1' + delimiter).replace('.', dot);
	}

	/**
	 * Round the number
	 */
	public static roundTo(value: number, precision: number = 0, floorOrCeil: string = '') {
		if (precision == 0) return Math.round(value);
		var f = (! floorOrCeil ? Math.round : floorOrCeil == 'floor' ? Math.floor : Math.ceil);
		if (precision < 0) {
			var roundingMultiplier = Math.pow(10, -precision);
			return f(value / roundingMultiplier) * roundingMultiplier;
		} else {
			var roundingMultiplier = Math.pow(10, precision);
			return f(value * roundingMultiplier) / roundingMultiplier;
		}
	}
}

export = Numbers;