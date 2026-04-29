const { default: axios } = require("axios");

module.exports = {
	getCurrentUser() {
		return axios
			.get("/user/current")
			.then(({ data }) => {
				return Promise.resolve(data);
			})
			.catch(error => {
				return Promise.reject(error);
			});
	},
	getTechnicians(withAdmin = false) {
		return axios
			.get("/users", {
				params: {
					type: "vue",
					paginate: false,
					onlyTechnicians: true,
					withAdmin: withAdmin
				}
			})
			.then(({ data }) => {
				return Promise.resolve(data);
			})
			.catch(error => {
				return Promise.reject(error);
			});
	},
	getUsers() {
		return axios
			.get("/users", {
				params: {
					type: "vue",
					paginate: false
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
