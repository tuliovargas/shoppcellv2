<template>
	<div class="row">
		<card
			v-for="(card, index) of cards"
			:key="index"
			:color="card.color"
			:title="card.title"
			:footer="card.footer"
			footer-vertical-padding="py-1"
			:fullHeight="true"
		>
			<div class="row">
				<div class="col-12 col-md-8">
					<h3 class="font-weight-bold">
						{{ card.title }}
					</h3>
					<p v-html="card.body"></p>
				</div>
				<div class="col-12 col-md-4 text-right">
					<i :class="card.icon" class="card-icon fa-4x"></i>
				</div>
			</div>
		</card>
	</div>
</template>

<style>
.card-icon {
	color: #4746484f;
}
</style>

<script>
import {
	getTotalClients,
	getRegisteredClientsYesterday,
} from "../../services/client";
import {
	getTotalMaintenance,
	getOverdueMaintenances,
} from "../../services/maintenance";
import { getCashierInfo } from "../../services/cashier";
import Card from "../Resources/Card";
import { getLowStock } from "../../services/product";
import moment from "moment";

export default {
	components: {
		Card,
	},
	data() {
		return {
			clients: {
				total: null,
				yesterday: null,
			},
			maintenances: {
				total: 0,
			},
			caixas: [],
			caixa: {},
			cards: [],
			products: {},
		};
	},
	methods: {
		getProductsList() {
			return this.products.products
				.map((product) => {
					return `<li class="my-1">${product.quantity_in_stock} - ${product.name}</li>`;
				})
				.join("");
		},
		getTotalSales() {
			return this.caixa.earnings[this.caixa.earnings.length - 1].quantity_sales;
		},
		getYesterdaySales() {
			let earnings = 0;
			const yesterday = moment().subtract(1, "days");
			this.caixas.forEach((el) => {
				let caixaClosed = moment(el.close_time);
				if (
					caixaClosed.day() == yesterday.day() &&
					yesterday.month() == caixaClosed.month()
				) {
					earnings += el.earnings[el.earnings.length - 1].quantity_sales;
				}
			});
			return earnings;
		},
		getCaixa() {
			this.caixas = Array.isArray(this.caixa) ? this.caixa : [this.caixa];
			this.caixa = Array.isArray(this.caixa) ? this.caixa[0] : this.caixa;
			if (!this.caixa.created_at) {
				return {
					title: "Caixa Fechado",
					color: "bg-info",
					body: "Não existem registros de caixa no banco de dados",
					icon: " fa fa-cash-register",
				};
			} else {
				return {
					title: !this.caixa.close_time ? "Caixa Aberto" : "Caixa Fechado",
					color: "bg-info",
					body: !this.caixa.close_time
						? `<ul class="list-unstyled">
          <li>Por ${this.caixa.user.name} - ${new Date(
								this.caixa.created_at
						  ).toLocaleString("pt-BR")}</li>
          <li><b>Total vendas:</b> ${this.getTotalSales()}</li>
          </ul>`
						: `<ul class="list-unstyled">
          <li class="my-1"><b>Fechado por</b> ${
						this.caixa.user.name
					} - ${new Date(this.caixa.close_time).toLocaleString("pt-BR")}</li>
          <li class="my-1"><b>Total vendas realizadas ontem:</b> ${this.getYesterdaySales()}</li>
          </ul>`,
					icon: " fa fa-cash-register",
				};
			}
		},
	},
	async mounted() {
		this.clients.total = await getTotalClients();
		this.clients.yesterday = await getRegisteredClientsYesterday();
		const maintenances = await getTotalMaintenance([
			"maintenance",
			"approved",
			"waiting_stock",
		]);
		this.maintenances.total = maintenances.length;
		this.maintenances.outDueDate = await getOverdueMaintenances();
		this.caixa = await getCashierInfo(true);
		this.products = await getLowStock();
		this.cards = [
			this.getCaixa(),
			{
				title: `${this.clients.total} Clientes`,
				color: "bg-success",
				body: `${this.clients.yesterday} novos clientes ontem`,
				icon: "fa fa-user",
			},
			{
				title: `${this.maintenances.total} em manutenção`,
				body: this.maintenances.outDueDate
					? `${this.maintenances.outDueDate} fora do prazo de entrega`
					: null,
				color: "bg-warning",
				icon: "fa fa-wrench",
			},
			{
				title: `${this.products.count} prod. baixo estoque`,
				body: `<ul class="list-unstyled">` + this.getProductsList() + `</ul>`,
				color: "bg-danger",
				icon: "fa fa-sort-down",
			},
		];
	},
};
</script>
