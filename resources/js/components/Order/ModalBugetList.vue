<template>
	<!--Modal listagem de orçamentos-->
	<div
		class="modal modal-budget-list"
		id="budgetListModal"
		tabindex="-1"
		role="dialog"
	>
		<form class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header flex-column">
					<div class="d-flex justify-content-between w-100 mb-1">
						<h5 class="modal-title font-weight-bold">
							{{ isMaintenance ? "Orçamentos / Manutenções" : "Orçamentos" }}
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
					<div class="d-flex justify-content-between w-100">
						<input
							type="search"
							ref="searchClient"
							autocomplete="off"
							@keyup="searchOrderByName()"
							v-model="searchOrder"
							class="form-control mr-2"
							placeholder="Filtrar por (ID / Cliente / Item)"
							id="client"
							@keyup.enter="searchOrderByName()"
						/>
						<div style="max-width: 400px" class="mx-4">
							<multiselect
								style="min-width: 350px"
								v-model="selectedStatus"
								:options="listStatus"
								:multiple="true"
								:close-on-select="false"
								:clear-on-select="false"
								:preserve-search="true"
								:taggable="false"
								:show-labels="false"
								@input="changeStatus"
								selectLabel=""
								placeholder="Selectione um ou mais status"
								label="text"
								track-by="text"
								:preselect-first="true"
							>
								<template slot="selection" slot-scope="{ values, isOpen }"
									><span
										class="multiselect__single"
										v-if="values.length && !isOpen"
										>{{ selectedStatus.length }} Status selecionados</span
									></template
								>
							</multiselect>
						</div>
						<div class="w-100 d-flex ml-2" style="max-width: 350px">
							<flat-pickr
								ref="calendar"
								v-model="dateSelected"
								class="form-control mr-2"
								placeholder="Filtrar por período"
								:config="config"
								@input="setRangeDate"
							>
							</flat-pickr>
						</div>
					</div>
				</div>
				<div class="modal-body">
					<p v-if="hasClient" class="font-weight-bold mb-0">Cliente</p>
					<p v-if="hasClient">{{ client.full_name }}</p>
					<div v-if="orders && orders.length > 0" class="table-responsive">
						<table class="table table-hover table-budget">
							<thead>
								<tr>
									<th scope="col" width="45px">ID</th>
									<th scope="col" v-if="!hasClient">Cliente</th>
									<th scope="col">Item</th>
									<th scope="col">
										{{ isMaintenance ? "Data conserto" : "Data" }}
									</th>
									<th scope="col">Vendedor</th>
									<th v-if="isMaintenance" scope="col">Técnico</th>
									<th scope="col">Valor</th>
									<th scope="col">Status</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="order in listOrders"
									:key="order.id"
									class="cursor-pointer-hover"
									@click.prevent="handleClick(order)"
								>
									<td width="45px">{{ order.id }}</td>
									<td v-if="!hasClient">{{ getClientName(order) }}</td>
									<td>
										<span v-for="(item, i) in productsBy(order)" :key="i"
											>-
											{{ isMaintenance ? getItemName(order) : getItemName(item)
											}}<br
										/></span>
									</td>
									<td align="center">
										{{ moment(order.created_at).format("DD/MM/YYYY") }}
									</td>
									<td>
										{{ sellerName }}
									</td>
									<td v-if="isMaintenance">
										{{ getTechnicianName(order) }}
									</td>
									<td>{{ order.total || order.order.total | currency }}</td>
									<td>
										<span
											class="badge status"
											:class="getClassBadgeByStatus(orderStatus(order))"
											>{{ getLabelByStatus(orderStatus(order)) }}</span
										>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="text-center p-5" v-else>Nenhum orçamento encontrado</div>
				</div>
			</div>
		</form>

		<div
			ref="modalClearCart"
			class="modal"
			id="modalClearCart"
			tabindex="-1"
			role="dialog"
		>
			<form
				class="modal-dialog modal-lg"
				role="document"
				style="max-width: 300px; margin: 20% auto"
				id="formAdminPass"
			>
				<div class="modal-content">
					<div class="modal-header">
						<h5>Deseja REMOVER os itens da cesta ?</h5>
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
						<div class="form-group">
							<p>
								Realizar essa ação vai remover todos os itens adicionados na
								cesta e adicionar os novos selecionados.
							</p>
						</div>
					</div>
					<div class="modal-footer">
						<button
							type="button"
							@click="clearCartAndAddProducts()"
							class="btn btn-lg btn-primary"
						>
							Confirmar
						</button>
						<button
							type="button"
							@click="closeModal()"
							class="btn btn-lg btn-danger"
						>
							Cancelar
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</template>

