<template>
	<div class="order-list">
		<div
			v-for="order in maintenances"
			:key="order.maintenance.id"
			class="container page-print"
			:style="styles.main"
		>
			<div
				class="ordem-page"
				style="
					padding: 0 5px 10px;
					color: #4b545c !important;
					border-bottom: dotted black 2px;
					min-height: 50%;
				"
			>
				<!-- BOX-1 -->
				<div class="box-1" style="margin: 0 2px">
					<div class="row align-items-center" :style="styles.border">
						<div class="col-md-3">
							<img
								style="max-width: 200px"
								src="/images/logo.png"
								class="img-responsive img-logo"
								alt="logo"
							/>
						</div>
						<div
							class="col-md-6 d-flex justify-content-between align-items-end"
							style="border-right: 1px solid #e4e7ed"
						>
							<div :style="styles.addressText">
								<b :style="styles.labelText">SHOPP CELL</b>
								<div>Rua Cel. Martinho Ferreira do Amaral, 232</div>
								<div>Nova Serrana - MG - Cep: 35520-122</div>
								<div>{{ cellphone }} - {{ email }}</div>
							</div>
							<b class="mb-2">1ª VIA - LOJA</b>
						</div>
						<div class="col-md-3 text-right">
							<div :style="styles.labelText">
								ORDEM SERVIÇO
								<div :style="styles.osNumberText">
									<span>{{ order.order.is_warranty ? "GARANTIA" : "" }}</span>
									<span>Nº {{ order.order.id }}</span>
								</div>
								EMISSÃO:
								{{ moment(order.created_at).format("DD/MM/YYYY HH:MM") }}
							</div>
						</div>
					</div>
				</div>
				<!-- END BOX-1 -->

				<div class="row" style="padding: 8px 10px" :style="styles.border">
					<div class="col-md-9 d-flex flex-column">
						<span :style="styles.primaryText">
							<b :style="styles.labelText">CLIENTE:</b>
							{{ getOrderClient(order) }}
						</span>
					</div>
					<div class="col-md-3 d-flex flex-column">
						<span :style="styles.primaryText">
							<b>CPF/CNPJ:</b> {{ getClientDocument(order) }}</span
						>
					</div>

					<div class="col-md-9 d-flex flex-column">
						<span :style="styles.primaryText"
							><b :style="styles.labelText">ENDEREÇO: </b
							>{{ getAddress(order) }}</span
						>
					</div>
					<div class="col-md-3 d-flex flex-column">
						<span :style="styles.primaryText"
							><b>FONE: </b>{{ getPhones(order).phone }}</span
						>
					</div>
					<div class="col-md-6 d-flex flex-column">
						<span :style="styles.primaryText"
							><b>MARCA/MODELO: </b>{{ getEquipBranAndModel(order) }}</span
						>
					</div>

					<div class="col-md-3 d-flex flex-column">
						<span :style="styles.primaryText"
							><b>CEP: </b>{{ getPostalCode(order) }}</span
						>
						<span :style="styles.primaryText"
							><b>STATUS: </b>{{ getStatusOrder(order) }}</span
						>
					</div>
					<div class="col-md-3 d-flex flex-column">
						<span :style="styles.primaryText"
							><b>CELULAR: </b>{{ getPhones(order).cellphone }}</span
						>
					</div>
					<div class="col-md-12 d-flex flex-column">
						<span :style="styles.primaryText" style="word-break: break-all"
							><b>DESCRIÇÃO DO PROBLEMA: </b>{{ order.maintenance.issue }}</span
						>
					</div>
					<div class="col-md-12 d-flex">
						<span
							:style="styles.primaryText"
							class="d-flex"
							style="word-break: break-all"
						>
							<b class="mr-2">CHECKLIST: </b>
							<span
								class="d-flex"
								v-for="(checklistItem, ckIndex) in order.product.checklists"
								:key="ckIndex"
							>
								<span class="mx-2">{{ ckIndex > 0 ? " - " : "" }}</span>

								<span class="d-flex align-items-center"
									><div
										style="
											width: 0.8rem;
											margin-right: 4px;
											height: 0.8rem;
											border: 1px solid black;
											border-radius: 9999px;
										"
									></div>
									{{ checklistItem.name }}</span
								>
							</span>
						</span>
					</div>
				</div>

				<div class="table-responsive-md">
					<table class="table table-bordered" :style="styles.table">
						<thead>
							<tr style="background-color: #f5f6f8 !important">
								<th scope="col" :style="styles.tableHead">PRODUTO/SERVIÇO</th>
								<th class="text-center" :style="styles.tableHead" scope="col">
									UNIDADE
								</th>
								<th class="text-center" :style="styles.tableHead" scope="col">
									QUANTIDADE
								</th>
								<th class="text-center" :style="styles.tableHead" scope="col">
									V.UNITÁRIO
								</th>
								<th class="text-center" :style="styles.tableHead" scope="col">
									DESCONTO
								</th>
								<th class="text-center" :style="styles.tableHead" scope="col">
									TOTAL
								</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(item, i) in order.by_products" :key="i">
								<td scope="row" :style="styles.tableHead">
									{{ item.product ? item.product.name : "" }}
								</td>
								<td
									class="text-center"
									style="text-transform: uppercase"
									:style="styles.tableContent"
								>
									{{ item.product ? item.product.type : "" }}
								</td>
								<td class="text-center" :style="styles.tableContent">
									{{ item.amount }}
								</td>
								<td class="text-center" :style="styles.tableContent">
									{{ item.price | currency }}
								</td>
								<td class="text-center" :style="styles.tableContent">
									{{ item.discount | currency }}
								</td>
								<td class="text-center" :style="styles.tableContent">
									{{ getTotalItemPrice(item) | currency }}
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- BOX-4 -->
				<div :style="styles.border" style="padding: 12px">
					<p class="mb-0" style="font-size: 14px">
						{{ maintenance_text }}
					</p>
				</div>
				<!-- END BOX-4 -->

				<!-- BOX-5 -->
				<div class="p-3" :style="styles.border">
					<div class="row">
						<div
							class="col-md-4 d-flex flex-column justify-content-between pb-3"
						>
							<h5 :style="styles.labelText">SENHA DO DISPOSITIVO</h5>
							<div style="border-bottom: 1px solid #e4e7ed" />
						</div>

						<div class="col-md-2 pt-3">
							<div>
								<svg
									width="67"
									height="49"
									viewBox="0 0 67 49"
									fill="none"
									xmlns="http://www.w3.org/2000/svg"
								>
									<circle cx="3.5" cy="3.5" r="3.5" fill="#BEC3CC" />
									<circle cx="33.5" cy="3.5" r="3.5" fill="#BEC3CC" />
									<circle cx="63.5" cy="3.5" r="3.5" fill="#BEC3CC" />
									<circle cx="3.5" cy="24.5" r="3.5" fill="#BEC3CC" />
									<circle cx="33.5" cy="24.5" r="3.5" fill="#BEC3CC" />
									<circle cx="63.5" cy="24.5" r="3.5" fill="#BEC3CC" />
									<circle cx="3.5" cy="45.5" r="3.5" fill="#BEC3CC" />
									<circle cx="33.5" cy="45.5" r="3.5" fill="#BEC3CC" />
									<circle cx="63.5" cy="45.5" r="3.5" fill="#BEC3CC" />
								</svg>
							</div>
						</div>

						<div class="col-md-6">
							<h5 :style="styles.labelText">ICLOUD/GMAIL</h5>
							<div class="d-flex mb-2">
								<div :style="styles.primaryText">EMAIL:</div>
								<span
									style="
										border-bottom: 1px solid #e4e7ed;
										width: 100%;
										margin: 0 0 4px 2px;
									"
								/>
							</div>
							<div class="d-flex">
								<div :style="styles.primaryText">SENHA:</div>
								<span
									style="
										border-bottom: 1px solid #e4e7ed;
										width: 100%;
										margin: 0 0 4px 2px;
									"
								/>
							</div>
						</div>
					</div>
				</div>
				<!-- END BOX-5 -->

				<div class="row" :style="styles.border">
					<div
						class="col-md-5 d-flex flex-column"
						style="
							padding: 10px 20px;
							border-bottom: 1px solid #e4e7ed;
							border-right: 1px solid #e4e7ed;
						"
					>
						<div class="d-flex align-items-center">
							<div :style="styles.labelText" style="margin-right: 4px">
								PREVISÃO ENTREGA:
							</div>
							<span :style="styles.primaryText">{{
								getEstimatedDelivery(order)
							}}</span>
						</div>
						<div class="d-flex align-items-center">
							<div :style="styles.labelText" style="margin-right: 4px">
								COND.PAGAMENTO:
							</div>
							<span :style="styles.primaryText">{{
								getTypePayment(order)
							}}</span>
						</div>
					</div>
					<div
						class="col-md-2 w-100 d-flex flex-column align-items-center"
						style="
							padding: 10px 0;
							border-bottom: 1px solid #e4e7ed;
							border-right: 1px solid #e4e7ed;
						"
					>
						<div>
							<div :style="styles.textValue">SUBTOTAL</div>
							<span :style="styles.values">{{
								getOrderTotals(order).subtotal | currency
							}}</span>
						</div>
					</div>
					<div
						class="col-md-2 w-100 d-flex flex-column align-items-center"
						style="
							padding: 10px 0;
							border-bottom: 1px solid #e4e7ed;
							border-right: 1px solid #e4e7ed;
						"
					>
						<div>
							<div :style="styles.textValue">DESCONTO</div>
							<span :style="styles.values">{{
								getOrderTotals(order).discount | currency
							}}</span>
						</div>
					</div>
					<!-- <div
						class="col-md-2 d-flex flex-column align-items-center"
						style="padding: 10px 0; border-bottom: 1px solid #E4E7ED; border-right: 1px solid #E4E7ED;"
					>
						<div>
							<div :style="styles.textValue">FRETE</div>
							<span :style="styles.values">R$ 0,00</span>
						</div>
					</div> -->
					<div
						class="col-md-3 w-100 d-flex flex-column align-items-center"
						style="padding: 10px 0; border-bottom: 1px solid #e4e7ed"
					>
						<div>
							<div :style="styles.textValue">TOTAL</div>
							<span :style="styles.values">{{
								getOrderTotals(order).total | currency
							}}</span>
						</div>
					</div>
					<div
						class="row w-100 d-flex align-items-center"
						style="padding: 15px 10px 10px"
					>
						<div class="col-md-4 text-left" :style="styles.primaryText">
							Nova Serrana, {{ moment(currentdate).format("LLL") }}
						</div>
						<div class="d-flex flex-column col-md-8 align-items-center">
							<span
								style="
									border-bottom: 1px solid #e4e7ed;
									min-width: 100%;
									margin: 20px 0 4px;
								"
							/>
							<div class="text-center" :style="styles.primaryText">
								{{ `${getOrderClient(order)} - ${getClientDocument(order)}` }}
							</div>
						</div>
						<!-- <div class="col-md-4 text-right" :style="styles.primaryText">
							Impresso
							{{
								`${moment(currentdate).format("DD/MM/YYYY HH:MM")} por ${
									user.name
								}`
							}}
						</div> -->
					</div>
				</div>
			</div>
			<!--  2 VIA CLIENTE -->
			<div
				class="ordem-page"
				style="padding: 0 5px; color: #4b545c !important; margin-top: 15px"
			>
				<!-- BOX-1 -->
				<div class="box-1" style="margin: 0 2px">
					<div class="row align-items-center" :style="styles.border">
						<div class="col-md-3">
							<img
								style="max-width: 200px"
								src="/images/logo.png"
								class="img-responsive img-logo"
								alt="logo"
							/>
						</div>
						<div
							class="col-md-6 d-flex justify-content-between align-items-end"
							style="border-right: 1px solid #e4e7ed"
						>
							<div :style="styles.addressText">
								<b :style="styles.labelText">SHOPP CELL</b>
								<div>
									{{ address }}
								</div>
								<div>{{ cellphone }} - {{ email }}</div>
							</div>
							<b class="mb-2">2ª VIA - CLIENTE</b>
						</div>
						<div class="col-md-3 text-right">
							<div :style="styles.labelText">
								ORDEM SERVIÇO
								<div :style="styles.osNumberText">Nº {{ order.order.id }}</div>
								EMISSÃO:
								{{ moment(order.created_at).format("DD/MM/YYYY HH:MM") }}
							</div>
						</div>
					</div>
				</div>
				<!-- END BOX-1 -->

				<div class="row" style="padding: 8px 10px" :style="styles.border">
					<div class="col-md-6 d-flex flex-column">
						<span :style="styles.primaryText">
							<b :style="styles.labelText">CLIENTE:</b>
							{{ getOrderClient(order) }}
						</span>
					</div>
					<div class="col-md-3 d-flex flex-column">
						<span :style="styles.primaryText">
							<b>CPF/CNPJ:</b> {{ getClientDocument(order) }}</span
						>
					</div>
					<div class="col-md-3 d-flex flex-column">
						<span :style="styles.primaryText"
							><b>VENDEDOR: </b>{{ getSellerName(order) }}</span
						>
					</div>

					<div class="col-md-9 d-flex flex-column">
						<span :style="styles.primaryText"
							><b :style="styles.labelText">ENDEREÇO: </b
							>{{ getAddress(order) }}</span
						>
					</div>
					<div class="col-md-3 d-flex flex-column">
						<span :style="styles.primaryText"
							><b>FONE: </b>{{ getPhones(order).phone }}</span
						>
					</div>
					<div class="col-md-6 d-flex flex-column">
						<span :style="styles.primaryText"
							><b>MARCA/MODELO: </b>{{ getEquipBranAndModel(order) }}</span
						>
					</div>

					<div class="col-md-3 d-flex flex-column">
						<span :style="styles.primaryText"
							><b>STATUS: </b>{{ getStatusOrder(order) }}</span
						>
					</div>
					<div class="col-md-3 d-flex flex-column">
						<span :style="styles.primaryText"
							><b>CELULAR: </b>{{ getPhones(order).cellphone }}</span
						>
					</div>
					<div class="col-md-12 d-flex flex-column">
						<span :style="styles.primaryText" style="word-break: break-all"
							><b>DESCRIÇÃO DO PROBLEMA: </b>{{ order.maintenance.issue }}</span
						>
					</div>
					<div class="col-md-12 d-flex flex-column">
						<span
							class="d-flex"
							:style="styles.primaryText"
							style="word-break: break-all"
						>
							<b>CHECKLIST: </b>
							<span
								class="d-flex"
								v-for="(checklistItem, ckIndex) in order.product.checklists"
								:key="ckIndex"
								><span class="mx-2">{{ ckIndex > 0 ? " - " : "" }}</span>

								<span class="d-flex align-items-center"
									><div
										style="
											width: 0.8rem;
											margin-right: 4px;
											height: 0.8rem;
											border: 1px solid black;
											border-radius: 9999px;
										"
									></div>
									{{ checklistItem.name }}</span
								>
							</span>
						</span>
					</div>
				</div>

				<div class="table-responsive-md">
					<table class="table table-bordered" :style="styles.table">
						<thead>
							<tr style="background-color: #f5f6f8 !important">
								<th scope="col" :style="styles.tableHead">PRODUTO/SERVIÇO</th>
								<th class="text-center" :style="styles.tableHead" scope="col">
									UNIDADE
								</th>
								<th class="text-center" :style="styles.tableHead" scope="col">
									QUANTIDADE
								</th>
								<th class="text-center" :style="styles.tableHead" scope="col">
									V.UNITÁRIO
								</th>
								<th class="text-center" :style="styles.tableHead" scope="col">
									DESCONTO
								</th>
								<th class="text-center" :style="styles.tableHead" scope="col">
									TOTAL
								</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(item, i) in order.by_products" :key="i">
								<th scope="row" :style="styles.tableHead">
									{{ item.product ? item.product.name : "" }}
								</th>
								<td
									class="text-center"
									style="text-transform: uppercase"
									:style="styles.tableContent"
								>
									{{ item.product ? item.product.type : "" }}
								</td>
								<td class="text-center" :style="styles.tableContent">
									{{ item.amount }}
								</td>
								<td class="text-center" :style="styles.tableContent">
									{{ item.price | currency }}
								</td>
								<td class="text-center" :style="styles.tableContent">
									{{ item.discount | currency }}
								</td>
								<td class="text-center" :style="styles.tableContent">
									{{ getTotalItemPrice(item) | currency }}
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- BOX-4 -->
				<div :style="styles.border" style="padding: 12px">
					<p class="mb-0" style="font-size: 14px">
						{{ maintenance_text }}
					</p>
				</div>
				<!-- END BOX-4 -->

				<div class="row" :style="styles.border">
					<div
						class="col-md-5 d-flex flex-column"
						style="
							padding: 10px 20px;
							border-bottom: 1px solid #e4e7ed;
							border-right: 1px solid #e4e7ed;
						"
					>
						<div class="d-flex align-items-center">
							<div :style="styles.labelText" style="margin-right: 4px">
								PREVISÃO ENTREGA:
							</div>
							<span :style="styles.primaryText">{{
								getEstimatedDelivery(order)
							}}</span>
						</div>
						<div class="d-flex align-items-center">
							<div :style="styles.labelText" style="margin-right: 4px">
								COND.PAGAMENTO:
							</div>
							<span :style="styles.primaryText">{{
								getTypePayment(order)
							}}</span>
						</div>
					</div>
					<div
						class="col-md-2 d-flex flex-column align-items-center"
						style="
							padding: 10px 0;
							border-bottom: 1px solid #e4e7ed;
							border-right: 1px solid #e4e7ed;
						"
					>
						<div>
							<div :style="styles.textValue">SUBTOTAL</div>
							<span :style="styles.values">{{
								getOrderTotals(order).subtotal | currency
							}}</span>
						</div>
					</div>
					<div
						class="col-md-2 d-flex flex-column align-items-center"
						style="
							padding: 10px 0;
							border-bottom: 1px solid #e4e7ed;
							border-right: 1px solid #e4e7ed;
						"
					>
						<div>
							<div :style="styles.textValue">DESCONTO</div>
							<span :style="styles.values">{{
								getOrderTotals(order).discount | currency
							}}</span>
						</div>
					</div>
					<!-- <div
						class="col-md-2 d-flex flex-column align-items-center"
						style="padding: 10px 0; border-bottom: 1px solid #E4E7ED; border-right: 1px solid #E4E7ED;"
					>
						<div>
							<div :style="styles.textValue">FRETE</div>
							<span :style="styles.values">R$ 0,00</span>
						</div>
					</div> -->
					<div
						class="col-md-3 d-flex flex-column align-items-center"
						style="padding: 10px 0; border-bottom: 1px solid #e4e7ed"
					>
						<div>
							<div :style="styles.textValue">TOTAL</div>
							<span :style="styles.values">{{
								getOrderTotals(order).total | currency
							}}</span>
						</div>
					</div>
					<div
						class="row w-100 d-flex align-items-center"
						style="padding: 15px 10px 10px"
					>
						<div class="col-md-4 text-left" :style="styles.primaryText">
							Nova Serrana, {{ moment(currentdate).format("LLL") }}
						</div>
						<div class="d-flex flex-column col-md-8 align-items-center">
							<span
								style="
									border-bottom: 1px solid #e4e7ed;
									min-width: 100%;
									margin: 20px 0 4px;
								"
							/>
							<div class="text-center" :style="styles.primaryText">
								{{ `${getOrderClient(order)} - ${getClientDocument(order)}` }}
							</div>
						</div>
						<!-- <div class="col-md-4 text-right" :style="styles.primaryText">
							Impresso
							{{
								`${moment(currentdate).format("DD/MM/YYYY HH:MM")} por ${
									user.name
								}`
							}}
						</div> -->
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import moment from "moment";
import _ from "lodash";

