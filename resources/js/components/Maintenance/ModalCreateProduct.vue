<template>
	<div
		class="modal"
		id="createProductModal"
		tabindex="-1"
		role="dialog"
		ref="createProductModal"
	>
		<form class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title font-weight-bold text-center w-100">
						Cadastrar NOVO Produto
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
					<div class="d-flex">
						<div class="form-file" ref="formFile">
							<input type="file" @change="previewFile" />
						</div>
						<div class="flex-fill">
							<input
								class="form-control my-3"
								v-model="form.name"
								autocomplete="no"
								placeholder="Nome produto"
								ref="productName"
								required
							/>
							<div class="input-group my-3">
								<div class="input-group-prepend">
									<span class="input-group-text" id="basic-addon1">VALOR</span>
								</div>
								<money
									class="money form-control"
									v-model="form.price"
									v-bind="money"
								></money>
							</div>
						</div>
					</div>
					<select
						v-model="form.type"
						name="type"
						id="type"
						class="form-control"
					>
						<option :value="null" disabled>TIPO DE MEDIDA</option>
						<option value="un">Unidade</option>
						<option value="sv">Serviço</option>
					</select>
					<div class="input-group my-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="basic-addon1"
								>DIAS DE GARANTIA</span
							>
						</div>
						<input
							class="form-control"
							type="number"
							min="1"
							max="365"
							v-model="form.days_warranty"
							autocomplete="no"
							ref="daysWarranty"
							required
						/>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger" data-dismiss="modal">Cancelar</button>
					<button class="btn btn-primary" @click.prevent="handleSubmit()">
						Salvar
					</button>
				</div>
			</div>
		</form>
	</div>
</template>

<script>
import moment from "moment";
import _ from "lodash";
import { Money } from "v-money";
import axios from "axios";
import * as Yup from "yup";
import toastr from "toastr";
import "toastr/build/toastr.min.css";

export default {
	components: { Money },

	props: {
		name: {
			type: String,
			default: ""
		}
	},

	data() {
		return {
			moment: moment,
			form: {
				name: this.name,
				photo: null,
				price: "",
				type: null,
				days_warranty: null,
				brand_id: 2,
				category_id: 1,
				cost: null,
				observation: "",
				minimum_stock: 0,
				quantity_in_stock: 1,
				can_discount: true,
				comission_percentage: null,
				technician_comission_percentage: null,
				discount_percentage: null,
				brand_model: this.name,
				is_new: true
			},
			/**
       'barcode' => 'nullable|string',
        'name' => 'required|string',
        'photo' => 'nullable|image',
        'price' => 'nullable|numeric',
        'cost' => 'nullable|numeric',
        'observation' => 'nullable|string',
        'minimum_stock' => 'nullable|integer',
        'quantity_in_stock' => 'nullable|integer',
        'can_discount' => 'nullable',
        'can_commission' => 'nullable',
        'commission_percentage' => 'nullable|numeric',
        'technician_commission_percentage' => 'nullable|numeric',
        'discount_percentage' => 'nullable|numeric',
        'is_new' => 'nullable',
        'is_active' => 'nullable',
        'type' => 'required|in:un,sv,kg,other',
        'days_warranty' => 'required|integer',
        'brand_id' => 'required|integer',
        'category_id' => 'required|integer',
        'sub_category' => 'nullable|integer',
        'brand_model' => 'nullable|string',
*/

			money: {
				decimal: ",",
				thousands: ".",
				prefix: "R$ ",
				suffix: "",
				precision: 2,
				masked: false
			}
		};
	},

	watch: {
		"form.price"(value) {
			value = parseFloat(value);
		},
		name: _.debounce(function(value) {
			this.form.name = value;
		}, 300),
		"form.name": _.debounce(function(value) {
			this.form.brand_model = value;
		}, 300)
	},

	methods: {
		async handleSubmit() {
			const validate = Object.entries(this.form).map(([key, value]) =>
				this.validateField(key, value)
			);

			if (validate.some(value => !!value))
				return validate.filter(msg => !!msg).forEach(msg => toastr.error(msg));

			try {
				const { data } = await axios.post("/products", this.form, {
					params: { paginate: false }
				});
				toastr.success("Produto cadastrado com sucesso!");
				this.$emit("productCreated", data);
				$("#createProductModal").modal("hide");
			} catch (e) {
				toastr.error("Erro no cadastro de produto!");
				console.error("Erro no cadastro de produto", e);
			}
		},
		previewFile(e) {
			const supportedMime = ["image/jpeg", "image/png"];
			const { type } = e.target.files[0];
			const [image] = e.target.files;
			this.form.photo = image;
			if (supportedMime.indexOf(type) > -1) {
				const reader = new FileReader();
				reader.readAsDataURL(e.target.files[0]);
				reader.onload = () => {
					this.$refs.formFile.style.background = `url('${reader.result}') no-repeat center center / cover`;
				};
			}
		},
		validateField(field, value) {
			const fields = {
				name: "Nome do produto",
				price: "Preço do produto",
				type: "Tipo de medida",
				days_warranty: "Dias de garantia"
			};
			if (!fields[field]) return;

			try {
				if (!value)
					return `${fields[field]} é um campo obrigatório não pode ser null`;

				if (field !== "price") {
					Yup.string()
						.label(fields[field])
						.required(`${fields[field]} é um campo obrigatório`)
						.validateSync(value);
				} else {
					Yup.number()
						.min(0, `O campo ${fields[field]} não pode ser =< 0`)
						.required(`${fields[field]} é um campo obrigatório`)
						.validateSync(value);
				}
			} catch (error) {
				return error.message;
			}
		},
		clearData() {
			this.form = {
				name: null,
				photo: null,
				price: "",
				type: null,
				days_warranty: null,
				brand_id: 2,
				category_id: 1,
				cost: null,
				observation: "",
				minimum_stock: 0,
				quantity_in_stock: 1,
				can_discount: true,
				comission_percentage: null,
				technician_comission_percentage: null,
				discount_percentage: null,
				brand_model: this.form.name,
				is_new: true
			};
		}
	}
};
</script>

<style lang="scss" scoped>
.form-file input {
	width: 100%;
	height: 100%;
	opacity: 0;
	color: transparent;
}

.form-file {
	width: 118px;
	height: 118px;
	border-radius: 4px;
	background: #e4e7ed;
	margin-right: 8px;
}
</style>
