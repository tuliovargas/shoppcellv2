<template>
	<div class="col-12" v-if="userCanSeeChart">
		<hr />
		<div class="row">
			<div class="col-12 col-md-8 mx-auto">
				<div class="card">
					<div class="card-body">
						<h4>Relatório</h4>
						<div class="row my-4">
							<div class="col-12 col-md-3">
								<select
									class="form-control"
									@change="this.renderGraphic"
									v-model="selectedYear"
								>
									<option :value="y" :key="y" v-for="y in years">
										{{ y }}
									</option>
								</select>
							</div>
							<div class="col-12 col-md-3">
								<select
									class="form-control"
									@change="this.renderGraphic"
									v-model="selectedMonth"
								>
									<option :value="m.index" :key="m.index" v-for="m in months">
										{{ m.name }}
									</option>
								</select>
							</div>
						</div>
						<canvas id="report-chart"></canvas>
					</div>
					<div v-if="showGraphic">
						<apexchart
							width="100%"
							height="400"
							type="heatmap"
							:options="chartOptions"
							:series="series"
						></apexchart>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import {
	getYearPeriod,
	getDaysInMonth,
	getMonthFromNumber,
} from "../../services/time";
import { getReport as getCashierReport } from "../../services/cashier";
import { getReport as getExpensesReport } from "../../services/expenses";
import { getReport as getCommissionReport } from "../../services/commission";
import { getReport as getCostReport } from "../../services/product";
import moment from "moment";
const Chart = require("chart.js");
export default {
	components: {},
	data() {
		return {
			cashierReport: {},
			expenseReport: {},
			commissionReport: {},
			showGraphic: false,
			cashierHistory: [],
			years: [],
			selectedYear: new Date().getFullYear(),
			selectedMonth: new Date().getUTCMonth(),
			graphic: null,
			currentYear: new Date().getFullYear(),
			currentMonth: new Date().getUTCMonth(),
			userCanSeeChart: true,
			series: [
				{
					name: "Seg",
					data: [
						{
							x: "00",
							y: 0,
						},
						{
							x: "01",
							y: 0,
						},
						{
							x: "02",
							y: 0,
						},
						{
							x: "03",
							y: 0,
						},
						{
							x: "04",
							y: 0,
						},
						{
							x: "05",
							y: 0,
						},
						{
							x: "06",
							y: 0,
						},
						{
							x: "07",
							y: 0,
						},
						{
							x: "08",
							y: 0,
						},
						{
							x: "09",
							y: 0,
						},
						{
							x: "10",
							y: 0,
						},
						{
							x: "11",
							y: 0,
						},
						{
							x: "12",
							y: 0,
						},
						{
							x: "13",
							y: 0,
						},
						{
							x: "14",
							y: 0,
						},
						{
							x: "15",
							y: 0,
						},
						{
							x: "16",
							y: 0,
						},
						{
							x: "17",
							y: 0,
						},
						{
							x: "18",
							y: 0,
						},
						{
							x: "19",
							y: 0,
						},
						{
							x: "20",
							y: 0,
						},
						{
							x: "21",
							y: 0,
						},
						{
							x: "22",
							y: 0,
						},
						{
							x: "23",
							y: 0,
						},
					],
				},
				{
					name: "Ter",
					data: [
						{
							x: "00",
							y: 0,
						},
						{
							x: "01",
							y: 0,
						},
						{
							x: "02",
							y: 0,
						},
						{
							x: "03",
							y: 0,
						},
						{
							x: "04",
							y: 0,
						},
						{
							x: "05",
							y: 0,
						},
						{
							x: "06",
							y: 0,
						},
						{
							x: "07",
							y: 0,
						},
						{
							x: "08",
							y: 0,
						},
						{
							x: "09",
							y: 0,
						},
						{
							x: "10",
							y: 0,
						},
						{
							x: "11",
							y: 0,
						},
						{
							x: "12",
							y: 0,
						},
						{
							x: "13",
							y: 0,
						},
						{
							x: "14",
							y: 0,
						},
						{
							x: "15",
							y: 0,
						},
						{
							x: "16",
							y: 0,
						},
						{
							x: "17",
							y: 0,
						},
						{
							x: "18",
							y: 0,
						},
						{
							x: "19",
							y: 0,
						},
						{
							x: "20",
							y: 0,
						},
						{
							x: "21",
							y: 0,
						},
						{
							x: "22",
							y: 0,
						},
						{
							x: "23",
							y: 0,
						},
					],
				},
				{
					name: "Qua",
					data: [
						{
							x: "00",
							y: 0,
						},
						{
							x: "01",
							y: 0,
						},
						{
							x: "02",
							y: 0,
						},
						{
							x: "03",
							y: 0,
						},
						{
							x: "04",
							y: 0,
						},
						{
							x: "05",
							y: 0,
						},
						{
							x: "06",
							y: 0,
						},
						{
							x: "07",
							y: 0,
						},
						{
							x: "08",
							y: 0,
						},
						{
							x: "09",
							y: 0,
						},
						{
							x: "10",
							y: 0,
						},
						{
							x: "11",
							y: 0,
						},
						{
							x: "12",
							y: 0,
						},
						{
							x: "13",
							y: 0,
						},
						{
							x: "14",
							y: 0,
						},
						{
							x: "15",
							y: 0,
						},
						{
							x: "16",
							y: 0,
						},
						{
							x: "17",
							y: 0,
						},
						{
							x: "18",
							y: 0,
						},
						{
							x: "19",
							y: 0,
						},
						{
							x: "20",
							y: 0,
						},
						{
							x: "21",
							y: 0,
						},
						{
							x: "22",
							y: 0,
						},
						{
							x: "23",
							y: 0,
						},
					],
				},
				{
					name: "Qui",
					data: [
						{
							x: "00",
							y: 0,
						},
						{
							x: "01",
							y: 0,
						},
						{
							x: "02",
							y: 0,
						},
						{
							x: "03",
							y: 0,
						},
						{
							x: "04",
							y: 0,
						},
						{
							x: "05",
							y: 0,
						},
						{
							x: "06",
							y: 0,
						},
						{
							x: "07",
							y: 0,
						},
						{
							x: "08",
							y: 0,
						},
						{
							x: "09",
							y: 0,
						},
						{
							x: "10",
							y: 0,
						},
						{
							x: "11",
							y: 0,
						},
						{
							x: "12",
							y: 0,
						},
						{
							x: "13",
							y: 0,
						},
						{
							x: "14",
							y: 0,
						},
						{
							x: "15",
							y: 0,
						},
						{
							x: "16",
							y: 0,
						},
						{
							x: "17",
							y: 0,
						},
						{
							x: "18",
							y: 0,
						},
						{
							x: "19",
							y: 0,
						},
						{
							x: "20",
							y: 0,
						},
						{
							x: "21",
							y: 0,
						},
						{
							x: "22",
							y: 0,
						},
						{
							x: "23",
							y: 0,
						},
					],
				},
				{
					name: "Sex",
					data: [
						{
							x: "00",
							y: 0,
						},
						{
							x: "01",
							y: 0,
						},
						{
							x: "02",
							y: 0,
						},
						{
							x: "03",
							y: 0,
						},
						{
							x: "04",
							y: 0,
						},
						{
							x: "05",
							y: 0,
						},
						{
							x: "06",
							y: 0,
						},
						{
							x: "07",
							y: 0,
						},
						{
							x: "08",
							y: 0,
						},
						{
							x: "09",
							y: 0,
						},
						{
							x: "10",
							y: 0,
						},
						{
							x: "11",
							y: 0,
						},
						{
							x: "12",
							y: 0,
						},
						{
							x: "13",
							y: 0,
						},
						{
							x: "14",
							y: 0,
						},
						{
							x: "15",
							y: 0,
						},
						{
							x: "16",
							y: 0,
						},
						{
							x: "17",
							y: 0,
						},
						{
							x: "18",
							y: 0,
						},
						{
							x: "19",
							y: 0,
						},
						{
							x: "20",
							y: 0,
						},
						{
							x: "21",
							y: 0,
						},
						{
							x: "22",
							y: 0,
						},
						{
							x: "23",
							y: 0,
						},
					],
				},
				{
					name: "Sáb",
					data: [
						{
							x: "00",
							y: 0,
						},
						{
							x: "01",
							y: 0,
						},
						{
							x: "02",
							y: 0,
						},
						{
							x: "03",
							y: 0,
						},
						{
							x: "04",
							y: 0,
						},
						{
							x: "05",
							y: 0,
						},
						{
							x: "06",
							y: 0,
						},
						{
							x: "07",
							y: 0,
						},
						{
							x: "08",
							y: 0,
						},
						{
							x: "09",
							y: 0,
						},
						{
							x: "10",
							y: 0,
						},
						{
							x: "11",
							y: 0,
						},
						{
							x: "12",
							y: 0,
						},
						{
							x: "13",
							y: 0,
						},
						{
							x: "14",
							y: 0,
						},
						{
							x: "15",
							y: 0,
						},
						{
							x: "16",
							y: 0,
						},
						{
							x: "17",
							y: 0,
						},
						{
							x: "18",
							y: 0,
						},
						{
							x: "19",
							y: 0,
						},
						{
							x: "20",
							y: 0,
						},
						{
							x: "21",
							y: 0,
						},
						{
							x: "22",
							y: 0,
						},
						{
							x: "23",
							y: 0,
						},
					],
				},
				{
					name: "Dom",
					data: [
						{
							x: "00",
							y: 0,
						},
						{
							x: "01",
							y: 0,
						},
						{
							x: "02",
							y: 0,
						},
						{
							x: "03",
							y: 0,
						},
						{
							x: "04",
							y: 0,
						},
						{
							x: "05",
							y: 0,
						},
						{
							x: "06",
							y: 0,
						},
						{
							x: "07",
							y: 0,
						},
						{
							x: "08",
							y: 0,
						},
						{
							x: "09",
							y: 0,
						},
						{
							x: "10",
							y: 0,
						},
						{
							x: "11",
							y: 0,
						},
						{
							x: "12",
							y: 0,
						},
						{
							x: "13",
							y: 0,
						},
						{
							x: "14",
							y: 0,
						},
						{
							x: "15",
							y: 0,
						},
						{
							x: "16",
							y: 0,
						},
						{
							x: "17",
							y: 0,
						},
						{
							x: "18",
							y: 0,
						},
						{
							x: "19",
							y: 0,
						},
						{
							x: "20",
							y: 0,
						},
						{
							x: "21",
							y: 0,
						},
						{
							x: "22",
							y: 0,
						},
						{
							x: "23",
							y: 0,
						},
					],
				},
			],
			chartOptions: {
				chart: {
					height: 350,
					type: "heatmap",
				},
				dataLabels: {
					enabled: false,
				},
				colors: ["#008FFB"],
				title: {
					text: "Frequência de vendas",
				},
			},
		};
	},
	computed: {
		months() {
			let months = [];
			for (let month = 0; month < 12; month++) {
				months.push({
					name: getMonthFromNumber(month + 1).name,
					index: month,
				});
				if (
					this.selectedYear == this.currentYear &&
					this.currentMonth == month
				) {
					break;
				}
			}
			return months;
		},
		labels() {
			let days = getDaysInMonth(this.selectedMonth, this.selectedYear);
			if (
				this.selectedMonth == this.currentMonth &&
				this.selectedYear == this.currentYear
			) {
				/*days = days.filter((day) => {
          return day <= new Date().getDate();
        });*/
			}
			return days;
		},
	},
	methods: {
		getMonthFromNumber,
		async getCashierInfo() {
			let url = "/cashierInfo";
			const config = {
				params: {
					"with-closed": true,
				},
			};

			config.params.all = true;
			config.params.date_ini = moment(
				`${this.selectedMonth + 1}/01/${this.selectedYear}`
			).format();
			config.params.date_fim = moment(
				`${this.selectedMonth + 1}/31/${this.selectedYear}`
			).format();

			try {
				let { data } = await axios.get(url, config);
				this.cashierHistory = data;
				this.series.forEach((serie) => {
					serie.data.map((data) => {
						if (data) data.y = 0;
					});
				});
				this.handleCreateGraphic();
			} catch (error) {
				console.error(error);
			}
		},
		handleCreateGraphic() {
			this.cashierHistory.map((history) => {
				if (history) {
					history.orders.forEach((order) => {
						let hour = moment(new Date(order.created_at)).format("HH");
						let weekDay = moment(new Date(order.created_at)).isoWeekday() - 1;

						if (this.series[weekDay].data[Number(hour)]) {
							this.series[weekDay].data[Number(hour)].y++;
							console.log(this.series[weekDay].data[Number(hour)]);
						}
					});
				}
			});

			this.showGraphic = true;
		},
		async getReport() {
			this.cashierReport = await getCashierReport(
				this.selectedMonth + 1,
				this.selectedYear
			);
			this.expenseReport = await getExpensesReport(
				this.selectedMonth + 1,
				this.selectedYear
			);
			this.commissionReport = await getCommissionReport(
				this.selectedMonth + 1,
				this.selectedYear
			);
			this.costReport = await getCostReport(
				this.selectedMonth + 1,
				this.selectedYear
			);
		},
		async fillYears() {
			const period = await getYearPeriod();
			for (let year = period.min; year <= period.max; year++) {
				this.years.push(year);
			}
		},
		async setUserCanSee() {
			axios
				.get("/user/has_role/admin")
				.then(({ data }) => {
					this.userCanSeeChart = data;

					if (this.userCanSeeChart) {
						this.fillYears();
						this.renderGraphic();
					}
				})
				.catch((error) => {
					//
				});
		},
		async renderGraphic() {
			this.showGraphic = false;
			await this.getReport();
			let ctx1 = $("#report-chart");
			if (!this.graphic) {
				this.graphic = new Chart(ctx1, {
					type: "bar",
					options: {
						scales: {
							yAxes: [
								{
									stacked: true,
								},
							],
							xAxes: [
								{
									stacked: true,
								},
							],
						},
						tooltips: {
							callbacks: {
								label: function (tooltipItem, data) {
									let label =
										data.datasets[tooltipItem.datasetIndex].label || "";

									if (label) {
										label += ": ";
									}
									label += new Intl.NumberFormat("pt-BR", {
										style: "currency",
										currency: "BRL",
									}).format(tooltipItem.yLabel);

									return label;
								},
							},
						},
					},
				});
			}
			this.graphic.data = {
				labels: this.labels,
				datasets: [
					{
						label: "Lucro",
						data: this.cashierReport.totals,
						backgroundColor: "#4caf508c",
						borderWidth: 1,
					},
					{
						label: "Gastos",
						data: this.expenseReport.totals,
						backgroundColor: "#ff572296",
						borderWidth: 1,
					},
					{
						label: "Custo",
						data: this.costReport.totals,
						backgroundColor: "burlywood",
						borderWidth: 1,
					},
					{
						label: "Comissão",
						borderWidth: 1,
						data: this.commissionReport.totals,
						backgroundColor: "#ffeb3b73",
					},
				],
			};
			this.graphic.update();
			await this.getCashierInfo();
		},
	},
	async mounted() {
		this.setUserCanSee();
	},
};
</script>
