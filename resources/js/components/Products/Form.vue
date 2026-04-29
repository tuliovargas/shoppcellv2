<template>
	<form class="" @submit.prevent="handleSubmit">
		<history-product
			:productId="this.productId"
			@showHistory="showHistory = false"
			v-if="showHistory"
		></history-product>
		<template v-else>
			<div
				class="tab-pane fade active show"
				id="custom-tabs-three-home"
				role="tabpanel"
				aria-labelledby="custom-tabs-three-home-tab"
			>
				<div class="card-header p-0 border-bottom-0">
					<ul
						v-if="parseInt(productId) > 0"
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
								>Editar produto</a
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
								@click="showHistory = true"
								>Histórico de Alterações</a
							>
						</li>
					</ul>
					<h1 v-else>Adicionar produto</h1>
				</div>
				<div class="card card-bordered-product">
					<div class="row align-items-center">
						<div class="form-file" ref="formFile">
							<input type="file" @change="previewFile" />
						</div>
						<div class="col">
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label>Categoria</label>
										<select2
											:options="categories"
											v-model="product.category_id"
										/>
									</div>
								</div>
								<div class="col-12 col-md-6" v-if="subCategories.length > 0">
									<div class="form-group">
										<label>Sub-Categoria</label>
										<select2
											:options="subCategories"
											v-model="product.sub_category"
										/>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-4">
									<div class="form-group">
										<label>Marca</label>
										<select2 :options="brands" v-model="product.brand_id" />
									</div>
								</div>
								<div class="col">
									<div class="form-group">
										<label>Modelo</label>
										<search-select
											placeholder="Selecione ou crie um modelo"
											@select="handleSelectModel"
											:showList="showModelList"
											@input="getModels"
											@close="closeModelList"
											:items="models"
											ref="modelo"
										/>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-4">
							<div class="form-group">
								<label>Custo</label>
								<money
									v-model="product.cost"
									v-bind="money"
									class="form-control"
								/>
							</div>
						</div>
						<div class="col-4">
							<div class="form-group">
								<label>Valor Venda</label>
								<money
									v-model="product.price"
									v-bind="money"
									class="form-control"
									required
								/>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-6 col-lg-4">
							<div class="form-group">
								<label>Código de Barras</label>
								<input v-model="product.barcode" class="form-control" />
							</div>
						</div>
						<div class="col-6 col-lg-2">
							<div class="form-group">
								<label>Tipo de medida</label>
								<select2 :options="[]" v-model="product_type">
									<option value="un" selected>Unidade</option>
									<option value="sv">Serviço</option>
									<option value="kg">Kilogramas</option>
									<option value="other">Outro</option>
								</select2>
							</div>
						</div>
						<div v-if="product_type !== 'sv'" class="col-6 col-lg-2">
							<div class="form-group">
								<label>Estoque mínimo</label>
								<input
									type="number"
									v-model="product.minimum_stock"
									class="form-control"
								/>
							</div>
						</div>
						<div v-if="product_type !== 'sv'" class="col-6 col-lg-2">
							<div class="form-group">
								<label>Estoque atual</label>
								<input
									type="number"
									class="form-control"
									v-model="product.quantity_in_stock"
								/>
							</div>
						</div>
						<div class="col-2">
							<div class="form-group">
								<label>Garantia em dias</label>
								<input
									type="number"
									v-model="product.days_warranty"
									class="form-control"
									min="0"
								/>
							</div>
						</div>
					</div>

					<div class="row mt-2">
						<div v-if="product_type !== 'sv'" class="col-12 col-lg-2">
							<div class="form-check">
								<input
									class="form-check-input"
									type="checkbox"
									id="is_new"
									v-model="product.is_new"
								/>
								<label class="form-check-label" for="is_new"> É novo? </label>
							</div>
						</div>

						<div class="col-12 col-lg-2">
							<div class="form-check">
								<input
									class="form-check-input"
									type="checkbox"
									id="is_active"
									v-model="product.is_active"
								/>
								<label class="form-check-label" for="is_active"> Ativo</label>
							</div>
						</div>

						<div class="col-12 col-lg-3">
							<div class="row">
								<div class="col">
									<div class="form-check">
										<input
											class="form-check-input"
											v-model="can_commission"
											type="checkbox"
											id="accepts_commission"
											:value="true"
										/>
										<label class="form-check-label" for="accepts_commission">
											Aceita comissão?
											<span style="color: grey" v-if="can_commission">{{
												formatReal(val_commission)
											}}</span></label
										>
									</div>
								</div>
							</div>
							<div class="row mt-4" v-if="can_commission">
								<div class="col">
									<input
										type="number"
										class="form-control"
										min="0"
										max="100"
										step="any"
										v-model.number="product.commission_percentage"
										@change="calculaComissao()"
									/>
								</div>
							</div>
						</div>

						<div
							class="col-12 col-lg-2"
							v-if="
								can_commission &&
								(product.category_id == 1 || product.category_id == 2)
							"
						>
							<div class="row">
								<div class="col">
									<label style="margin: 0"
										>Comissão Técnico
										<span style="color: grey">{{
											formatReal(val_technician_commission)
										}}</span></label
									>
								</div>
							</div>
							<div class="row mt-4">
								<div class="col">
									<input
										type="number"
										class="form-control"
										min="0"
										max="100"
										step="any"
										v-model.number="product.technician_commission_percentage"
										@change="calculaComissaoTecnico()"
									/>
								</div>
							</div>
						</div>

						<div class="col-12 col-lg-3">
							<div class="row">
								<div class="col">
									<div class="form-check">
										<input
											v-model="can_discount"
											class="form-check-input"
											type="checkbox"
											id="accepts_discount"
											:value="true"
										/>
										<label class="form-check-label" for="accepts_discount">
											Aceita desconto?
											<span style="color: grey" v-if="can_discount">{{
												formatReal(val_discount)
											}}</span></label
										>
									</div>
								</div>
							</div>
							<div class="row mt-4" v-if="can_discount">
								<div class="col">
									<input
										type="number"
										class="form-control"
										min="0"
										max="100"
										v-model.number="product.discount_percentage"
										@change="calculaDesconto()"
									/>
								</div>
							</div>
						</div>
					</div>

					<div class="row mt-4">
						<div class="col">
							<div class="form-group">
								<label>Observação</label>
								<textarea class="form-control" v-model="product.observation" />
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<a class="btn btn-danger ml-auto mr-4" :href="cancelLink">Cancelar</a>
					<button ref="confirm-button" class="btn btn-primary mr-1">
						Confirmar
					</button>
				</div>
			</div>
		</template>
	</form>
