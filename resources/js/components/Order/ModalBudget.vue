<template>
	<div
		class="modal"
		id="budgetModal"
		tabindex="-1"
		role="dialog"
		ref="modalBudget"
	>
		<form class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5
						v-if="lastOrder"
						class="modal-title font-weight-bold text-center w-100"
					>
						{{
							lastOrder.is_warranty
								? "G A R A N T I A"
								: notIsBudget
								? "ORDEM DE VENDA"
								: "O R Ç A M E N T O"
						}}
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
				<div class="modal-body" v-if="lastOrder">
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
							{{ notIsBudget ? "Venda N:" : "Orçam. N:" }}

							<span class="text-lg font-weight-bold">{{
								lastOrder ? lastOrder.id : 1
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
								{{ lastOrder.client.full_name }}
							</span>

							<span v-if="lastOrder.user">
								<span class="font-weight-bold">
									Vendedor:
								</span>
								{{ lastOrder.seller.name }}
							</span>
						</div>
						<span v-html="getClientDocument(lastOrder)"></span>
					</div>
					<hr />
					<div class="row border-bottom-dashed pb-2 mb-2">
						<div class="col-2">
							<span class="font-weight-bold">CÓD</span>
						</div>
						<div class="col-5">
							<span class="font-weight-bold">PRODUTO/SERVIÇO</span>
						</div>
						<div class="col-2">
							<span class="font-weight-bold">QUANT.</span>
						</div>
						<div class="col-3">
							<span class="font-weight-bold">V.TOTAL</span>
						</div>
					</div>
					<div
						v-for="(item, index) in lastOrder.products"
						:key="index"
						class="row"
					>
						<div class="col-2 text-center">
							{{ item.product.id }}
						</div>
						<div class="col-5" v-html="getProductName(item)"></div>
						<div class="col-2 text-center">
							{{ item.amount }}
						</div>
						<div class="col-3 text-right">
							<p class="text-break">
								{{ formatReal(getTotalProducts(item)) }}
							</p>
						</div>
						<div
							class="row w-100"
							v-for="(by_item, i) in item.by_products"
							:key="i"
						>
							<div class="col-2 text-center">
								{{ by_item.product_id }}
							</div>
							<div class="col-5">
								-
								{{
									by_item.product && by_item.product.name
										? by_item.product.name
										: "Desconhecido"
								}}
							</div>
							<div class="col-2 text-center">
								{{ item.amount }}
							</div>
							<div class="col-3 text-right">
								{{ by_item.price | currency }}
							</div>
						</div>
					</div>
					<div class="mt-3 text-right">
						<div>SUBTOTAL: {{ lastOrder.subtotal | currency }}</div>
						<div v-if="lastOrder.coupon">
							CUPOM DESCONTO ({{ lastOrder.coupon.name }}):
							{{ lastOrder.coupon.value | currency }}
						</div>
						<div>
							DESCONTO TOTAL:
							{{ lastOrder.discount | currency }}
						</div>
						<div class="font-weight-bold">
							TOTAL:
							{{ lastOrder.total | currency }}
						</div>
					</div>
					<div
						class="my-2 border-top pt-2"
						v-if="lastOrder.note && lastOrder.note.length > 0"
					>
						<h6 class="font-weight-bold">
							Observações
						</h6>
						<p v-html="lastOrder.note.replace(/(\r\n|\n|\r)/gm, `<br />`)"></p>
					</div>
				</div>
				<div class="modal-footer">
					<div class="row" style="width: 100%">
						<p>
							{{
								notIsBudget
									? warrantyText + " " + getObservation
									: orcamentoText + " " + getObservation
							}}
						</p>
					</div>
					<div class="row">
						<a
							target="_blank"
							:href="'/print/order/' + orderId"
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
		sellerSelected: null,
		lastOrder: null,
		observation: "",
		warrantyText: "",
		orcamentoText: "",
		address: "",
		cellphone: "",
		email: ""
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
		notIsBudget() {
			return this.lastOrder && this.lastOrder.status !== "is_budget";
		},
		getObservation() {
			return this.observation ? this.observation : "";
		},
		orderId() {
			return this.lastOrder ? this.lastOrder.id : null;
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
					let productName = "";
					const { brand, brand_model, issue } = item.maintenance;

					productName = `${item.product.name} \n (${item.id}) <br /> ${
						brand ? brand.name : ""
					} / ${brand_model ? brand_model.name : ""} <br /> ${issue} <br />`;

					// if (item.by_products && !!item.by_products.length) {
					// 	item.by_products.forEach((item, i) => {
					// 		productName += `- ${this.getMaintenanceProdName(item)} <br />`;
					// 	});
					// }

					return productName;
				}
				return item.product.name;
			} catch (e) {
				return "Error processing";
			}
		},
		getMaintenanceProdName(item) {
			try {
				return item.product.name;
			} catch (error) {
				return "Desconhecido";
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
		}
	}
};
</script>

<style></style>
