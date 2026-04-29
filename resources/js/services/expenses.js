const { default: axios } = require("axios");

module.exports = {
	getReport(month, year) {
		return axios
			.get("/reports/expenses", {
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
	},
	getExpenseTypes(paginate = false) {
		return axios
			.get("/expense-types", {
				params: {
					paginate: paginate
				}
			})
			.then(({ data }) => {
				return Promise.resolve(data);
			})
			.catch(error => {
				return Promise.reject(error);
			});
	}
};
