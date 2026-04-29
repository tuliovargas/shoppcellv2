module.exports = {
	getBrand(id) {
		return axios
			.get("/brands", {
				params: {
					id: id
				}
			})
			.then(response => {
				if (response.data.length >= 0) {
					return response.data[0];
				}
				return null;
			})
			.catch(error => {
				return Promise.reject(error);
			});
	},
	getBrandModel(id) {
		return axios
			.get("/brand-models", {
				params: {
					id: id
				}
			})
			.then(response => {
				if (response.data.data.length >= 0) {
					return response.data.data[0];
				}
				return null;
			})
			.catch(error => {
				return Promise.reject(error);
			});
	}
};
