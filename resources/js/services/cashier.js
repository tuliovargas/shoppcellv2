const { default: axios } = require("axios");

module.exports = {
	getCashierInfo(all = false) {
		let params = {
			'with-closed': true
		};
		if(all){
			params.all = true;
		}
		return axios.get("/cashierInfo", {
			params: params
		}).then(({ data }) => {
			return data;
		});
	},
	getReport(month, year) {
		return axios
			.get("/reports/sales", {
				params: {
					annualReport: true,
					type: "vue",
					month: month,
					year: year
				}
			})
			.then(({ data }) => {
				return data;
			});
	}
};
