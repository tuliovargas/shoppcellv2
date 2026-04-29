const { default: axios } = require("axios");

module.exports = {
	getLowStock() {
		return axios({
			url: "/products",
			params: {
				lowStock: true
			},
			method: "GET"
		})
			.then(({ data }) => {
				return Promise.resolve(data);
			})
			.catch(error => {
				return Promise.reject(error);
			});
	},
	getReport(month, year) {
		return axios
			.get("/reports/cost", {
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