</template>

<script>
import SearchSelect from "./SearchSelect.vue";
import toastr from "toastr";
import "toastr/build/toastr.min.css";
import { Money } from "v-money";
import { formatReal } from "../helpers/number";
import HistoryProduct from "./HistoryProduct";

export default {
	components: {
		HistoryProduct,
		SearchSelect,
		Money,
	},
	props: {
		cancelLink: {
			type: String,
			required: true,
		},
		productId: {
			type: String,
			required: true,
		},
	},

	data() {
		return {
			product: {
				category_id: 1,
				days_warranty: null,
				sub_category: null,
				quantity_in_stock: null,
				is_active: true,
				is_new: true,
				brand_id: 1,
			},
			allCategories: [],
			categories: [],
			subCategories: [
				{
					id: "0",
					text: "Selecione uma categoria com sub-categorias",
					selected: true,
				},
			],
			brands: [],
			models: [],
			showModelList: false,
			showHistory: false,
			modelSearch: "",
			money: {
				decimal: ",",
				thousands: ".",
				prefix: "R$ ",
				precision: 2,
				masked: false,
			},
			can_commission: false,
			can_discount: false,
			val_commission: 0.0,
			val_discount: 0.0,
			val_technician_commission: 0.0,
			product_type: "un",
		};
	},

	computed: {
		category_id() {
			return this.product.category_id;
		},
	},

	watch: {
		category_id(value) {
			this.getSubCategories(value);
		},
		can_discount(value) {
			this.calculaDesconto();
		},
		can_commission(value) {
			this.calculaComissao();
			this.calculaComissaoTecnico();
		},
		"product.days_warranty": function (newWarranty, oldWarranty) {
			if (newWarranty < 0) {
				this.product.days_warranty = -1 * newWarranty;
			}
		},
	},

	methods: {
		formatReal,
		calculaDesconto() {
			this.val_discount = parseFloat(
				(this.product.price * this.product.discount_percentage) / 100
			).toFixed(2);
		},
		calculaComissao() {
			this.val_commission = parseFloat(
				(this.product.price * this.product.commission_percentage) / 100
			).toFixed(2);
		},
		calculaComissaoTecnico() {
			this.val_technician_commission =
				this.product.technician_commission_percentage != undefined
					? parseFloat(
							(this.product.price *
								this.product.technician_commission_percentage) /
								100
					  ).toFixed(2)
					: 0.0;
		},
		previewFile(e) {
			const supportedMime = ["image/jpeg", "image/png"];
			const { type } = e.target.files[0];
			const [image] = e.target.files;
			this.product.photo = image;
			if (supportedMime.indexOf(type) > -1) {
				const reader = new FileReader();
				reader.readAsDataURL(e.target.files[0]);
				reader.onload = () => {
					this.$refs.formFile.style.background = `url('${reader.result}') no-repeat center center / cover`;
				};
			}
		},

		async getCategories() {
			const config = {
				params: {
					paginate: false,
					noSubcategories: true,
				},
			};

			let url = "/categories";

			try {
				let response = await axios.get(url, config);
				this.allCategories = response.data;
				this.categories = response.data.map((i) => ({
					id: i.id,
					text: i.name,
				}));
			} catch (error) {
				console.error(error);
			}
		},

		getSubCategories(value) {
			let index = this.allCategories.findIndex((c) => c.id == value);
			let childrenIndex = null;
			if (index == -1) {
				// A categoria do produto é uma subcategoria
				this.allCategories.forEach((el) => {
					childrenIndex = el.children.findIndex((c) => c.id == value);
					if (childrenIndex != -1) {
						this.product.category_id = el.id;
						index = el.id;
						setTimeout(() => {
							this.product.sub_category = value;
						}, 500);
					}
				});
			} else {
				this.product.sub_category = null;
			}
			const category = this.allCategories[index];
			this.subCategories = category.children.map((i) => ({
				id: i.id,
				text: i.name,
			}));
		},

		async getBrands() {
			const config = {
				params: {
					paginate: false,
				},
			};

			let url = "/brands";

			try {
				let response = await axios.get(url, config);
				this.brands = response.data.map((i) => ({
					id: i.id,
					text: i.name,
				}));
			} catch (error) {
				console.error(error);
			}
		},

		async getProduct() {
			const config = {
				params: {
					paginate: false,
				},
			};

			let url = "/products/" + this.productId;

			try {
				let response = await axios.get(url, config);
				let product = response.data;

				this.product.barcode = product.barcode;
				this.product.brand_id = product.brand_id
					? parseInt(product.brand_id)
					: 1;
				this.product.commission_percentage = product.commission_percentage
					? product.commission_percentage
					: 0;
				this.product.technician_commission_percentage =
					product.technician_commission_percentage
						? product.technician_commission_percentage
						: 0;
				this.product.cost = product.cost;
				this.product.days_warranty = product.days_warranty;
				this.product.discount_percentage = product.discount_percentage
					? product.discount_percentage
					: 0;
				this.product.is_active = product.is_active == 1 ? true : false;
				this.product.is_new = product.is_new == 1 ? true : false;
				this.product.minimum_stock = product.minimum_stock;
				this.product.price = product.price;
				this.product.quantity_in_stock = product.quantity_in_stock;
				this.product.observation = product.observation;
				this.modelSearch = product.brand_model
					? product.brand_model.name
					: product.name;

				this.product_type = product.type;
				this.can_commission = product.can_commission == 1 ? true : false;
				this.can_discount = product.can_discount == 1 ? true : false;
				this.product.category_id = product.category ? product.category : "1";
				this.product.sub_category = null;
				this.$refs.modelo.search = product.brand_model
					? product.brand_model.name
					: product.name;
			} catch (error) {
				console.error(error);
			}
		},

		closeModelList() {
			this.showModelList = false;
		},

		closePopup(e) {
			if (e.key === "Escape" || e.keyCode === 27) {
				this.showModelList = false;
			}
		},

		handleSelectModel(model) {
			if (model.text) {
				this.modelSearch = model.text;
				this.$refs.formFile.style.background = `url('/storage/${model.photo_url}') no-repeat center center / cover`;
			}
		},

		async getModels(e) {
			this.modelSearch = e.target.value;
			this.showModelList = true;
			addEventListener("keydown", this.closePopup);

			const config = {
				params: {
					paginate: false,
					search: e.target.value,
					brand_id: parseInt(this.product.brand_id),
				},
			};

			let url = "/brand-models";

			try {
				let response = await axios.get(url, config);
				this.models = response.data.map((i) => ({
					id: i.id,
					text: i.name,
					photo_url: i.photo_url,
				}));
			} catch (error) {
				console.error(error);
			}
		},

		resetForm() {
			this.product = {};
			this.allCategories = [];
			this.categories = [];
			this.subCategories = [
				{
					id: "0",
					text: "Selecione uma categoria com sub-categorias",
					selected: true,
				},
			];
			this.brands = [];
			this.models = [];
			this.showModelList = false;
			this.modelSearch = "";
		},

		async handleSubmit() {
			this.product.can_commission = this.can_commission;
			this.product.can_discount = this.can_discount;
			this.product.type = this.product_type;

			if (
				this.product.category_id == undefined ||
				this.product.category_id.length == 0
			) {
				toastr.error("Informe a categoria");
				return;
			}

			if (
				this.product.brand_id == undefined ||
				this.product.brand_id.length == 0
			) {
				toastr.error("Informe a marca");
				return;
			}

			if (this.modelSearch == undefined || this.modelSearch.length == 0) {
				toastr.error("Informe o modelo");
				return;
			}

			if (this.product.type == undefined || this.product.type.length == 0) {
				toastr.error("Informe o tipo de medida");
				return;
			}

			if (
				this.product.type != "sv" &&
				(this.product.minimum_stock == undefined ||
					this.product.minimum_stock.length == 0)
			) {
				toastr.error("Informe o estoque mínimo");
				return;
			}

			if (
				this.product.type != "sv" &&
				(this.product.quantity_in_stock == undefined ||
					this.product.quantity_in_stock.length == 0)
			) {
				toastr.error("Informe o estoque atual");
				return;
			}

			if (
				this.product.days_warranty == undefined ||
				this.product.days_warranty.length == 0
			) {
				toastr.error("Informe a garantia em dias");
				return;
			}

			let url = "",
				formData = new FormData(),
				success = "",
				errorMessage = "";

			if (parseInt(this.productId) > 0) {
				url = "/products/" + this.productId;
				formData.set("_method", "PUT");
				success = "Produto atualizado com sucesso";
				errorMessage = "Não foi possível atualizar o produto";
			} else {
				url = "/products";
				success = "Produto cadastrado com sucesso";
				errorMessage = "Não foi possível cadastrar o produto";
			}

			Object.keys(this.product).forEach((key) => {
				if (this.product[key]) {
					formData.append(key, this.product[key]);
				}
			});

			formData.set("name", this.modelSearch);
			formData.set("brand_model", this.modelSearch);

			try {
				await axios.post(url, formData, {
					headers: { "Content-Type": "multipart/form-data" },
				});
				this.$refs["confirm-button"].setAttribute("disabled", true);
				toastr.success(success);
				setTimeout(() => {
					window.location.href = this.cancelLink;
				}, 2000);
			} catch (error) {
				toastr.error(errorMessage);
			}
		},
	},

	async mounted() {
		await this.getCategories();
		await this.getBrands();

		if (parseInt(this.productId) > 0) {
			this.getProduct();
		}
	},
};
</script>

