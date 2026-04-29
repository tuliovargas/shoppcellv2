<template>
	<div
		class="modal"
		id="cancelModal"
		tabindex="-1"
		role="dialog"
		ref="cancelModal"
	>
		<form class="modal-dialog" role="document">
			<div v-if="cancelOrder" class="modal-content">
				<div class="modal-header">
					<h5
						v-if="cancelOrder"
						class="modal-title font-weight-bold text-center w-100"
					>
						CANCELAMENTO DE VENDA
					</h5>
					<button
						type="button"
						class="close"
						data-dismiss="modal"
						aria-label="Close"
					>
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" v-if="cancelOrder">
					<div class="text-center d-flex mr-2 align-items-center">
						<img
							class="mb-2 d-flex"
							style="max-width: 120px; height: 35px;"
							src="/images/logo.png"
							alt="logo"
						/>
						<span class="ml-2" style="font-size: 12px"
							>{{ address.toUpperCase() }} <br />
							{{ cellphone }} {{ email }}</span
						>
					</div>

					<hr />
					<div class="d-flex justify-content-between align-items-center">
						<p class="mb-0">{{ today.format("DD/MM/YYYY H:mm") }}</p>
						<p class="mb-0">
							{{ "Venda N:" }}

							<span class="text-lg font-weight-bold">{{
								cancelOrder ? cancelOrder.id : 1
							}}</span>
						</p>
					</div>
					<hr />
					<div class="mb-0">
						<div class="d-flex justify-content-between">
							<span>
								<span class="font-weight-bold">
									Cliente:
								</span>
								{{
									cancelOrder.client
										? cancelOrder.client.full_name
										: "Consumidor Final"
								}}
							</span>
						</div>
						<span v-html="getClientDocument(cancelOrder)"></span>
					</div>
					<hr />

					<div
						v-if="cancelOrder.cancellation_coupon"
						class="d-flex flex-column"
					>
						<span
							>VALOR TOTAL DEVOLVIDO EM CUPOM:
							<b>{{
								cancelOrder.cancellation_coupon.value | currency
							}}</b></span
						>
						<span>
							CUPOM VÁLIDO ATÉ:
							<b>{{
								moment(cancelOrder.cancellation_coupon.end_date).format(
									"DD/MM/YYYY"
								)
							}}</b></span
						>
						<span>
							CODIGO DO CUPOM: <b>{{ cancelOrder.cancellation_coupon.name }}</b>
						</span>
					</div>
					<template v-else>
						<div v-for="(pay, i) in cancelOrder.payments" :key="i">
							<span
								>{{ getLabelChargeBack(pay).label }}
								{{ getLabelChargeBack(pay).value | currency }}
								<b>{{ pay.value | currency }}</b></span
							>
							<br />
							<span :key="i"
								>CANCELADA EM:
								{{ moment(pay.updated_at).format("DD/MM/YYYY") }}</span
							>
						</div>
					</template>
					<div class="mt-3 text-right">
						<div>SUBTOTAL DA VENDA: {{ cancelOrder.subtotal | currency }}</div>
						<div v-if="cancelOrder.coupon">
							CUPOM DESCONTO ({{ cancelOrder.coupon.name }}):
							{{ cancelOrder.coupon.value | currency }}
						</div>
						<div>
							DESCONTO DA VENDA:
							{{ cancelOrder.discount | currency }}
						</div>
						<div class="font-weight-bold">
							TOTAL DA VENDA:
							{{ cancelOrder.total | currency }}
						</div>
					</div>
					<div
						class="my-2 border-top pt-2"
						v-if="cancelOrder.note && cancelOrder.note.length > 0"
					>
						<h6 class="font-weight-bold">
							Observações
						</h6>
						<p
							v-html="
								cancelOrder.cancellation_observation.replace(
									/(\r\n|\n|\r)/gm,
									`<br />`
								)
							"
						></p>
					</div>
				</div>
				<div class="modal-footer">
					<div class="row" style="width: 100%">
						<p>
							<!-- {{
								notIsBudget
									? warrantyText + " " + getObservation
									: orcamentoText + " " + getObservation
							}} -->
							{{
								cancelOrder.cancellation_coupon
									? `Este cupom pode ser utilizado na sua próxima compra na loja para
							desconto do valor integral do mesmo, sendo assim guarde-o em local
							seguro e na sua proxima compra apresente-o.`
									: `Este cupom serve como garantia de devolução parcial e/ou integral de valores referente a cancelamento da compra de Nº ${cancelOrder.id}, confirme se o valor devolvido é igual ao apresentado neste cupom, reclamações posteriores não seram levadas em consideração.`
							}}
						</p>
					</div>
					<div class="row">
						<a
							target="_blank"
							:href="'/print/cancellation/' + orderId"
							class="btn btn-lg btn-primary"
							@click="printing"
						>
							Imprimir
						</a>
					</div>
				</div>
			</div>
		</form>
	</div>
