<template>
	<div class="modal" id="selectGaranteeModal" tabindex="-1" role="dialog">
		<form class="modal-dialog" style="min-width: 750px;" @submit.prevent>
			<div class="modal-content" style="padding: 0px; min-width: 750px;">
				<div class="modal-header">
					<h5 class="modal-title font-weight-bold">
						Selecionar Produto Garantia
					</h5>
					<button
						@click="cancelGarantee()"
						type="button"
						class="close"
						data-dismiss="modal"
						aria-label="Close"
					>
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="w-100 divider">
						<div>
							<label class="w-100">CLIENTE</label>
							<span>{{ clientName }}</span>
						</div>
					</div>

					<table class="table table-striped">
						<thead>
							<tr style="cursor: default;">
								<th width="30%">Data</th>
								<th width="30%">Quantidade</th>
								<th>Vendedor</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<template v-for="(order, index) in garanteeOrders">
								<tr
									:key="index"
									class="expanse-row"
									:class="{
										opened: opened.includes(order.id)
									}"
									style="border: 0;"
									@click="toggle(order.id)"
								>
									<td>
										{{ moment(order.created_at).format("DD/MM/YYYY") }}
									</td>
									<td>
										{{ order.products.length }}
									</td>
									<td>
										{{ order.seller.name }}
									</td>
									<td>
										<i class="fas fa-chevron-down"></i>
									</td>
								</tr>
								<tr
									:key="`${index}-expanded`"
									v-if="opened.includes(order.id)"
									style="cursor: default;"
								>
									<td colspan="4" class="p-0">
										<div class="expanse-row-table w-100">
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="5%"></th>
														<th width="30%">
															Produto
														</th>
														<th width="22%">
															Data Compra
														</th>
														<th width="21%">
															Dias Garantia
														</th>
														<th width="22%">
															Status
														</th>
													</tr>
												</thead>
												<tbody>
													<!-- Linha vazia para deixar a primeira linha da table branca -->
													<tr style="cursor: default;"></tr>
													<tr
														v-for="(prod, index) in order.products"
														:key="index"
														style="user-select: none;"
														@click="handleSelectedProduct(order, prod)"
													>
														<td>
															<input
																type="checkbox"
																:name="'selectedProduct-' + index"
																:id="'selectedProduct-' + index"
																v-model="prod.selected"
																:disabled="!prod.selected && disableSelection"
																@change="
																	handleSelectedProductCheck(order, prod)
																"
															/>
														</td>
														<td>
															{{ prod.product.name }}
														</td>
														<td>
															{{
																moment(order.created_at).format("DD/MM/YYYY")
															}}
														</td>
														<td>
															{{ prod.product.days_warranty }}
														</td>
														<td>
															<span
																v-if="
																	getStatus(
																		order.created_at,
																		prod.product.days_warranty
																	)
																"
																class="not-warranty"
															>
																Fora da Garantia
															</span>
															<span v-else class="have-warranty">
																Na Garantia
															</span>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</td>
								</tr>
							</template>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button
						type="button"
						@click="cancelGarantee()"
						class="btn btn-lg btn-danger"
					>
						Cancelar
					</button>
					<button
						type="button"
						@click="handleSelectedGarantee()"
						class="btn btn-lg btn-primary"
					>
						<b>Confirmar</b>
					</button>
				</div>
			</div>
		</form>
	</div>
</template>

<script>
import moment from "moment";
import toastr from "toastr";
import axios from "axios";

export default {
	name: "ModalGarantee",

	props: {
		selectedClient: {
			required: true
		}
	},

	data() {
		return {
			orders: [],
			opened: [],
			moment: moment,
			selectedProduct: null,
			selectedOrder: null,
			disableSelection: false
		};
	},

	mounted() {
		moment.locale("pt-br");
	},

	beforeDestroy() {
		if (!this.selectedOrder && !this.selectedProduct)
			this.$emit("cancelGarantee", false);
	},

	computed: {
		clientName() {
			return this.selectedClient && this.selectedClient.full_name
				? this.selectedClient.full_name
				: "Nenhum cliente selecionado";
		},
		garanteeOrders() {
			return this.orders.filter(order => !order.order_id);
		}
	},
	methods: {
		getOrders() {
			axios
				.get("/orders", {
					params: {
						paginate: false,
						type: "vue",
						clientId: this.selectedClient.id
					}
				})
				.then(response => {
					this.orders = response.data;
				})
				.catch(() => {
					toastr.error("Algo deu errado, tente novamente mais tarde.");
				});
		},
		toggle(orderId) {
			const index = this.opened.indexOf(orderId);
			if (index > -1) {
				this.opened.splice(index, 1);
			} else {
				this.opened.push(orderId);
			}
			this.$forceUpdate();
		},
		getStatus(buyDate, warrantyDays) {
			const buyDateOld = moment(buyDate).format("L");
			const buyDateUpd = moment(buyDate)
				.add(warrantyDays, "d")
				.format("L");
			return moment(buyDateOld).isBefore(buyDateUpd);
		},
		handleSelectedProduct(order, product) {
			if (this.getStatus(order.created_at, product.days_warranty))
				return toastr.error("Produto fora de Garantia!");

			if (this.disableSelection) {
				this.selectedOrder = null;
				this.selectedProduct = null;
				this.disableSelection = false;
				product.selected = false;
			} else {
				this.selectedOrder = order;
				this.selectedProduct = product;
				this.disableSelection = true;
				product.selected = true;
			}
		},
		handleSelectedProductCheck(order, product) {
			if (this.getStatus(order.created_at, product.days_warranty))
				return toastr.error("Produto fora de Garantia!");

			if (!this.disableSelection) {
				this.selectedOrder = null;
				this.selectedProduct = null;
				this.disableSelection = false;
				product.selected = false;
			} else {
				this.selectedOrder = order;
				this.selectedProduct = product;
				this.disableSelection = true;
				product.selected = true;
			}
		},
		cancelGarantee() {
			this.$emit("cancelGarantee", false);
		},
		handleSelectedGarantee() {
			if (!this.selectedOrder || !this.selectedProduct) this.cancelGarantee();
			else {
				this.$emit("confirmedGarantee", {
					order: this.selectedOrder,
					product: this.selectedProduct
				});
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.expanse-row {
	&:hover {
		color: #0983e8;
		background-color: #e3f2ff;
	}

	&.opened {
		.fa-chevron-down {
			transform: rotate(180deg);
		}
		background-color: #e3f2cc;
		.header {
			padding: 1px;
			border: 1px solid #aebed1;
			box-sizing: border-box;
			border-bottom: none;
			border-radius: 4px 4px 0 0;
		}
	}
}

.expanse-row-table {
	border: 1px solid #aebed1;
	box-sizing: border-box;
	border-top: none;
	border-radius: 0 0 4px 4px;
}

.have-warranty {
	color: white;
	background: #299348;
	border-radius: 4px;
	padding: 6px 10px;
}

.not-warranty {
	color: white;
	background: #d63030;
	border-radius: 4px;
	padding: 6px 10px;
}

.divider {
	border-bottom: 1px solid #e4e7ed;
	padding-bottom: 10px;
}
</style>
