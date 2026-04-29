<template>
	<div class="w-100 h-100 card card-body mb-0">
		<template>
			<div class="form-group row px-2 mb-2">
				<div class="col-12 col-md-6">
					<label for=""> NOME </label>
					<input type="text" class="form-control" v-model="form.name" />
				</div>
			</div>
			<div class="form-group row mb-3 px-2">
				<div class="col-6 pr-3">
					<label>FORNECEDOR</label>
					<select2
						v-model="form.supplier_id"
						:options="suppliersOptions"
						name="select2"
						class="form"
					>
						<option value="" disabled>Selecione</option>
					</select2>
				</div>
				<div class="col-6 pl-3">
					<label>TIPO DESPESA</label>
					<select2
						:options="expenseTypesOptions"
						v-model="form.expense_type_id"
						placeholder="Tipos de despesas"
					>
						<option value="" disabled>Selecione</option>
					</select2>
				</div>
			</div>
			<div class="form-group row mb-3 px-2">
				<div class="col-6 pr-3">
					<label>MEIO DE PAGAMENTO</label>
					<select2
						class="form-control form"
						:options="paymentTypesOptions"
						v-model="form.payment_method_id"
					>
						<option value="" disabled>Selecione</option>
					</select2>
				</div>
				<div class="col-6 pl-3">
					<label for="value">VALOR</label>
					<money
						class="money"
						:class="{ 'is-invalid': false }"
						v-model="form.value"
						v-bind="money"
					></money>
					<p v-if="isMax" class="text-danger">
						deve ser menor que <b>R$ {{ maxValue }}</b>
					</p>
				</div>
			</div>
			<div class="form-group row mb-3 px-2">
				<div class="col-6 pr-3">
					<label>PARCELAS</label>
					<select class="form-control form" v-model="form.installments">
						<option v-for="(number, index) in 12" :key="index">
							{{ number }}
						</option>
					</select>
				</div>
				<div class="col-6 pl-3">
					<label for="payment-day">DIA DO PAGAMENTO</label>
					<input
						type="date"
						class="form-control form"
						id="payment-day"
						v-model="form.payday"
					/>
				</div>
			</div>
			<div class="form-group px-2 mb-5">
				<label class="d-block text-uppercase">Observação</label>
				<textarea
					class="w-100 h-100 txt-observations form-control"
					placeholder="Observação"
					v-model="form.observation"
				>
				</textarea>
			</div>
			<div class="form-group px-2 mb-0">
				<div class="custom-file custom-file-lg">
					<input
						type="file"
						class="custom-file-input px-2"
						id="customFile"
						lang="pt-br"
						@change="listFiles"
						multiple
					/>
					<label
						class="custom-file-label"
						for="customFile"
						data-browse="Selecionar"
						>Escolha um arquivo</label
					>
				</div>
			</div>
			<ul class="list-group list-images px-2">
				<li
					class="list-group-item d-flex justify-content-between align-items-center"
					v-for="(file, i) in form.selectedImages"
					:key="i"
				>
					<span>{{ file.name }}</span>
					<button
						class="btn"
						style="padding: 0 0.75rem"
						@click="removeImageFromList(i)"
					>
						<span style="display: block; color: #d63030">
							<i class="far fa-trash-alt"></i>
						</span>
					</button>
				</li>
			</ul>
			<div class="text-right form-group px-2 mb-0">
				<button class="btn btn-info" :disabled="isOnError" @click="saveExpense">
					Salvar
				</button>
			</div>
		</template>
	</div>
</template>

<script>
import { Money } from "v-money";
import toastr from "toastr";
import "toastr/build/toastr.min.css";
import moment from "moment";

import { getSuppliers as loadSuppliers } from "../../services/supplier";
import { getPaymentTypes as loadPaymentTypes } from "../../services/payment";
import { getExpenseTypes as loadExpenseTypes } from "../../services/expenses";

const defaultForm = {
	name: null,
	value: 0,
	observation: null,
	selectedImages: [],
	payday: moment().format("YYYY-MM-DD"),
	payment_method_id: null,
	expense_type_id: null,
	installments: 1,
	supplier_id: null,
};

