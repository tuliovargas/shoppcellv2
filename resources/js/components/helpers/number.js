module.exports = {
	formatReal(price) {
		if(price == 'NaN'){
			return "R$ 0,00";
		}
		const formatter = new Intl.NumberFormat("pt-BR", {
			style: "currency",
			currency: "BRL"
		});
		return formatter.format(price);
	}
};