</template>

<script>
import moment from "moment";
import _ from "lodash";
import { formatReal } from "../helpers/number";
export default {
	props: {
		cancelOrder: {},
		warrantyText: "",
		orcamentoText: "",
		address: "",
		cellphone: "",
		email: ""
	},

	data() {
		return {
			moment: moment
		};
	},

	filters: {
		currency(value) {
			if (!value) return "R$ 0,00";
			return `R$ ${parseFloat(value).toLocaleString("pt-br", {
				minimumFractionDigits: 2
			})}`;
		},
		currencyWithouRS(value) {
			if (!value) return "0,00";
			return `${parseFloat(value).toLocaleString("pt-br", {
				minimumFractionDigits: 2
			})}`;
		}
	},

	computed: {
		today() {
			return moment(Date.now());
		},
		getObservation() {
			return this.observation;
		},
		orderId() {
			return this.cancelOrder ? this.cancelOrder.id : null;
		}
	},

	methods: {
		formatReal,
		getTotalByProducts(item) {
			let total = 0;
			total +=
				(parseFloat(item.price) +
					parseFloat(item.addition) -
					parseFloat(item.discount)) *
				parseInt(item.amount);

			item.by_products.forEach(prod => {
				total +=
					parseFloat(prod.price) +
					parseFloat(prod.addition) -
					parseFloat(prod.discount);
			});
			return total;
		},
		getTotalProducts(item) {
			return (
				item.amount * parseFloat(item.price) +
				parseFloat(item.addition) -
				parseFloat(item.discount)
			);
		},
		printing() {
			this.$emit("budgetPrint");
		},
		getProductName(item) {
			try {
				if (item.product_id === 1) {
					const { brand, brand_model, issue } = item.maintenance;

					return `${item.product.name} \n (${item.id}) <br /> ${
						brand ? brand.name : ""
					} / ${brand_model ? brand_model.name : ""} <br /> ${issue}`;
				}
				return item.product.name;
			} catch (e) {
				return "Error processing";
			}
		},
		getClientDocument(order) {
			try {
				const { cpf, cpnj } = order.client;
				if (cpnj) {
					return `<span class="font-weight-bold">CNPJ</span>: ${cnpj}`;
				} else {
					return `<span class="font-weight-bold">CPF</span>: ${cpf}`;
				}
			} catch (e) {
				console.error(e);
				return "";
			}
		},
		getLabelChargeBack(pay) {
			try {
				switch (pay.payment_method_id) {
					case 1 || 2 || 4:
						return {
							label: `VALOR EXTORNADO EM ${pay.payment_method.name.toUpperCase()}`
						};
					case 3 || 5:
						return {
							label: `VALOR DEVOLVIDO EM ${pay.payment_method.name.toUpperCase()}`
						};
				}
			} catch (error) {
				return { label: " null", value: 0 };
			}
		}
	}
};
</script>

<style></style>
