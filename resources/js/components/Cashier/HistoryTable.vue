<template>
	<div class="w-100 h-100 card-history" ref="historyTable">
		<div class="card">
			<div class="card-title mb-3 d-flex justify-content-between">
				<b
					>STATUS DO CAIXA
					{{
						cashierDetail && cashierDetail.id ? ` (${cashierDetail.id})` : ""
					}}</b
				>
				<div v-if="cashierDetail">
					Data:
					{{ moment(cashierDetail.close_time).format("DD/MM/YYYY - HH:mm") }}
					Usuário:
					{{ userName }}
				</div>
				<div v-if="cashierHistory && !cashierDetail && !cashierIsOpen">
					<flat-pickr
						ref="calendar"
						v-model="dateSelected"
						class="form-control"
						placeholder="Filtrar por período"
						:config="config"
						@input="setRangeDate"
					>
					</flat-pickr>
				</div>
				<div v-if="cashierDetail" class="d-flex">
					<button class="btn" @click="backToList()">
						<i class="fas fa-arrow-left" style="font-size: 20px"></i>
					</button>
					<button class="btn" @click="handlePrintCashier()">
						<i class="fas fa-print" style="font-size: 20px"></i>
					</button>
				</div>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr v-if="cashierIsOpen || cashierDetail">
								<th>Meios de pagamento</th>
								<th>Vendas</th>
								<th>Valor</th>
							</tr>
							<tr v-else>
								<th>ID</th>
								<th>Data do Fechamento</th>
								<th>Usuário</th>
								<th>Troco</th>
								<th>Diferença</th>
							</tr>
						</thead>
						<tbody v-if="cashierIsOpen || cashierDetail">
							<tr v-for="(hist, i) in cashierInfo.earnings" :key="i">
								<td>{{ hist.payment_type }}</td>
								<td>{{ hist.quantity_sales }}</td>
								<td>
									{{
										(parseFloat(hist.total) - parseFloat(hist.total_devolvido))
											| currency
									}}
								</td>
							</tr>

							<tr v-if="expense" class="text-danger text-bold">
								<td class="text-left">{{ expense.name }}</td>
								<td>Quantidade: {{ expense.quantity }}</td>
								<td>{{ (parseFloat(expense.value) * -1) | currency }}</td>
							</tr>
							<tr v-if="cashierDetail">
								<td>Valor depositado</td>
								<td></td>
								<td>{{ cashierDetail.deposit | currency }}</td>
							</tr>
						</tbody>
						<tbody v-else>
							<tr
								v-for="(cashier, i) in cashierHistory"
								:key="i"
								class="history-row"
								@click="handleSelectedCashier(cashier)"
								v-if="cashier.close_time"
							>
								<td>{{ cashier.id }}</td>
								<td v-if="cashier.close_time">
									{{ moment(cashier.close_time).format("DD/MM/YYYY - HH:mm") }}
								</td>
								<td v-else style="color: #a4272e">Caixa Aberto</td>
								<td>
									{{ cashier.user ? cashier.user.name : "Desconhecido" }}
								</td>
								<td>{{ cashier.charge | currency }}</td>
								<td>{{ getDifference(cashier) | currency }}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div v-if="totalHistory" class="history-total">
			<div
				class="card card-body mb-0 mt-3 d-flex justify-content-center align-items-end"
			>
				<div class="card-total d-flex justify-content-between">
					<span>Total</span>
					<span class="value"
						><b>{{ parseFloat(totalHistory) | currency }}</b></span
					>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import moment from "moment";
import { formatReal } from "../helpers/number";
import _ from "lodash";
import toastr from "toastr";
import { Portuguese } from "flatpickr/dist/l10n/pt";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";

