<template>
	<div>
		<table class="table table-striped">
			<thead>
				<tr class="text-center">
					<th class="text-left">Meios de Pagamento</th>
					<th style="width: 20%">Vendas</th>
					<th style="width: 20%">Diferença</th>
					<th>Valor</th>
				</tr>
			</thead>
			<tbody class="text-center">
				<tr v-for="(hist, i) in cashierInfo.earnings" :key="i">
					<td class="text-left">{{ hist.payment_type }}</td>
					<td>{{ hist.quantity_sales }}</td>
					<td>
						<div class="item-summary__addition">
							<span class="d-flex justify-content-center align-items-center">
								<span v-if="!hist.editDiff">
									{{ hist.difference || 0 | currency }}
								</span>
								<money
									v-if="hist.editDiff"
									class="money"
									:class="{
										'is-invalid': true
									}"
									@keypress.enter.native="applyDiff(hist)"
									v-model="hist.difference"
									v-bind="money"
								/>
								<a
									v-if="!hist.editDiff"
									href=""
									@click.prevent="editDiff(hist)"
									class="text-primary ml-2"
									><i class="fas fa-pen"></i
								></a>
								<a
									v-else
									href=""
									@click.prevent="applyDiff(hist)"
									class="text-primary ml-2"
									><i class="fas fa-save"></i
								></a>
							</span>
						</div>
					</td>
					<td>{{ (hist.total - hist.difference) | currency }}</td>
				</tr>
				<tr v-if="expense" class="text-danger text-bold">
					<td class="text-left">{{ expense.name }}</td>
					<td>Quantidade: {{ expense.quantity }}</td>
					<td>-</td>
					<td>{{ (parseFloat(expense.value) * -1) | currency }}</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
import moment from "moment";
import { Money } from "v-money";

export default {
	name: "CashierStatus",

	components: { Money },

	data() {
		return {
			moment: moment,
			cashierInfo: [],
			difference: [],
			expense: null,
			money: {
				decimal: ",",
				thousands: ".",
				prefix: "R$ ",
				suffix: "",
				precision: 2,
				masked: false
			}
		};
	},

	mounted() {
		this.getCashierInfo();
	},

	filters: {
		currency(value) {
			if (!value) return "R$ 0,00";
			return `R$ ${parseFloat(value).toLocaleString("pt-br", {
				minimumFractionDigits: 2
			})}`;
		}
	},

	methods: {
		async getCashierInfo() {
			let url = "/cashierInfo";

			try {
				const { data } = await axios.get(url);
				this.cashierInfo = data;

				if (data.expenses && data.expenses.length > 0) {
					this.expense = {
						name: `Despesas - ${moment().format("DD/MM/YYYY")}`,
						quantity: data.expenses.length,
						value: data.expenses.reduce(
							(acc, exp) => (acc += parseFloat(exp.value)),
							0
						)
					};
				}
			} catch (error) {
				console.error(error);
			}
		},

		async updateCashierInfo(id) {
			this.difference = {};

			this.cashierInfo.earnings.map((el, index) => {
				this.difference[index + 1] = el.difference;
			});

			const config = {
				difference: this.difference
			};

			let url = "/cashierInfo/" + id;

			try {
				let response = await axios.put(url, config);
				this.orders = response.data;
			} catch (error) {
				console.error(error);
			}
		},

		applyDiff(history) {
			history.editDiff = false;
			let valueDiff = 0;
			this.cashierInfo.earnings.forEach(el => {
				if (el.difference) valueDiff += parseFloat(el.difference);
			});
			this.$emit("updateDiference", valueDiff);
			this.$forceUpdate();
		},
		editDiff(history) {
			history.editDiff = true;
			this.$forceUpdate();
		}
	}
};
</script>

<style lang="scss" scoped>
.btn-edit {
	padding: 1px;
}

.money {
	height: calc(2.25rem + 2px);
	padding: 0.275rem 0.75rem;
	font-size: 1rem;
	font-weight: 400;
	line-height: 1.5;
	color: #495057;
	background-color: #fff;
	background-clip: padding-box;
	border: 1px solid #ced4da;
	border-radius: 0.25rem;
	box-shadow: inset 0 0 0 transparent;
	transition: border-color 0.15s;
	max-width: 90px;
}
</style>
