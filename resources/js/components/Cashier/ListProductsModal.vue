<template>
	<div
		class="modal"
		id="listProducts"
		tabindex="-1"
		role="dialog"
		ref="listProducts"
	>
		<form class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title font-weight-bold text-center w-100">
						Lista de produtos
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
					<table class="w-100 text-secondary">
						<thead>
							<tr style="cursor: default">
								<th class="th__prod-name" witdth="35%">
									Produto
								</th>
								<th class="th__prod-amount text-center" width="5%">
									Qtd.
								</th>
								<th width="15%" class="th__prod-price text-center">
									Preço
								</th>
								<th width="15%" class="th__prod-addition text-center">
									Acréscimo
								</th>
								<th width="15%" class="th__prod-discount text-center">
									Desconto
								</th>
								<th width="15%" class="th__prod-subtotal text-center">
									Subtotal
								</th>
							</tr>
						</thead>
						<tbody>
							<template v-for="(prod, index) in products">
								<tr style="cursor: default" :key="prod.id">
									<td width="25%" class="bullet py-1">
										{{
											prod.product.id == 1
												? `${prod.product.name} (${index + 1})`
												: prod.product.name
										}}
									</td>
									<td width="5%" class="text-center">
										{{ prod.amount }}
									</td>
									<td width="15%" class="text-center">
										{{ prod.product.price | currency }}
									</td>
									<td width="15%" class="text-center">
										{{ prod.addition | currency }}
									</td>
									<td width="15%" class="text-center">
										{{ prod.discount | currency }}
									</td>
									<td width="15%" class="text-center">
										{{ getTotal(prod) | currency }}
									</td>
								</tr>
								<tr
									v-for="byProd in prod.by_products"
									:key="byProd.id"
									style="cursor: default; background-color: #ccc;"
								>
									<td width="25%" class="bullet py-1">
										{{ `OS: (${index + 1}) ` + byProd.product.name }}
									</td>
									<td width="5%" class="text-center">
										{{ byProd.amount }}
									</td>
									<td width="15%" class="text-center">
										{{ byProd.product.price | currency }}
									</td>
									<td width="15%" class="text-center">
										{{ byProd.addition | currency }}
									</td>
									<td width="15%" class="text-center">
										{{ byProd.discount | currency }}
									</td>
									<td width="15%" class="text-center">
										{{ getTotal(byProd) | currency }}
									</td>
								</tr>
							</template>
						</tbody>
					</table>
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
		products: {
			type: Array,
			default: () => []
		}
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

	computed: {},

	methods: {
		getTotal(product) {
			if (parseFloat(product.price) < parseFloat(product.addition)) {
				return (
					parseFloat(product.price) +
					parseFloat(product.addition) * product.amount
				);
			}
			return product.price * product.amount;
		}
	}
};
</script>

<style></style>
