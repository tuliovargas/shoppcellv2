<template>
	<div
		class="modal"
		id="productEdit"
		tabindex="-1"
		role="dialog"
		ref="productEdit"
	>
		<form class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title font-weight-bold text-center w-100">
						Edição de produto
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
				<div class="modal-body">
					<span
						>Produto: <b>{{ product.name }}</b></span
					>
					<div class="row mt-2 d-flex justify-content-end">
						<div class="col-4">
							<label>Quantidade</label>
							<input
								type="number"
								placeholder=""
								v-model="currentEditProduct.amount"
							/>
						</div>
						<div class="col-4">
							<label>Acréscimo</label>
							<money
								class="money"
								:class="{
									'is-invalid': parseFloat(currentEditProduct.addition) < 0
								}"
								v-model="currentEditProduct.addition"
								v-bind="money"
							/>
						</div>
						<div class="col-4">
							<label>Desconto</label>
							<money
								class="money"
								:class="{
									'is-invalid': parseFloat(currentEditProduct.discount) < 0
								}"
								v-model="currentEditProduct.discount"
								v-bind="money"
							/>
						</div>
					</div>
					<div class="d-flex justify-content-end mt-4">
						<div class="mr-3">
							<label>Preço</label>
							<span>{{ price | currency }}</span>
						</div>
						<div>
							<label>Subtotal</label>
							<span>{{
								(price * parseInt(currentEditProduct.amount)) | currency
							}}</span>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="d-flex">
						<button
							class="btn btn-danger mr-2"
							data-dismiss="modal"
							aria-label="Close"
						>
							Cancelar
						</button>
						<button class="btn btn-primary" @click.prevent="submit()">
							Salvar
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</template>

<script>
import { Money } from "v-money";

export default {
	components: { Money },
	props: {
		currentEditProduct: {
			type: Object,
			required: true
		},
		currentIndex: {
			type: Number
		}
	},

	data() {
		return {
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

	filters: {
		currency(value) {
			if (!value) return "R$ 0,00";
			return `R$ ${parseFloat(value).toLocaleString("pt-br", {
				minimumFractionDigits: 2
			})}`;
		}
	},

	computed: {
		product() {
			return this.currentEditProduct.product || "-";
		},
		quantity() {
			return this.currentEditProduct.amount;
		},
		price() {
			if (this.currentEditProduct && this.currentEditProduct.product)
				return (
					parseFloat(this.currentEditProduct.product.price) +
					parseFloat(this.currentEditProduct.addition) -
					parseFloat(this.currentEditProduct.discount)
				);
		}
	},

	methods: {
		submit() {
			this.$emit("submit", {
				product: this.currentEditProduct,
				update: true,
				indexProd: this.currentIndex
			});
		}
	}
};
</script>

<style></style>