export default {
	name: "HistoryTable",

	components: { flatPickr },

	props: {
		cashierIsOpen: {
			type: Boolean,
			default: false,
		},
		loggedUser: {
			type: Object,
			required: true,
		},
		userIsAdmin: {
			required: true,
		},
	},

	data() {
		return {
			moment: moment,
			cashierInfo: [],
			cashierHistory: [],
			cashierDetail: false,
			expense: null,
			totalHistory: null,
			isCashierHistory: false,
			dateSelected: `${moment().format("YYYY-MM-DD 00:00:00")} até ${moment()
				.subtract(7, "d")
				.format("YYYY-MM-DD 00:00:00")}`,
			dateIni: moment().subtract(7, "d").format("YYYY-MM-DD 00:00:00"),
			dateFim: moment().format("YYYY-MM-DD 00:00:00"),
			config: {
				mode: "range",
				wrap: true, // set wrap to true only when using 'input-group'
				altFormat: "d - F - Y",
				altInput: true,
				dateFormat: "Y-m-d H:i:S",
				locale: Portuguese,
				minDate: this.startDate,
				maxDate: this.maxDate,
			},
			callStackUpdateCalendar: 0,
		};
	},

	filters: {
		currency(value) {
			if (!value) return "R$ 0,00";
			return `R$ ${parseFloat(value).toLocaleString("pt-br", {
				minimumFractionDigits: 2,
			})}`;
		},
	},

	async mounted() {
		await this.getCashierInfo();
	},

	computed: {
		userName() {
			return this.cashierDetail && this.cashierDetail.user
				? this.cashierDetail.user.name
				: "Desconhecido";
		},
	},

	methods: {
		formatReal,
		async getCashierInfo() {
			let loader = this.$loading.show({
				container: this.$refs.historyTable,
				canCancel: true,
				onCancel: this.onCancel,
			});
			let url = "/cashierInfo";
			const config = {
				params: {
					"with-closed": true,
				},
			};

			if (!this.cashierIsOpen) config.params.all = true;
			if (!this.cashierIsOpen) config.params.date_ini = this.dateIni;
			if (!this.cashierIsOpen) config.params.date_fim = this.dateFim;

			try {
				let { data } = await axios.get(url, config);
				this.cashierInfo = data;

				if (this.cashierIsOpen) {
					if (data.expenses && data.expenses.length > 0) {
						this.expense = {
							name: `Despesas - ${moment().format("DD/MM/YYYY")}`,
							quantity: data.expenses.length,
							value: data.expenses.reduce(
								(acc, exp) => (acc += parseFloat(exp.value)),
								0
							),
						};

						this.$emit("updateExpenseValue", parseFloat(this.expense.value));
					}
					this.calculateTotal();
				} else {
					this.cashierHistory = data;
				}
			} catch (error) {
				console.error(error);
			}
			loader.hide();
		},
		calculateTotal() {
			const earningsTotal =
				this.cashierInfo.earnings.reduce(
					(acc, hist) =>
						acc + parseFloat(hist.total) - parseFloat(hist.total_devolvido),
					0
				) || 0;

			const expensesTotal =
				this.cashierInfo.expenses.reduce(
					(acc, exp) => acc + parseFloat(exp.value),
					0
				) || 0;

			this.totalHistory = earningsTotal - expensesTotal;
		},
		getDifference(cashier) {
			return Object.entries(cashier.difference).reduce(
				(acc, entrie) => (acc += entrie[1]),
				0
			);
		},
		handleSelectedCashier(cashier) {
			if (cashier.user_id !== this.loggedUser.id && !this.userIsAdmin)
				return toastr.error(
					"Somente permitido visualização de caixas do usuário ativo! Você não tem as permissões necessárias para visualizar esse caixa"
				);
			this.cashierInfo = cashier;
			if (cashier.expenses && cashier.expenses.length > 0) {
				this.expense = {
					name: `Despesas - ${moment().format("DD/MM/YYYY")}`,
					quantity: cashier.expenses.length,
					value: cashier.expenses.reduce(
						(acc, exp) => (acc += parseFloat(exp.value)),
						0
					),
				};
			}
			this.calculateTotal();
			this.cashierDetail = cashier;
		},
		handlePrintCashier() {
			this.$emit("showCashierPrint", this.cashierDetail);
		},
		backToList() {
			this.cashierDetail = null;
			this.totalHistory = null;
		},
		async setRangeDate(value) {
			if (!value) return;
			const dateSplited = value.split(" até ");
			this.callStackUpdateCalendar++;

			if (dateSplited[0]) {
				this.$refs.calendar.config.maxDate = moment(dateSplited[0])
					.add(30, "d")
					.format("YYYY-MM-DD HH:MM:SS");
			}

			if (dateSplited.length == 1) {
				this.dateIni = dateSplited[0];
				this.dateFim = moment(dateSplited[0])
					.add({ hours: 24 })
					.format("YYYY-MM-DD HH:MM:SS");
			} else if (dateSplited.length > 1) {
				this.dateIni = dateSplited[0];
				this.dateFim = dateSplited[1];
			} else {
				this.dateIni = null;
				this.dateFim = null;
			}

			if (this.callStackUpdateCalendar % 2 === 0) await this.getCashierInfo();
		},
	},

	beforeDestroy() {
		this.$emit("totalHistoryTable", null);
	},
};
</script>

<style lang="scss" scoped>
.card-history {
	.card {
		padding: 30px;
	}
}
.card-body {
	min-height: 500px;
}
.table td,
.table th {
	border-top: none;
}

.table-striped tbody tr:nth-of-type(odd) {
	background-color: #fafbfc;
}

.table thead th {
	border-bottom: 1px solid #e4e7ed;
}

.card-total {
	background: #fafbfc;
	border-radius: 0px 0px 4px 4px;
}

.history-total {
	max-height: 120px;

	.card-body {
		height: auto;
		min-height: 115px;
		background: #fafbfc;
		border-radius: 4px;
		padding: 30px;

		.card-total {
			font-size: 1rem;
			background: rgba(241, 244, 246, 0.7);
			border: 1px solid #e4e7ed;
			box-sizing: border-box;
			border-radius: 4px;
			width: 100%;
			max-width: 300px;
			padding: 15px 20px;
			line-height: 18.75px;

			.value {
				color: #0983e8;
			}
		}
	}
}

.history-row {
	user-select: none;
	&:hover {
		background: #defdfd !important;
	}
}
</style>
