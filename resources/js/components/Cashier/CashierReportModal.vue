<template>
	<!--Modal listagem de orçamentos-->
	<div
		class="modal moda-cashier-report"
		id="cashierReportModal"
		tabindex="-1"
		role="dialog"
	>
		<form class="modal-dialog modal-xl" role="document">
			<div class="modal-content" id="cashier-report">
				<div class="modal-header">
					<div class="modal-title font-weight-bold">
						<h5 class="mb-0">
							Relatório de Vendas do <b>Caixa {{ cashierData.id }}</b> Usuário:
							<b
								>{{ cashierData.user ? cashierData.user.name : "" }}({{
									cashierData.user_id
								}})</b
							>
						</h5>
					</div>
					<div class="d-flex">
						<label>
							<input v-model="merged" type="checkbox" />
							<small
								>Merge todos os caixas do dia
								{{ moment(cashierData.created_at).format("DD/MM/YYYY") }}</small
							>
						</label>
					</div>
					<div class="d-flex">
						<p class="mb-0">
							Fechado em
							{{ moment(cashierData.close_time).format("DD/MM/YYYY - HH:mm") }}
						</p>
						<button
							class="btn mx-2"
							title="Imprimir Relatório"
							@click.prevent="printReport()"
						>
							<i class="fas fa-print" style="font-size: 20px"></i>
						</button>
						<button
							type="button"
							class="close"
							data-dismiss="modal"
							aria-label="Close"
						>
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
				</div>
				<div class="modal-body">
					<div class="table-responsive" v-if="paymentsArray.length > 0">
						<table class="table table-hover table-budget">
							<thead>
								<tr>
									<th scope="col">ID</th>
									<th scope="col">Caixa</th>
									<th scope="col">Horário</th>
									<th scope="col">Cliente</th>
									<th scope="col">Vendedor</th>
									<th scope="col">Valor Venda</th>
									<th scope="col">Valor PAGO</th>
									<th scope="col">Troco</th>
								</tr>
							</thead>
							<tbody>
								<template v-for="(item, index) in paymentsArray">
									<tr :key="'tr-header' + index" class="row-header">
										<td
											colspan="8"
											class="text-center text-bold text-blue text-lg"
										>
											{{ item[0] }}
										</td>
									</tr>
									<tr
										v-for="(order, index) in item[1].orders"
										:key="'order-' + order.id"
										:class="{
											'bg-row-grey': index % 2 === 0,
											'text-danger':
												order.status === 'returned' ||
												order.status === 'partially_returned',
										}"
										:style="
											order.color
												? 'background-color:' + order.color
												: 'background-color: transparent'
										"
									>
										<td>{{ order.id }}</td>
										<td>{{ order.user.name }}</td>
										<td>
											{{ moment(order.created_at).format("HH:mm") }}
										</td>
										<td>{{ getClientName(order) }}</td>
										<td>
											{{ order.seller ? order.seller.name : "-" }}
											{{
												order.status === "returned" ||
												order.status === "partially_returned"
													? "(Cancelado)"
													: ""
											}}
										</td>
										<td>
											{{ order.total | currency }}
										</td>
										<td>
											{{
												getTotalOrdersByPayment(order, item[1].id) | currency
											}}
											<span v-if="item[0] === 'Cartão crédito'">
												({{
													getTaxInstallment(order.id) === true
														? `${order.taxIn}x`
														: `${getTaxInstallment(order.id)}x`
												}})
											</span>
										</td>
										<td>
											{{
												getChargeByPayment(order, item[1].id)
													| currency(
														getChargeByPayment(order, item[1].id),
														typeof getChargeByPayment(order, item[1].id) !==
															"number"
													)
											}}
										</td>
									</tr>
								</template>
								<tr
									v-if="cashierData.expenses && cashierData.expenses.length > 0"
								>
									<td
										colspan="6"
										class="text-center text-bold text-blue text-lg"
										style="border: 2px solid coral !important"
									>
										Despesas
									</td>
								</tr>
								<tr
									v-for="(expense, index) in cashierData.expenses"
									:key="'exp-' + expense.id"
									:class="{ 'bg-row-grey': index % 2 === 0 }"
								>
									<td>{{ expense.id }}</td>
									<td>
										{{ moment(expense.created_at).format("HH:mm") }}
									</td>
									<td>
										{{ getSuppliterName(expense) }}
									</td>
									<td>
										{{ expense.name }}
									</td>
									<td>{{ expense.value | currency }}</td>
								</tr>
							</tbody>
						</table>
					</div>
					<p v-else class="alert alert-danger">
						Nenhum registro para essa abertura de caixa!
					</p>
					<div class="cashier-details row mt-5">
						<div class="col-md-6">
							<h6 class="text-bold">Observações do Caixa</h6>
							<div
								class="w-100"
								style="min-height: 75px; border: 1px solid gray"
							>
								Observações ao abrir: <br />
								{{ cashierData.observation_open }}
							</div>
							<div
								class="w-100"
								style="min-height: 75px; border: 1px solid gray"
							>
								Observações ao fechar: <br />
								{{ cashierData.observation_close }}
							</div>
							<div v-if="totalDifference > 0">
								<h6 class="text-bold mt-1">Diferenças</h6>
								<div
									class="d-flex justify-content-between totals"
									v-for="(item, i) in listDifferences"
									:key="'diff-' + i"
								>
									<b>{{ item.name }}</b>
									<span>{{ item.value | currency }}</span>
								</div>
							</div>
						</div>
						<div class="col-md-6 d-flex flex-column justify-content-end">
							<div
								class="d-flex justify-content-between totals"
								v-for="(item, i) in totalsByCategory"
								:key="'totals-' + i"
							>
								<b>TOTAL Vendas - {{ item.type }}</b>
								<span>{{ item.total | currency }}</span>
							</div>
							<div class="d-flex justify-content-between totals text-danger">
								<b>DESPESAS (Dinheiro + Cheque)</b>
								<span>{{ (expensesPaidValue * -1) | currency }}</span>
							</div>
							<div class="d-flex justify-content-between totals text-danger">
								<b>DESPESAS (Restante)</b>
								<span>{{ (expensesLeftValue * -1) | currency }}</span>
							</div>
							<div class="d-flex justify-content-between totals">
								<b>TROCO (Abertura Caixa)</b>
								<span>{{ previousCashierCharge | currency }}</span>
							</div>
							<div class="d-flex justify-content-between totals text-danger">
								<b>TROCO (Fechamento Caixa)</b>
								<span>{{ cashierData.charge | currency }}</span>
							</div>
							<div class="d-flex justify-content-between totals">
								<b>Diferença</b>
								<span>{{ totalDifference | currency }}</span>
							</div>
							<div class="d-flex justify-content-between totals">
								<b>Depósito</b>
								<span>{{ depositValue | currency }}</span>
							</div>
							<div class="d-flex justify-content-between totals">
								<b>TOTAL Geral bruto</b>
								<span>{{ (totalCashier - expensesValue) | currency }}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</template>

