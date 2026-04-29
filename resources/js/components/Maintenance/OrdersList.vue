<template>
	<div class="order-list-maintenance">
		<div class="row">
			<div class="col-md-5">
				<div class="input-group">
					<input
						type="text"
						class="form-control border-right-0"
						id="client"
						placeholder="Busca por manutenções"
						v-model="maintenanceSearch"
						@keyup="getMaintenanceOrders()"
					/>
					<div class="input-group-append">
						<span class="input-group-text bg-white border-left-0">
							<i class="fa fa-search"></i>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<multiselect
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
					placeholder="Selectione o status"
					label="text"
					track-by="text"
					:preselect-first="true"
				>
					<template slot="selection" slot-scope="{ values, isOpen }"
						><span class="multiselect__single" v-if="values.length && !isOpen"
							>{{ selectedStatus.length }} Status selecionados</span
						></template
					>
				</multiselect>
			</div>
			<div class="col-md-3">
				<flat-pickr
					ref="calendar"
					style="font-size: 14px"
					v-model="dateSelected"
					class="form-control mr-2"
					placeholder="Filtrar por período"
					:config="config"
					@input="setRangeDate"
				>
				</flat-pickr>
			</div>
		</div>

		<div class="row mt-4 pl-2">
			<div class="table-responsive">
				<table class="text-sm table table-hover" style="max-height: 700px">
					<thead class="text-secondary">
						<tr>
							<th class="th__id" scope="col">ID</th>
							<th class="th__client" scope="col">
								<span>Cliente</span>
							</th>
							<th class="th__product" scope="col">
								<span>Produto</span>
							</th>
							<th class="th__details" scope="col">
								<span>Detalhes do problema</span>
							</th>
							<th class="th__technician" scope="col">
								<span>Técnico</span>
							</th>
							<th class="th__status" scope="col">
								<span>Status</span>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr
							v-for="o in orders"
							:key="o.id"
							@click="showOrderDetails(o)"
							class="table-row"
							:class="{
								'selected-row': o && currentOrder && o.id === currentOrder.id,
								wait_stock: o.maintenance.os_status == 'waiting_stock',
								outdate:
									o.maintenance.due_date &&
									moment(o.maintenance.due_date) < moment(),
							}"
						>
							<td class="td__id">
								<span>{{ o.id }}</span>
							</td>
							<td class="td__client" :title="o.order.client.full_name">
								<span>{{ o.order.client.full_name.split(" ")[0] }}</span>
							</td>
							<td class="td__product" :title="getBrandName(o)">
								<span
									>{{
										(
											getBrandName(o) +
											" " +
											o.maintenance.brand_model.name
										).substring(0, 15)
									}}{{
										getBrandName(o).length +
											o.maintenance.brand_model.name.length >
										15
											? "..."
											: ""
									}}</span
								>
							</td>
							<td class="td__details" :title="o.maintenance.issue">
								<span
									>{{
										o.maintenance.issue
											? o.maintenance.issue.substring(0, 10)
											: ""
									}}{{ o.maintenance.issue.length > 10 ? "..." : "" }}</span
								>
							</td>
							<td class="td__technician">
								<span>{{
									handleUser(o).name ? o.user.name.split(" ")[0] : "--"
								}}</span>
							</td>
							<td class="td__status">
								<span
									class="btn micro-button"
									:class="{
										'btn-dark': o.maintenance.os_status == 'waiting_approval',
										'btn-danger': o.maintenance.os_status == 'no_maintenance',
										'btn-primary': o.maintenance.os_status == 'finished',
										'btn-success': o.maintenance.os_status == 'fixed',
										'btn-warning': o.maintenance.os_status == 'approved',
										'btn-secondary': o.maintenance.os_status == 'waiting_stock',
										'btn-info': o.maintenance.os_status == 'maintenance',
									}"
								>
									{{
										o.maintenance
											? status[o.maintenance.os_status]
											: status["waiting_approval"]
									}}
								</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</template>

<script>
import axios from "axios";
import moment from "moment";
import Datepicker from "vuejs-datepicker";
import { ptBR } from "vuejs-datepicker/dist/locale";
import { Portuguese } from "flatpickr/dist/l10n/pt";
import Multiselect from "vue-multiselect";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import toastr from "toastr";
import "toastr/build/toastr.min.css";