<style scoped>
.card-bordered-product {
	border: 1px solid #dee2e6;
	border-top: transparent;
}
.card {
	color: #4b545c;
}

h1 {
	color: #4b545c;
	font-size: 22px;
	font-family: "Roboto";
}

.card label {
	text-transform: uppercase;
	margin-bottom: 12px;
}

.card .form-check {
	display: flex;
	align-items: center;
}

.card .form-check label {
	margin-bottom: 0;
	padding-left: 16px;
	cursor: pointer;
	color: #4b545c;
	font-weight: bold;
}

.card .form-check input {
	visibility: hidden;
	opacity: 0;
}

.card .form-check label:before,
.card .form-check label:after {
	content: "";
	position: absolute;
	top: 50%;
}

.card .form-check label:before {
	left: 0;
	width: 30px;
	height: 30px;
	margin: -15px 0 0;
	background: #ffffff;
	border: 1px solid #e4e7ed;
	border-radius: 4px;
}

.card .form-check label:after {
	left: 5px;
	width: 20px;
	height: 20px;
	margin: -10px 0 0;
	opacity: 0;
	transition: all 0.2s;
	border-radius: 4px;
	background: #0983e8;
}

.card .form-check input:checked + label:after {
	opacity: 1;
}

.card .form-control {
	padding: 24px 20px;
	border: 1px solid #e4e7ed;
	color: #4b545c;
}

.card {
	padding: 30px;
	box-shadow: none;
	border-radius: 4px;
}

.btn {
	font-family: "Roboto", sans-serif;
	font-size: 16px;
	padding: 12px 42px;
}

.btn.btn-primary {
	font-weight: bold;
	text-transform: uppercase;
}

.form-file input {
	width: 100%;
	height: 100%;
	opacity: 0;
	color: transparent;
}

.form-file {
	width: 180px;
	height: 180px;
	border-radius: 4px;
	background: #e4e7ed;
	margin-right: 8px;
}
</style>

<style>
.select2-selection {
	height: 50px !important;
	line-height: 50px;
	border-radius: 4px;
	border: 1px solid #e4e7ed;
	color: #4b545c;
}

.select2-selection__arrow {
	height: 45px !important;
}

.select2-container--default
	.select2-selection--single
	.select2-selection__rendered {
	line-height: 40px !important;
}

.select2-container--default .select2-selection--single {
	border: 1px solid #e4e7ed !important;
}
</style>
