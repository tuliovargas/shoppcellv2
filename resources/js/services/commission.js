module.exports = {
	getReport(month, year) {
		return axios
			.get("/reports/commission", {
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