export default {
	name: "ExpenseForm",

	components: { Money },

	props: {
		maxValue: {
			required: true,
			default: 0,
		},
	},

	data() {
		return {
			selectValue: null,
			isOnError: false,
			isMax: false,
			form: {
				installments: 1,
				selectedImages: [],
				payday: moment().format("YYYY-MM-DD"),
			},
			expenseTypes: [],
			suppliers: [],
			paymentTypes: [],
			money: {
				decimal: ",",
				thousands: ".",
				prefix: "R$ ",
				suffix: "",
				precision: 2,
				masked: false,
			},
		};
	},

	computed: {
		expenseTypesOptions() {
			let opts = this.expenseTypes.map((expense) => {
				return { id: expense.id, text: expense.name };
			});
			return opts;
		},
		suppliersOptions() {
			return this.suppliers.map((sup) => {
				return {
					id: sup.id,
					text: sup.name,
				};
			});
		},
		paymentTypesOptions() {
			return this.paymentTypes.map((pay) => {
				return {
					id: pay.id,
					text: pay.name,
				};
			});
		},
	},

	methods: {
		getSuppliers() {
			loadSuppliers()
				.then((data) => {
					this.suppliers = data;
				})
				.catch((error) => {
					toastr.error("Erro ao carregar fornecedores");
				});
		},
		listFiles(event) {
			this.form.selectedImages.push(event.target.files[0]);
		},

		removeImageFromList(index) {
			this.form.selectedImages.splice(index, 1);
		},
		saveExpense() {
			let data = new FormData();
			for (let file of this.form.selectedImages) {
				data.append("receipt[]", file, file["name"]);
			}
			for (let key of Object.keys(this.form)) {
				data.append(key, this.form[key]);
			}
			axios
				.post("/expenses", data, {
					headers: {
						"Content-Type": `multipart/form-data; boundary=${data._boundary}`,
					},
				})
				.then(({ data }) => {
					this.form = defaultForm;
					this.$emit("savedExpense");
					toastr.success("Despesa cadastrada com sucesso");
				})
				.catch((error) => {
					const errors = error.response.data.errors;
					let fail = "<ul>";
					for (let err of Object.keys(errors)) {
						fail += `<li>${errors[err][0]}</li>`;
					}
					fail += "</ul>";
					toastr.error(fail, "Erro ao cadastrar despesa");
				});
		},

		getPaymentTypes() {
			loadPaymentTypes()
				.then((data) => {
					this.paymentTypes = data;
				})
				.catch((error) => {
					toastr.error("Falha ao carregar tipos de pagamentos");
				});
		},

		getExpenseTypes() {
			loadExpenseTypes()
				.then((data) => {
					this.expenseTypes = data;
				})
				.catch((error) => {
					toastr.error("Falha ao carregar os tipos de gasto");
				});
		},
	},
	mounted() {
		this.getExpenseTypes();
		this.getSuppliers();
		this.getPaymentTypes();
	},
	watch: {
		"form.value"(val) {
			if (val > this.$props.maxValue) {
				this.isOnError = true;
				this.isMax = true;
			} else {
				this.isOnError = false;
				this.isMax = false;
			}
		},
	},
};
</script>
<style lang="scss" scoped>
@import "~@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.css";

.txt-observations {
	resize: none;
	min-height: 120px;
}

.col-form-label-lg {
	min-height: 55px;
	height: 100% !important;
	font-size: 1rem;
	line-height: 2.5;

	&::after {
		height: 100%;
		font-size: 1rem;
		line-height: 2.5;
	}
}

.list-images {
	margin-top: 15px;
	padding: 0 1.5rem;
	max-height: 150px;
	overflow-y: auto;

	.list-group-item {
		border: none;
		background: rgba(228, 231, 237, 0.3);
		border-radius: 4px;
	}

	li {
		padding: 0.75rem 15px 0.75rem 35px;

		span {
			display: list-item;

			&::marker {
				color: #0983e8;
			}
		}
	}
}

.sale-info {
	margin-top: 1rem;
	width: 100%;
	border-radius: 4px;
	background-color: #fbfcfc;
	border: 1px solid #e4e7ed;
	list-style: none;
	padding: 0 2rem;

	li {
		display: flex;
		justify-content: space-between;
		align-items: center;
		width: 100%;
		padding: 1rem 0;
		border-bottom: 1px solid #e4e7ed;
	}

	h2,
	p {
		margin: 0;
		display: inline;
		font-size: 1rem;
	}

	.input {
		padding: 1.125rem;
		border-radius: 4px;
	}
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
	width: 100%;
}
</style>
