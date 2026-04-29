const { default: axios } = require("axios");

function getMonths() {
	return [
		{
			name: "Janeiro",
			initials: "Jan"
		},
		{
			name: "Fevereiro",
			initials: "Fev"
		},
		{
			name: "Março",
			initials: "Mar"
		},
		{
			name: "Abril",
			initials: "Abr"
		},
		{
			name: "Maio",
			initials: "Maio"
		},
		{
			name: "Junho",
			initials: "Jun"
		},
		{
			name: "Julho",
			initials: "Jul"
		},
		{
			name: "Agosto",
			initials: "Ago"
		},
		{
			name: "Setembro",
			initials: "Set"
		},
		{
			name: "Outubro",
			initials: "Out"
		},
		{
			name: "Novembro",
			initials: "Nov"
		},
		{
			name: "Dezembro",
			initials: "Dez"
		}
	];
}
function getMonthFromNumber(month) {
	const months = getMonths();
	return months[month - 1];
}
module.exports = {
	getMonths,
	getMonthFromNumber,
	getYearPeriod() {
		return axios
			.get("/reports/period")
			.then(({ data }) => {
				return Promise.resolve(data);
			})
			.catch(error => {
				return Promise.reject(error);
			});
	},
	getDaysInMonth(month, year) {
		var date = new Date(year, month, 1);
		var days = [];
		while (date.getMonth() === month) {
			days.push(new Date(date).getDate());
			date.setDate(date.getDate() + 1);
		}
		return days;
	}
};