export default {
	header: {
		link: {
			href: "https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap",
			rel: "stylesheet",
		},
	},

	props: {
		maintenances: {
			type: Array,
			required: true,
		},
		sellers: {
			type: Array,
			required: true,
		},
		user: {
			type: Object,
			required: true,
		},
		maintenance_text: {
			type: String,
			required: true,
		},
		cellphone: {
			type: String,
			required: true,
		},
		email: {
			type: String,
			required: true,
		},
		address: {
			type: String,
			required: true,
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

	watch: {
		maintenances() {
			this.currentdate = new Date();
		},
	},

	data() {
		return {
			moment: moment,
			orderStatus: {
				waiting_approval: "Aguardando aprovação",
				waiting_payment: "Aguardando pagamento",
				approved: "Aprovado",
				waiting_product: "Aguardando Produto",
				concluded: "Concluído",
				canceled: "Cancelado",
				no_maintenance: "Sem conserto",
				waiting_stock: "Aguardando Produto",
				finished: "Aguardando pagamento",
				maintenance: "Em manutenção",
				fixed: "Concertado",
			},
			currentdate: new Date(),
			styles: {
				main: `
					@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap');
				`,
				border: `
					border: 1px solid #E4E7ED;
					border-radius: 4px;
					margin: 0 2px 10px;`,
				borderBottom: `
					border-bottom: 1px solid #E4E7ED;`,
				borderRight: `
					border-right: 1px solid #E4E7ED;`,
				primaryText: `
					font-family: 'Roboto', sans-serif;
					font-size: 14px;
					line-height: 20px;
					letter-spacing: 0.01em;`,
				labelText: `
					font-family: 'Roboto', sans-serif;
					font-weight: bold;
					font-size: 14px;
					line-height: 16px;
					letter-spacing: 0.01em;`,
				osNumberText: `
					font-family: 'Roboto', sans-serif;
					font-weight: bold;
					font-size: 21px;
					line-height: 25px;
					letter-spacing: 0.01em;`,
				addressText: `
					font-family: 'Roboto', sans-serif;
					font-weight: 300;
					font-size: 16px;
					line-height: 19px;
					letter-spacing: 0.01em;
					padding: 5px 0;
				`,
				textValue: `
					font-family: 'Roboto', sans-serif;
					font-weight: bold;
					font-size: 11px;
					line-height: 13px;
					letter-spacing: 0.01em;
				`,
				values: `
					font-family: 'Roboto', sans-serif;
					font-weight: bold;
					font-size: 16px;
					line-height: 19px;
					letter-spacing: 0.01em;
				`,
				table: `
					border: 1px solid #E4E7ED;
					border-radius: 4px;
					margin: 0 2px 10px;
				`,
				tableHead: `
					padding: 0.25rem 0.5rem;
				`,
				tableContent: `
					padding: 0.25rem 0.5rem;
				`,
			},
		};
	},

	methods: {
		getOrderClient(params) {
			const { client } = params.order;
			if (client && client.full_name)
				return `${client.id} - ${client.full_name}`;
			return "Desconhecido";
		},
		getClientDocument(params) {
			const { client } = params.order;
			if (client && client.cpf) return client.cpf;
			else if (client && client.cnpj) return client.cnpj;
			else return "Sem documento";
		},
		getEquipBranAndModel(order) {
			const { maintenance } = order;
			if (maintenance && maintenance.brand && maintenance.brand_model)
				return `${maintenance.brand.name} / ${maintenance.brand_model.name}`;
			else if (maintenance && maintenance.brand) return maintenance.brand.name;
			else return "Desconhecido";
		},
		getTotalItemPrice(item) {
			let itemPrice = item.product ? item.product.price : item.price;
			return (
				parseFloat(itemPrice) +
				parseFloat(item.addition) -
				parseFloat(item.discount) * item.amount
			);
		},
		getSellerName(params) {
			const { seller_id } = params.order;
			if (seller_id)
				return this.sellers.find((seller) => seller.id === seller_id).name;
			return "Desconhecido";
		},
		getAddress(params) {
			try {
				const { address: adrs } = params.order.client;
				let addresFields = [];
				if (adrs.street) addresFields.push(adrs.street);
				if (adrs.number) addresFields.push(adrs.number);
				if (adrs.complement) addresFields.push(adrs.complement);
				if (adrs.neighborhood) addresFields.push(adrs.neighborhood);
				const addrsLine1 = _.join(addresFields, ", ");
				addresFields = [];
				if (adrs.city) addresFields.push(adrs.city);
				if (adrs.state) addresFields.push(adrs.state);
				if (adrs.postcode) addresFields.push(adrs.postcode);
				const addrsLine2 = _.join(addresFields, " - ");
				return `${addrsLine1} - ${addrsLine2}`;
			} catch (e) {
				return "Não cadastrado";
			}
		},
		getCity(params) {
			try {
				const { address: adrs } = params.order.client;
				if (adrs.city && adrs.state) return `${adrs.city}/${adrs.state}`;
				return adrs.city ? adrs.city : adrs.state;
			} catch (e) {
				return "Não cadastrado";
			}
		},
		getPostalCode(params) {
			try {
				const { address: adrs } = params.order.client;
				return adrs.postcode.replace(new RegExp(/(\d{5})(\d{3})/), "$1-$2");
			} catch (e) {
				return "Não cadastrado";
			}
		},
		getPhones(params) {
			try {
				const { phone, cellphone } = params.order.client;

				return {
					phone,
					cellphone,
				};
			} catch (e) {
				console.error("Phone", e);
				return "Não cadastrado";
			}
		},
		getStatusOrder(params) {
			try {
				const { status } = params.order;
				return this.orderStatus[status];
			} catch (e) {
				return "Status desconhecido";
			}
		},
		getEstimatedDelivery(order) {
			try {
				const { due_date } = order.maintenance;
				return due_date ? moment(due_date).format("DD/MM/YYYY") : "A definir";
			} catch (e) {
				return "A definir";
			}
		},
		getTypePayment(params) {
			try {
				const { payments } = params.order;
				if (_.isEmpty(payments)) return "A definir";
				else {
					// logica de pagamentos
				}
			} catch (e) {
				return "A definir";
			}
		},
		getOrderTotals(params) {
			try {
				const { subtotal, discount, total } = params.order;
				return {
					subtotal: parseFloat(subtotal) + parseFloat(discount),
					discount,
					total,
				};
			} catch (e) {
				return "R$ -";
			}
		},
	},
};
</script>

<style></style>