export default {
	name: "OrdersList",

	components: { Datepicker, Multiselect, flatPickr },

	props: {
		currentOrder: {
			type: Object,
			default: () => {},
		},
	},

	data() {
		return {
			moment: moment,
			dateSelected: null,
			showCalendar: false,
			portuguese: ptBR,
			orders: [],
			maintenanceSearch: null,
			status: {
				waiting_approval: "Aguardando aprovação do cliente",
				approved: "Aprovado pelo Cliente",
				waiting_stock: "Aguardando peça do fornecedor",
				maintenance: "Em manutenção",
				no_maintenance: "Sem conserto",
				fixed: "Finalizado/consertado",
				finished: "Enviado para recebimento no Caixa",
			},
			selectedStatus: [
				{ text: "Aprovado", value: "approved" }, // Cliente liberou para que faça a manutenção || tela::PDV
				{ text: "Aguardando peça do fornecedor", value: "waiting_stock" }, //Aguardando peça do fornecedor || tela::Manutençao
				{ text: "Em manutenção", value: "maintenance" }, //Em manutenção || tela::Manutençao
			],
			listStatus: [
				{ text: "Aprovado", value: "approved" }, // Cliente liberou para que faça a manutenção || tela::PDV
				{ text: "Aguardando peça do fornecedor", value: "waiting_stock" }, //Aguardando peça do fornecedor || tela::Manutençao
				{ text: "Em manutenção", value: "maintenance" }, //Em manutenção || tela::Manutençao
				{ text: "Aguardando aprovação cliente", value: "waiting_approval" }, // Aguardando aprovação do cliente || tela::PDV || listagem de ordens
				{ text: "Sem conserto", value: "no_maintenance" }, //sem concerto || tela::Manutençao || listagem de ordens
				{ text: "Consertado", value: "fixed" }, //finalizado/consertado || tela::Manutençao || listagem de ordens
				{ text: "Finalizado", value: "finished" }, // enviado para recebimento no Caixa || tela::PDV só vai aparecer após o liberação do técnico
				{ text: "Sem conserto e entregue", value: "no_maintenance_delivered" }, // Sem conserto e entregue
			],
			dateIni: moment().subtract(7, "d").format("YYYY-MM-DD 00:00:00"),
			dateFim: moment().format("YYYY-MM-DD 00:00:00"),
			dateSelected: `${moment()
				.subtract(7, "d")
				.format("YYYY-MM-DD 00:00:00")} até ${moment().format(
				"YYYY-MM-DD 00:00:00"
			)}`,
			config: {
				mode: "range",
				wrap: true, // set wrap to true only when using 'input-group'
				// dateFormat: 'd-m-Y'
				altFormat: "d/m/y",
				altInput: true,
				dateFormat: "Y-m-d H:i:S",
				locale: Portuguese,
				minDate: this.startDate,
				maxDate: this.maxDate,
			},
			callStackUpdateCalendar: 0,
			users: [],
		};
	},

	computed: {
		currentStatusSelected() {
			return this.selectedStatus.map((status) => status.value);
		},
	},

	async mounted() {
		await this.getTechnicians(true);
		await this.getMaintenanceOrders([
			"approved",
			"waiting_stock",
			"maintenance",
		]);
		//2021-04-28 00:00:00
		this.dateIni = moment().subtract(7, "d").format("YYYY-MM-DD 00:00:00");
		this.dateFim = moment().format("YYYY-MM-DD 00:00:00");
	},

	methods: {
		openPicker() {
			this.showCalendar = true;
			this.$refs.datePicker.showCalendar();
		},

		clearPicker() {
			this.showCalendar = false;
		},
		showOrderDetails(order) {
			this.$emit("showOrder", order);
		},
		handleUser(order) {
			let user = null;
			user = this.users.find((user) => user.id == order.maintenance.user_id);
			order.user = user;

			return user ? user : {};
		},
		async getTechnicians() {
			const config = {
				params: {
					type: "vue",
					paginate: false,
					onlyTechnicians: true,
					withAdmin: true,
				},
			};

			let url = "/users";

			try {
				let response = await axios.get(url, config);
				this.users = response.data;
			} catch (error) {
				console.error(error);
			}
		},
		getMaintenanceOrders: _.debounce(async function (
			status = this.currentStatusSelected,
			dataIni,
			dataFim
		) {
			const params = {
				paginate: false,
				type: "vue",
				query_status: status,
				search: this.maintenanceSearch,
				order_field: "orders.id",
				order: "desc",
				date_ini: dataIni ? dataIni : this.dateIni,
				date_fim: dataFim ? dataFim : this.dateFim,
			};

			await axios
				.get("/maintenances", { params })
				.then(({ data }) => {
					this.orders = data.filter((o) => o.maintenance); // Pega apenas os pedidos que possuem manutenção
					if (dataIni && dataFim)
						toastr.success("Orçamentos filtrados com sucesso!");
				})
				.catch((e) => console.error("Error on get Maintenance", e));
		},
		200),
		changeStatus(values) {
			const status = values.map((status) => status.value);
			this.getMaintenanceOrders(status, this.dateIni, this.dateFim);
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

			if (this.callStackUpdateCalendar % 2 === 0) {
				const status = this.selectedStatus.map((status) => status.value);

				this.getMaintenanceOrders(status, this.dateIni, this.dateFim);
			}
		},
		getBrandName(order) {
			try {
				return order.maintenance.brand.name;
			} catch (e) {
				return "";
			}
		},
	},
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss">
.order-list-maintenance {
	.multiselect {
		font-size: 14px !important;
		&__tags-wrap {
			display: none !important;
		}
		&__option {
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
		&__single {
			font-size: 14px;
		}
	}
	.micro-button {
		font-size: 10px;
		font-weight: 500;
		padding: 6px 8px;
		border-radius: 4px;
	}

	.wait_stock {
		background-color: #ff572266;
	}

	.outdate {
		background-color: #f4433675;
	}

	tr {
		cursor: pointer;
	}

	.table-responsive {
		max-height: 700px;
		min-height: 500px;
	}

	.th,
	.td {
		&__id,
		&__client,
		&__details,
		&__product,
		&__technician,
		&__delivery,
		&__status {
			span {
				display: block;
				overflow: hidden;
				white-space: nowrap;
				text-overflow: ellipsis;
			}
		}
	}

	.table-row {
		&:not(.selected-row):hover {
			background: #efefef;
		}
		&.selected-row {
			background-color: #e2e2e2;
		}
	}

	.form-control {
		&.mr-2 {
			&.input {
				font-size: 13px;
			}
		}
	}
}
</style>
