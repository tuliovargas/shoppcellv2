const { default: axios } = require("axios");

module.exports = {
	getSuppliers(paginate = false) {
		return axios
			.get("/suppliers", {
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
	},
	getById(id) {
		return axios
			.get("/suppliers/" + id)
			.then(({ data }) => {
				return Promise.resolve(data);
			})
			.catch(error => {
				return Promise.reject(error);
			});
	}
};
