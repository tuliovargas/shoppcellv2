module.exports = {
	getPaymentTypes(paginate = false) {
		return axios
			.get("/payment-methods", {
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
