module.exports = {
	getTotalClients() {
		return axios.get("/clients/count").then(({ data }) => {
			return data.count;
		});
	},
	getRegisteredClientsYesterday() {
		const today = new Date();
		const yesterday = new Date(today);
		yesterday.setDate(yesterday.getDate() - 1);
		let month = yesterday.getMonth() + 1;
		month = month < 10 ? "0" + month : month;
		const formattedDate = [
			yesterday.getFullYear(),
			month,
			yesterday.getDate()
		].join("-");
		return axios.get(`/clients/count/${formattedDate}`).then(({ data }) => {
			return data.count;
		});
	}
};
