module.exports = {
	getTotalMaintenance(status = ['maintenance']) {
		return axios
			.get(`/maintenances?paginate=false`, {
				params: {
					query_status: status
				}
			})
			.then(({ data }) => {
				return data;
			});
	},
	getOverdueMaintenances() {
		return axios.get(`/maintenances/overdue`).then(({ data }) => {
			return data.total;
		});
	}
};
