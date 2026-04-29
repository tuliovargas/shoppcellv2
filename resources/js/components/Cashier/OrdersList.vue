<template>
	<div class="row">
		<div class="form-group w-100 mb-0">
			<div class="row">
				<div class="col-6">
					<input
						type="text"
						class="form-control"
						id="client"
						@keyup="getOrders"
						v-model="clientName"
						placeholder="Buscar por cliente"
					/>
				</div>
				<div class="col-5 d-flex justify-content-end align-items-center">
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
				<div class="col-1 d-flex justify-content-end">
					<button class="btn" @click="reset()">
						<span>
							<i class="fas fa-redo-alt"></i>
						</span>
					</button>
				</div>
			</div>
			<div class="row mt-4">
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th scope="col" v-if="dateFim !== dateIni">Data</th>
								<th scope="col">Cliente</th>
								<th scope="col" v-if="dateFim === dateIni">Hora</th>
								<th scope="col">Vendedor</th>
								<th scope="col">Total</th>
								<th scope="col">Status</th>
							</tr>
						</thead>
						<tbody>
							<tr
								v-for="order in orders"
								:key="order.id"
								@click="handleOrderSelected(order)"
								class="table-row-orders"
								:class="{
									'danger-row': order.status === 'waiting_product',
									'grey-row':
										order.cashier_info_id &&
										order.cashier_info_id != cashierInfo.id,
								}"
							>
								<td v-if="dateFim !== dateIni" class="td__created_at">
									<span>{{
										moment(order.created_at).format("DD/MM/YYYY HH:mm")
									}}</span>
								</td>
								<td :title="order.client.full_name" class="td__client-name">
									<span>{{ order.client.full_name }}</span>
								</td>
								<td class="td__cpf-user" v-if="dateFim === dateIni">
									<span>{{ moment(order.created_at).format("HH:mm") }}</span>
								</td>
								<td class="td__user-name">
									<span>{{
										order.seller ? order.seller.name.split(" ")[0] : null
									}}</span>
								</td>
								<td class="td__total">
									<span>{{ formatReal(order.total) }}</span>
								</td>
								<td class="td__status">
									<span
										class="right badge status-button h-100"
										:class="[
											{
												'badge-success': order.status === 'concluded',
											},
											{
												'badge-danger':
													order.status === 'canceled' ||
													order.status === 'returned',
											},
											{
												'badge-warning':
													order.status === 'waiting_product' ||
													order.status === 'waiting_payment',
											},
											{ 'badge-orange': order.status === 'partially_returned' },
										]"
									>
										{{ getStatusName(order.status) }}
									</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import axios from "axios";
import "flatpickr/dist/flatpickr.css";
import { Portuguese } from "flatpickr/dist/l10n/pt";
import moment from "moment";
import toastr from "toastr";
import flatPickr from "vue-flatpickr-component";
import { formatReal } from "../helpers/number";

export default {
	name: "OrdersList",

	components: { flatPickr },

	props: {
		cashierInfo: Object,
	},

	data() {
		return {
			orders: "",
			orderSelected: "",
			paymentMethod: "",
			clientName: "",
			dateSelected: `${moment().format(
				"YYYY-MM-DD 00:00:00"
			)} até ${moment().format("YYYY-MM-DD 00:00:00")}`,
			moment: moment,
			dateIni: moment().format("YYYY-MM-DD 00:00:00"),
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

	mounted() {
		this.getOrders();
		this.update();
	},

	computed: {
		startDate() {
			this.dateIni ? this.dateIni : moment();
		},
		maxDate() {
			this.dateIni ? moment(this.dateIni) : moment().add(30, "d");
		},
	},

	methods: {
		formatReal,
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

			if (this.callStackUpdateCalendar % 2 === 0) this.getOrders("update");
		},
		update() {
			this.getOrders(true);
		},
		reset() {
			this.dateSelected = `${moment().format(
				"YYYY-MM-DD 00:00:00"
			)} até ${moment().format("YYYY-MM-DD 00:00:00")}`;
			this.dateIni = moment().format("YYYY-MM-DD 00:00:00");
			this.dateFim = moment().format("YYYY-MM-DD 00:00:00");
			this.getOrders("update");
		},
		getOrders: _.debounce(async function (status) {
			const config = {
				params: {
					paginate: false,
					clientName: this.clientName,
					no_maintenance: true,
					no_orcamento: true,
					no_canceled: true,
					order_by: "created_at",
					order: "desc",
					date_ini: status ? this.dateIni : null,
					date_fim: status ? this.dateFim : null,
				},
			};

			let url = "/orders";

			try {
				let response = await axios.get(url, config);
				this.orders = response.data;
				if (status === "update")
					toastr.success("Lista de vendas atualizada com sucesso!");
				if (status) this.callStackUpdateCalendar++;
			} catch (error) {
				console.error(error);
				toastr.error("Houve um problema em buscar as ordens");
			}
		}, 500),

		handleOrderSelected(order) {
			if (order.status === "canceled" || order.status === "returned") return;
			this.$emit("sale", { order, formSelected: 1 });
		},

		getStatusName(status) {
			let label = "";
			switch (status) {
				case "waiting_payment":
					label = "Aguardando pagamento";
					break;
				case "concluded":
					label = "Concluído";
					break;
				case "canceled":
					label = "Cancelado";
					break;
				case "partially_returned":
					label = "Devolução parcial";
					break;
				case "returned":
					label = "Devolvido";
					break;
				case "waiting_product":
					label = "Aguardando produto";
					break;
			}
			return label;
		},
	},
};
</script>

<style lang="scss">
tr {
	cursor: pointer;
}

.table-responsive {
	max-height: 670px;
}

.danger-row {
	background: #fdccfd;
}

.grey-row {
	background: #cccccc;
}

.table-row-orders {
	&:hover {
		background: #defdfd;

		&.danger-row {
			background: #fdfdee;
		}
	}
}

.vdp-datepicker {
	div {
		input {
			max-width: 100px !important;
		}
	}
}

.btn-calendar {
	background: #fbfcfc;
	border: 1px solid #e4e7ed;
	border-radius: 4px;

	span {
		font-size: 20px;
		color: #0983e8;
	}
}

.td {
	&__client-name,
	&__cpf-user,
	&__user-name,
	&__pay-method,
	&__total,
	&__status {
		span {
			display: block;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
	}

	&__client-name {
		width: 20%;

		span {
			max-width: 170px;
			width: 100%;
		}
	}
}

.badge-orange {
	color: #1f2d3d;
	background-color: #ff9307;
}

/* thead, tbody{
  display: block;
}

tbody{
  overflow-y: scroll;
  max-height: 200px;
  } */
</style>