<script>
import moment from "moment";
import { Portuguese } from "flatpickr/dist/l10n/pt";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import Multiselect from "vue-multiselect";
import _ from "lodash";

export default {
	components: { flatPickr, Multiselect },
	props: {
		orders: {
			required: true,
			default: () => [],
		},
		client: {
			required: true,
			default: () => {},
		},
		user: {
			required: true,
			default: () => [],
		},
		isMaintenance: {
			type: Boolean,
			default: false,
		},
		technicians: {
			type: Array,
			default: () => [],
		},
		cart: {
			type: Array,
		},
	},

	filters: {
		currency(value) {
			if (!value) return "R$ 0,00";
			return `R$ ${parseFloat(value).toLocaleString("pt-br", {
				minimumFractionDigits: 2,
			})}`;
		},
	},

	data() {
		return {
			moment: moment,
			searchOrder: null,
			budgetLabels: {
				is_budget: "Orçamento",
				waiting_approval: "Aguardando aprovação",
				waiting_payment: "Aguardando pagamento",
				approved: "Aprovado",
				waiting_product: "Aguardando Produto",
				concluded: "Concluído",
				canceled: "Cancelado",
				no_maintenance: "Sem conserto",
				no_maintenance_delivered: "Sem conserto e entregue",
				waiting_stock: "Aguardando Produto",
				finished: "Aguardando pagamento",
				maintenance: "Em manutenção",
				fixed: "Concertado",
			},
			dateSelected: null,
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
			lastOrderSelected: null,
			statusOrder: [
				{ text: "Orçamento", value: "is_budget" }, //  Orçamentos
				{ text: "Aguardando aprovação cliente", value: "waiting_approval" }, //O produto é um item de manutenção como consertar computador e está aguardando o cliente aprovar o conserto
				{ text: "Aprovado", value: "approved" }, //O produto é um item de manutenção como consertar computador e está aprovado o inicio do conserto
				{ text: "Aguardando produto", value: "waiting_product" }, //O produto é um item de manutenção como consertar computador, mas ainda não tem a peça necessária no estoque
				{ text: "Em manutenção", value: "maintenance" }, //O produto é um item de manutenção como consertar computador e está no processo de manutenção
				{ text: "Aguardando pagamento", value: "waiting_payment" }, //Produto ou serviço concluído que está aguardando pagamento do cliente
				{ text: "Concluído", value: "concluded" }, //Compra de produto ou serviço concluído
				{ text: "Cancelado", value: "canceled" }, //Compra de produto ou serviço cancelado
				{ text: "Pedido", value: "is_request" }, // O produto é um pedido para a compra pelo estabelecimento
				{ text: "Aguardando manutenção", value: "waiting_maintenance" }, // aguardando manutenção
				{ text: "Devolvido", value: "returned" }, //Compra de produto devolvido ou valor de serviço devolvido
				{ text: "Devolução parcial", value: "partially_returned" }, // parcialmente devolvida
			],
			statusMaintenance: [
				//os = ordem de serviço / manutencao
				{ text: "Aguardando aprovação cliente", value: "waiting_approval" }, // Aguardando aprovação do cliente || tela::PDV || listagem de ordens
				{ text: "Sem conserto", value: "no_maintenance" }, //sem concerto || tela::Manutençao || listagem de ordens
				{ text: "Consertado", value: "fixed" }, //finalizado/consertado || tela::Manutençao || listagem de ordens
				{ text: "Aprovado", value: "approved" }, // Cliente liberou para que faça a manutenção || tela::PDV
				{ text: "Aguardando peça do fornecedor", value: "waiting_stock" }, //Aguardando peça do fornecedor || tela::Manutençao
				{ text: "Em manutenção", value: "maintenance" }, //Em manutenção || tela::Manutençao
				{ text: "Finalizado", value: "finished" }, // enviado para recebimento no Caixa || tela::PDV só vai aparecer após o liberação do técnico
				{ text: "Entregue sem conserto", value: "no_maintenance_delivered" }, // Sem conserto e entregue
			],
			selectedStatus: [{ text: "Orçamento", value: "is_budget" }],
		};
	},

	computed: {
		sellerName() {
			return !_.isEmpty(this.user)
				? this.user.name.split(" ")[0]
				: "**Orçamento**";
		},
		hasClient() {
			return !_.isEmpty(this.client) && this.client.id !== 1;
		},
		listStatus() {
			return this.isMaintenance ? this.statusMaintenance : this.statusOrder;
		},
		listOrders() {
			return this.orders.reverse();
		},
	},

	methods: {
		handleClick(order) {
			this.lastOrderSelected = order;
			if (!!this.cart.length) {
				return $("#modalClearCart").modal("show");
			}
			this.$emit("selectedOrder", order);
		},
		clearCartAndAddProducts() {
			this.$emit("clearCart");
			setTimeout(() => {
				this.handleClick(this.lastOrderSelected);
				$("#modalClearCart").modal("hide");
			}, 100);
		},
		closeModal() {
			$("#modalClearCart").modal("hide");
		},
		searchOrderByName: _.debounce(function () {
			const status = this.isMaintenance
				? this.selectedStatus.map((el) => el.value)
				: this.selectedStatus.map((el) => el.value).join(",");

			this.$emit("getOrders", {
				dateIni: this.dateIni,
				dateFim: this.dateFim,
				type: this.isMaintenance ? "maintenance" : "budget",
				status: status,
				search: this.searchOrder,
			});
		}, 500),
		changeStatus: _.debounce(function (selectedOptions) {
			const status = this.isMaintenance
				? selectedOptions.map((el) => el.value)
				: selectedOptions.map((el) => el.value).join(",");

			this.$emit("getOrders", {
				dateIni: this.dateIni,
				dateFim: this.dateFim,
				type: this.isMaintenance ? "maintenance" : "budget",
				status: status,
				search: this.searchOrder,
			});
		}, 1000),
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

			if (this.callStackUpdateCalendar % 2 === 0) {
				const status = this.isMaintenance
					? this.selectedStatus.map((el) => el.value)
					: this.selectedStatus.map((el) => el.value).join(",");

				this.$emit("getOrders", {
					dateIni: this.dateIni,
					dateFim: this.dateFim,
					type: this.isMaintenance ? "maintenance" : "budget",
					status: status,
					search: this.searchOrder,
				});
			}
		},
		orderStatus(order) {
			return this.isMaintenance && order.order.status !== "concluded"
				? order.maintenance.os_status || "Sem status"
				: this.isMaintenance && order.order.status === "concluded"
				? order.order.status
				: order.status;
		},
		getLabelByStatus(status) {
			return this.budgetLabels[status]
				? this.budgetLabels[status]
				: "Status inválido";
		},
		getClassBadgeByStatus(status) {
			if (["concluded", "finished", "fixed"].includes(status))
				return "badge-success";
			if (["waiting_approval", "waiting_payment"].includes(status))
				return "badge-warning";
			if (["approved", "waiting_product"].includes(status)) return "badge-info";
			if (["canceled", "no_maintenance"].includes(status))
				return "badge-danger";
			if (["maintenance"].includes(status)) return "badge-primary";
			if (["waiting_stock"].includes(status)) return "badge-gray";
			if (["is_budget"].includes(status)) return "badge-buget";
			if (["no_maintenance_delivered"].includes(status)) return "badge-success";
		},
		productsBy(order) {
			const products = this.isMaintenance ? [1] : order.products;
			return !_.isEmpty(products)
				? products
				: [{ product: { name: "Sem produtos" } }];
		},
		getTechnicianName(order) {
			const techId = order.maintenance.user_id;
			const technician = this.technicians.find((tech) => tech.id === techId);
			return technician ? technician.name.split(" ")[0] : "Não encontrado";
		},
		getItemName(item) {
			if (this.isMaintenance) {
				return item.maintenance &&
					item.maintenance.brand &&
					item.maintenance.brand_model
					? `${item.maintenance.brand.name}/${item.maintenance.brand_model.name}`
					: "Desconhecido";
			}
			return item.product.name;
		},
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
	},
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss">
.modal {
	&.modal-budget-list {
		.multiselect__tags-wrap {
			display: none !important;
		}
		.multiselect__option {
			&.multiselect__option--selected {
				background: #41b883 !important;
				outline: none;
				color: #fff;
			}

			&.multiselect__option--highlight {
				background: #ddd !important;
				outline: none;
				color: #fff;

				&.multiselect__option--selected {
					background: #41b883 !important;
					outline: none;
					color: #fff;

					&:hover {
						background: #444 !important;
					}
				}
			}
		}
		table {
			tr {
				td,
				th {
					text-align: center;
				}
			}
		}

		.status {
			word-break: break-all;
		}

		.table-responsive {
			overflow-x: unset !important;
		}

		.badge-gray {
			background-color: #545b62;
			color: white;
		}

		.badge-buget {
			background-color: #04a5a5;
			color: white;
		}
	}
}
</style>
