<template>
	<form>
		<div
			class="tab-pane fade active show"
			id="custom-tabs-three-home"
			role="tabpanel"
			aria-labelledby="custom-tabs-three-home-tab card-history-product"
		>
			<div class="card-header p-0 border-bottom-0">
				<ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
					<li class="nav-item">
						<a
							class="nav-link"
							id="custom-tabs-three-home-tab"
							data-toggle="pill"
							href="#custom-tabs-three-home"
							role="tab"
							aria-controls="custom-tabs-three-home"
							aria-selected="false"
							@click="$emit('showHistory')"
							>Editar produto</a
						>
					</li>
					<li class="nav-item">
						<a
							class="nav-link active"
							id="custom-tabs-three-profile-tab"
							data-toggle="pill"
							href="#custom-tabs-three-profile"
							role="tab"
							aria-controls="custom-tabs-three-profile"
							aria-selected="true"
							>Histórico de Alterações</a
						>
					</li>
				</ul>
			</div>
			<div class="">
				<div class="card pt-4 card-bordered-product">
					<div
						class="tab-pane fade active show pt-6"
						id="custom-tabs-three-home"
						role="tabpanel"
						aria-labelledby="custom-tabs-three-home-tab"
					>
						<div class="card-header p-0 border-bottom-0">
							<ul
								class="nav nav-tabs"
								id="custom-tabs-three-tab"
								role="tablist"
							>
								<li class="nav-item">
									<a
										class="nav-link active"
										id="custom-tabs-three-home-tab"
										data-toggle="pill"
										href="#custom-tabs-three-home"
										role="tab"
										aria-controls="custom-tabs-three-home"
										aria-selected="false"
										>Estoque</a
									>
								</li>
								<li class="nav-item">
									<a
										class="nav-link"
										id="custom-tabs-three-profile-tab"
										data-toggle="pill"
										href="#custom-tabs-three-profile"
										role="tab"
										aria-controls="custom-tabs-three-profile"
										aria-selected="true"
										>Alterações</a
									>
								</li>
							</ul>
						</div>
						<div class="card-body">
							<div class="tab-content" id="custom-tabs-three-tabContent">
								<div
									class="tab-pane fade active show"
									id="custom-tabs-three-home"
									role="tabpanel"
									aria-labelledby="custom-tabs-three-home-tab"
								>
									<table class="table table-bordered">
										<tr>
											<th>Usuário</th>
											<th>Data</th>
											<th>Tipo</th>
											<th>Antes</th>
											<th>Depois</th>
										</tr>
										<tr v-for="history in stock_histories" :key="history.id">
											<td>{{ history.user.name }}</td>
											<td>{{ history.date }}</td>
											<td>{{ history.type }}</td>
											<td class="text-danger">
												<p v-if="history.old.price">
													R$ {{ history.old.price }}
												</p>
												<p v-else-if="history.old.quantity_in_stock">
													{{ history.old.quantity_in_stock }}
												</p>
												<p v-else-if="history.old.discount_percentage">
													{{ history.old.discount_percentage }} % (desconto)
												</p>
												<p v-else-if="history.old.commission_percentage">
													{{ history.old.commission_percentage }} % (comissão)
												</p>
											</td>
											<td class="text-success">
												<p v-if="history.new.price">
													R$ {{ history.new.price }}
												</p>
												<p v-else-if="history.new.quantity_in_stock">
													{{ history.new.quantity_in_stock }}
												</p>
												<p v-else-if="history.new.discount_percentage">
													{{ history.new.discount_percentage }} % (desconto)
												</p>
												<p v-else-if="history.new.commission_percentage">
													{{ history.new.commission_percentage }} % (comissão)
												</p>
											</td>
										</tr>
									</table>
								</div>
								<div
									class="tab-pane fade"
									id="custom-tabs-three-profile"
									role="tabpanel"
									aria-labelledby="custom-tabs-three-profile-tab"
								>
									<table class="table table-bordered">
										<tr>
											<th>Usuário</th>
											<th>Data</th>
											<th>Antes</th>
											<th>Depois</th>
										</tr>
										<tr v-for="history in histories" :key="history.id">
											<td>{{ history.user.name }}</td>
											<td>{{ history.date }}</td>
											<td class="text-danger">
												<p v-if="history.old.price">
													R$ {{ history.old.price }}
												</p>
												<p v-else-if="history.old.quantity_in_stock">
													{{ history.old.quantity_in_stock }}
												</p>
												<p v-else-if="history.old.discount_percentage">
													{{ history.old.discount_percentage }} % (desconto)
												</p>
												<p v-else-if="history.old.commission_percentage">
													{{ history.old.commission_percentage }} % (comissão)
												</p>
											</td>
											<td class="text-success">
												<p v-if="history.new.price">
													R$ {{ history.new.price }}
												</p>
												<p v-else-if="history.new.quantity_in_stock">
													{{ history.new.quantity_in_stock }}
												</p>
												<p v-else-if="history.new.discount_percentage">
													{{ history.new.discount_percentage }} % (desconto)
												</p>
												<p v-else-if="history.new.commission_percentage">
													{{ history.new.commission_percentage }} % (comissão)
												</p>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<!-- /.card -->
					</div>
				</div>
			</div>
		</div>
	</form>
</template>

<script>
export default {
	props: {
		productId: {
			type: String,
			required: true,
		},
	},
	data() {
		return {
			histories: [],
			stock_histories: [],
		};
	},
	mounted() {
		this.getHistory();
	},
	methods: {
		async getHistory() {
			let url = "/products/" + this.productId + "/log";

			try {
				let response = await axios.get(url);
				this.histories = response.data.histories;
				this.stock_histories = response.data.stock_histories;
			} catch (error) {
				console.error(error);
			}
		},
	},
};
</script>

<style></style>