<script>
import _ from "lodash";
import moment from "moment";

export default {
	props: {
		cashierData: {
			type: Object,
			default: () => {},
		},
		cashiers: {
			type: Array,
			default: [],
		},
	},

	filters: {
		currency(value, isEmpty) {
			if (!isEmpty && !value) return "--";
			if (!value) return "R$ 0,00";
			return `R$ ${parseFloat(value).toLocaleString("pt-br", {
				minimumFractionDigits: 2,
			})}`;
		},
	},

	data() {
		return {
			moment: moment,
			// payments: orders
			differenceLabels: {
				1: "Boleto",
				2: "Cartão de crédito",
				3: "Cartão de débito",
				4: "Cheque",
				5: "Dinheiro",
				6: "PIX",
			},
			merged: false,
		};
	},
	watch: {
		merged(val) {
			if (val) {
				this.getCashierInfo();
			} else {
				this.restore();
			}
		},
		"cashierData.id"(val) {
			if (val !== this.cashierData.id) {
				this.merged = false;
			}
		},
		cashierData: {
			handler(val) {
				this.$forceUpdate();
			},
			deep: true,
		},
	},
	computed: {
		payments() {
			console.log(this.cashierData);

			return !_.isEmpty(this.cashierData) ? this.cashierData.payments : [];
		},
		paymentsArray() {
			return Object.entries(this.payments);
		},
		totalsByCategory() {
			// total_devolvido
			return this.paymentsArray.map((el) => {
				const returnedValue = this.cashierData.earnings.find(
					(earn) => earn.id === el[1].id
				);
				let total = 0;
				if (el[1].total) total += el[1].total;
				if (el[1].total_devolvido) total -= el[1].total_devolvido;
				return {
					type: el[0],
					total:
						returnedValue && returnedValue.total_devolvido
							? el[1].total - returnedValue.total_devolvido
							: el[1].total,
				};
			});
		},
		totalCashier() {
			return this.totalsByCategory.reduce(
				(acc, item) => (acc += item.total),
				0
			);
		},
		difference() {
			return this.cashierData.difference;
		},
		totalDifference() {
			return this.difference
				? Object.entries(this.difference).reduce(
						(acc, el) => (acc += parseFloat(el[1])),
						0
				  )
				: 0;
		},
		listDifferences() {
			return Object.entries(this.difference)
				.filter((el) => parseFloat(el[1]) > 0)
				.map((el) => ({ name: this.differenceLabels[el[0]], value: el[1] }));
		},
		expensesPaidValue() {
			if (this.cashierData.expenses && this.cashierData.expenses.length > 0) {
				return this.cashierData.expenses
					.filter((exp) => [3, 5].includes(exp.payment_method_id))
					.reduce((acc, exp) => (acc += parseFloat(exp.value)), 0);
			} else {
				return 0;
			}
		},
		expensesLeftValue() {
			if (this.cashierData.expenses && this.cashierData.expenses.length > 0) {
				return this.cashierData.expenses
					.filter((exp) => ![3, 5].includes(exp.payment_method_id))
					.reduce((acc, exp) => (acc += parseFloat(exp.value)), 0);
			} else {
				return 0;
			}
		},
		expensesValue() {
			if (this.cashierData.expenses && this.cashierData.expenses.length > 0) {
				return this.cashierData.expenses.reduce(
					(acc, exp) => (acc += parseFloat(exp.value)),
					0
				);
			} else {
				return 0;
			}
		},
		depositValue() {
			return parseFloat(this.cashierData.deposit) - this.expensesPaidValue;
		},
		previousCashierCharge() {
			if (this.cashierData.earnings) {
				return (
					this.depositValue -
					this.cashierData.earnings.find((payment) => payment.id === 3).total +
					parseFloat(this.cashierData.charge)
				);
			} else {
				return 0;
			}
		},
	},

	methods: {
		getClientName(item) {
			if (this.isMaintenance) {
				if (item.order.client && item.order.client.full_name) {
					const splitedName = _.words(item.order.client.full_name);
					return splitedName[1]
						? `${splitedName[0]} ${splitedName[1]}`
						: splitedName[0];
				}
				return "Desconhecido";
			} else {
				if (item.client && item.client.full_name) {
					const splitedName = _.words(item.client.full_name);
					return splitedName[1]
						? `${splitedName[0]} ${splitedName[1]}`
						: splitedName[0];
				}

				return "Desconhecido";
			}
		},
		async printReport() {
			const _this = this;
			await _this.$htmlToPaper("cashier-report");
		},
		getSuppliterName(expense) {
			const name =
				expense.suplier && expense.suplier.name
					? expense.suplier.name
					: "Fornecedor desconhecido";
			const typePayment = this.getNameTypePayment(expense.payment_method_id);
			return `${name} (${typePayment})`;
		},
		getNameTypePayment(paymentId) {
			switch (paymentId) {
				case 1:
					return "Cartão de crédito";
				case 2:
					return "Cartão de débito";
				case 3:
					return "Dinheiro";
				case 4:
					return "PIX";
				case 5:
					return "Cheque";
				case 6:
					return "Boleto";
				default:
					return "Desconhecido";
			}
		},
		getTaxInstallment(id) {
			let installments = this.paymentsArray.filter((item) => {
				return item[0] === "Cartão crédito";
			});
			installments = installments[0][1].orders.filter((order) => {
				return order.id === id;
			});
			if (installments.length === 1) {
				return installments[0].payments[0].tax_installment_id;
			} else if (installments.length > 1) {
				installments.forEach((item, index) => {
					installments[index].taxIn = item.payments[index].tax_installment_id;
				});
				return true;
			} else return;
		},
		getTotalOrdersByPayment(params, paymentId) {
			const order = params.order ? params.order : params;
			return order.payments.find((pay) => pay.payment_method_id === paymentId)
				.value;
		},
		getChargeByPayment(params, paymentId) {
			const order = params.order ? params.order : params;
			const payment = order.payments.find(
				(pay) => pay.payment_method_id === paymentId
			);
			return payment && payment.charge ? parseFloat(payment.charge) : "--";
		},
		async getCashierInfo() {
			this.$emit(
				"merge-cashiers",
				this.moment(this.cashierData.created_at).format("YYYY-MM-DD")
			);
		},
		restore() {
			this.$emit("restore-merge-cashiers");
		},
	},
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss">
.modal {
	&.moda-cashier-report {
		.table-responsive {
			padding: 2px;

			tr td {
				padding: 5px 10px;
			}
		}

		.row-header {
			td {
				border: 2px solid grey !important;
				border-spacing: 0px;
			}
		}

		.bg-row-grey {
			background: #eee;
		}

		.totals {
			border: 1px solid gray;
			padding: 3px 8px;
		}
	}
}
</style>
