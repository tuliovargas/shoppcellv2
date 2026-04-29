<template>
	<div class="maintenance-page">
		<div class="row">
			<div class="col-md-6">
				<div class="card card-body h-100">
					<orders-list
						ref="ordersList"
						@showOrder="showOrder"
						@changeCurrentOrder="currentOrder = $event"
						:currentOrder="currentOrder"
					/>
				</div>
			</div>

			<div class="col-md-6">
				<!-- <div class="card h-100 bg-light"> -->
				<order-details
					v-if="currentOrder.id"
					:currentOrder="currentOrder"
					:is-admin-user="isAdminUser"
					@setTechnician="updateOrders()"
					@removeProduct="removeProduct"
					@orderFixed="handleOrderFixed()"
					@addProduct="addProduct"
					@updateProduct="updateProduct"
				/>
				<div
					v-else
					class="h-100 d-flex justify-content-center align-items-center"
				>
					<p>Selecione uma manutenção ao lado</p>
				</div>
				<!-- </div> -->
			</div>
		</div>

		<!-- Botões para salvamento-->
		<!-- <div class="row mt-3" v-if="this.currentOrder.id">
            <div class="col-md-12">
                <div class="d-flex flex-row-reverse w-100">
                    <div>
                        <button
                            type="button"
                            class="btn btn-lg font-weight-bold btn-block btn-primary mb-3"
                        >
                            CONCLUIR MANUTENÇÃO
                        </button>
                    </div>
                </div>
            </div>
        </div> -->
	</div>
</template>

<script>
import OrdersList from "./OrdersList";
import OrderDetails from "./OrderDetails";

export default {
	name: "MaintenanceIndex",
	components: {
		"orders-list": OrdersList,
		"order-details": OrderDetails,
	},

	props: {
		userIsAdmin: {
			required: true,
		},
	},

	data() {
		return {
			currentOrder: {},
			orders: [],
		};
	},

	computed: {
		isAdminUser() {
			return this.userIsAdmin == 1;
		},
	},

	methods: {
		showOrder(order) {
			this.currentOrder = order;
		},
		updateOrders() {
			this.$refs.ordersList.getMaintenanceOrders();
		},
		removeProduct() {
			const delIndex = this.currentOrder.by_products.findIndex(
				(prod) => prod.delete
			);
			this.currentOrder.by_products.splice(delIndex, 1);
			this.updateOrderTotals();
		},
		addProduct(product) {
			this.currentOrder.by_products.push(product);
			this.updateOrderTotals();
		},
		updateProduct(index, product) {
			this.currentOrder.by_products[index] = product;
			this.$forceUpdate();
			this.updateOrderTotals();
		},
		handleOrderFixed() {
			(this.currentOrder = {}), this.$refs.ordersList.getMaintenanceOrders();
		},
		async updateOrderTotals() {
			let total = 0;
			let discount = 0;
			let subtotal = 0;
			let addition = 0;

			const prods = this.currentOrder.by_products;

			prods.forEach((item) => {
				addition += parseFloat(item.addition) * item.amount;
				discount += parseFloat(item.discount) * item.amount;
				subtotal += parseFloat(item.product.price) * item.amount;
				item.price = parseFloat(item.product.price);
			});

			total = subtotal - discount + addition;

			try {
				const { data: orders } = await axios.get("/orders", {
					params: {
						paginate: false,
						type: "vue",
						search: this.currentOrder.id,
					},
				});
				orders[0].total = total;
				orders[0].discount = discount;
				orders[0].subtotal = subtotal;

				orders[0].products[0].by_products = prods.map((item) => {
					item.product.amount = parseInt(item.amount);
					if (!item.product.discount) item.product.addition = 0;
					if (!item.product.discount) item.product.discount = 0;
					return item;
				});

				await axios.put(`/orders/${this.currentOrder.id}`, orders[0]);
			} catch (error) {
				console.error("Error na atualização de preço da orderm", error);
			}
		},
	},
};
</script>

<style lang="scss" scoped>
.maintenance-page {
	font-family: "Roboto", sans-serif;
}
</style>
