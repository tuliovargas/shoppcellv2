<template>
	<div class="wrapper">
		<div v-if="currentOrder.id" class="h-100">
			<div class="card" style="padding: 25px">
				<div class="d-flex flex-column justify-content-between">
					<div class="d-flex justify-content-between">
						<div class="text-secondary">
							<label class="order-details-title mb-0">CLIENTE</label>
							<p>{{ currentOrder.order.client.full_name }}</p>
						</div>
					</div>
					<div class="d-flex">
						<div v-if="hasUserTechnician" class="text-secondary">
							<label class="order-details-title mb-0">{{
								currentOrder.maintenance.os_status ===
								"no_maintenance_delivered"
									? "ENTREGA"
									: "PRAZO"
							}}</label>
							<p class="text-secondary" v-if="!calendar">
								{{ date }} -
								<span class="text-primary">{{ remainingTime }}</span>

								<button
									id="editDate"
									class="btn-clean align-self-center"
									style="cursor: pointer"
									@click="calendar = true"
								>
									<i class="fas fa-pen text-primary ml-2"></i>
								</button>
							</p>
							<div v-else class="d-flex">
								<datepicker
									class="mr-2"
									v-show="calendar"
									ref="pickDeadline"
									v-model="dateDeadline"
									name="deadline"
									:language="portuguese"
									@selected="changeDealine"
								></datepicker>
								<p>Selecione um novo prazo</p>
							</div>
						</div>
						<div v-if="hasUserTechnician" class="text-secondary mr-4">
							<label style="font-size: 14px; margin-bottom: 0">TÉCNICO</label>
							<div>
								<select
									v-model="currentOrder.maintenance.user_id"
									class="form-control mr-2 p-2 border-0"
									@change="updateOrder()"
								>
									<option
										v-for="(tech, index) in technicians"
										:value="tech.id"
										:key="index"
									>
										{{ tech.name }}
									</option>
								</select>
							</div>
						</div>
						<div v-if="hasUserTechnician" class="text-secondary">
							<label style="font-size: 14px; margin-bottom: 0">STATUS</label>
							<div>
								<select
									v-model="selectedStatus"
									class="form-control mr-2 p-0 border-0"
									@change="updateOrder"
								>
									<option
										v-for="(status, index) in orderStatus"
										:value="status.value"
										:key="index"
									>
										{{ status.name }}
									</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group border-top mt-1 pt-2">
					<label class="text-secondary section-title mb-2">OBSERVAÇÃO</label>
					<p class="text-secondary observation">
						{{ currentOrder.maintenance.issue }}
					</p>
				</div>
			</div>
			<div v-if="hasUserTechnician && !showOptionsFinish">
				<div class="form-group card" style="padding: 25px">
					<div class="form-group position-relative">
						<input
							type="text"
							v-model="productSearch"
							@keyup="getProducts"
							class="form-control"
							placeholder="Busca por produto (Nome/Código)"
						/>

						<ul
							v-if="productSearch && products && showListProduct"
							class="list-group search-popup"
						>
							<li
								ref="options"
								v-if="products.length === 0 && productSearch"
								class="list-group-item is-active"
							>
								<div
									@click="showCreateProductModal()"
									class="flex justify-between items-center"
									style="user-select: none; cursor: pointer"
								>
									Produto não existe, clique aqui para criá-lo
								</div>
							</li>
							<li
								v-for="product in products"
								:key="product.id"
								@click="addToList({ product: product })"
								class="list-group-item"
							>
								<div class="flex justify-between items-center">
									<span>{{ `${product.name} |` }}</span>
									<span
										:class="{
											'text-danger': product.quantity_in_stock <= 0,
										}"
										>{{ `Estoque: ${product.quantity_in_stock}` }}</span
									>
									<span>{{ `| Código: ${product.barcode}` }}</span>
								</div>
							</li>
						</ul>
					</div>
					<label class="text-secondary section-title mt-3 mb-2">PRODUTO</label>
					<table class="w-100 text-secondary">
						<thead>
							<tr style="cursor: default">
								<th class="th__prod-name" width="25%">Produto</th>
								<th class="th__prod-amount text-center" width="5%">Qtd.</th>
								<th width="15%" class="th__prod-price text-center">Preço</th>
								<th width="15%" class="th__prod-addition text-center">
									Acréscimo
								</th>
								<th width="15%" class="th__prod-discount text-center">
									Desconto
								</th>
								<th width="15%" class="th__prod-subtotal text-center">
									Subtotal
								</th>
								<th width="5%" class="th__prod-remove"></th>
								<th width="5%" class="th__prod-remove"></th>
							</tr>
						</thead>
						<tbody>
							<tr
								v-for="(prod, index) in currentOrder.by_products"
								:key="prod.id"
								style="cursor: default"
							>
								<td width="25%" class="bullet py-1">
									{{ prod.product.name }}
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
								<td width="5%">
									<button
										class="btn-clean btn ml-2"
										@click="removeFromList(index)"
									>
										<i class="far fa-trash-alt text-danger"></i>
									</button>
								</td>
								<td width="5%">
									<button class="btn-clean btn ml-2" @click="editItem(index)">
										<i class="fas fa-pen text-primary"></i>
									</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div
				v-else-if="!hasUserTechnician && orderNotFinished"
				class="d-flex align-items-center h-100"
			>
				<button
					type="button"
					class="btn btn-lg font-weight-bold btn-block btn-primary mb-3"
					@click.prevent="showTechnicianModal()"
				>
					REALIZAR MANUTENÇÃO
				</button>
			</div>
			<!-- !orderNotFinished && hasUserTechnician -->
			<div v-if="showOptionsFinish" class="card order-finished">
				<div>
					<label>Selecione uma opção</label>
					<div class="d-flex">
						<div
							class="mr-3 cursor-pointer-hover"
							style="user-select: none"
							@click="handleFixed(!orderFixed)"
						>
							<input
								type="checkbox"
								v-model="orderFixed"
								@input="handleFixed"
								lass="form-control"
							/>
							Com Reparo
						</div>
						<div
							class="mr-3 cursor-pointer-hover"
							style="user-select: none"
							@click="handleNotFixed(!orderNotFixed)"
						>
							<input
								type="checkbox"
								v-model="orderNotFixed"
								@input="handleNotFixed"
								lass="form-control"
							/>
							Sem Reparo
						</div>
					</div>
					<div class="form-group mt-3 mb-0">
						<label>Laudo técnico</label>
						<textarea
							name=".maintenance_info.issue"
							class="form-control w-100"
							rows="3"
							placeholder="Laudo técnico..."
							style="resize: none"
							v-model="currentOrder.maintenance.technical_report"
						></textarea>
					</div>
				</div>
			</div>
			<div
				v-if="showOptionsFinish"
				class="d-flex btns-order-finish flex-grow-1 justify-content-end"
			>
				<button
					@click="showOptionsFinish = false"
					class="btn btn-danger btn-cancel"
				>
					Cancelar
				</button>
				<button @click="handleOrderFinish" class="btn btn-primary btn-confirm">
					CONFIRMAR
				</button>
			</div>
		</div>
		<div v-if="currentOrder.maintenance.user_id && !showOptionsFinish">
			<div class="card mb-0" style="padding: 30px">
				<div class="section-title form-group">
					<label class="text-secondary mb-0">COMENTÁRIOS</label>
				</div>
				<div id="comments" class="comments gallery" ref="comments">
					<div
						class="comment bg-light p-3 rounded"
						v-for="(comment, i) in comments"
						:key="comment.id"
					>
						<div class="comment-title" :id="'comment-pos-' + i">
							<p>
								<span class="comment-title-user">{{
									handleUser(comment.user_id)
								}}</span>
								<span class="text-muted"> - {{ comment.created_at }}</span>
							</p>
						</div>
						<p class="comment-text">{{ comment.text }}</p>
						<div
							class="mt-3"
							v-if="comment.attachments && comment.attachments.length"
						>
							<h6>
								<b>Anexos:</b>
							</h6>
							<div
								class="d-inline-block mr-1"
								v-for="(file, i) in comment.attachments"
								:key="i"
							>
								<a
									:href="'storage/' + file.file_name"
									v-if="isImage(file.file_name)"
								>
									<img
										class="rounded"
										style="height: 50px"
										:src="'storage/' + file.file_name"
										alt="Anexo"
									/>
								</a>
								<a v-else target="_blank" :href="'storage/' + file.file_name">
									<i class="fas fa-file-pdf fa-2x"></i>
								</a>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group pt-2 d-flex row">
					<div class="col-md-9">
						<textarea
							class="form-control comment-input"
							:class="{ 'form-error': errors.comment }"
							rows="3"
							placeholder="Comentário"
							v-model="form.comment"
						>
						</textarea>
						<small v-if="errors.comment" class="comment-error">{{
							errors.comment
						}}</small>
					</div>
					<div class="col-md-3 d-flex flex-column">
						<label
							class="position-relative text-secondary font-weight-normal text-center"
						>
							<input class="file-input" type="file" @change="listFiles" />
							<i class="fa fa-paperclip"></i>
							Anexar arquivo
						</label>
						<button
							class="btn btn-lg btn-secondary font-weight-bold"
							@click="submitComment"
						>
							Comentar
						</button>
					</div>
				</div>
				<ul class="list-group list-files px-2">
					<li
						class="list-group-item d-flex justify-content-between align-items-center"
						v-for="(file, i) in filesUploaded"
						:key="i"
					>
						<span>{{ file.name }}</span>
						<button
							class="btn"
							style="padding: 0 0.75rem"
							@click="removeImageFromList(i)"
						>
							<span style="display: block; color: #d63030">
								<i class="far fa-trash-alt"></i>
							</span>
						</button>
					</li>
				</ul>
			</div>
		</div>

		<ModalCreateProduct
			:name="productSearch"
			@productCreated="handleCreatedProd"
		/>

		<MaintenanceProductModal
			:current-edit-product="currentEditProduct"
			:current-index="currentIndex"
			@submit="addToList($event)"
		/>

		<!--Modal Vendedor-->
		<ModalUsers
			ref="modalUsers"
			@selectedUser="technicianSelected = $event"
			@confirmedUser="startMaintenance()"
			:users="technicians"
			:userSelected="technicianSelected"
			type="Técnico"
		/>
	</div>
</template>

<script>
import moment from "moment";
import "daterangepicker/daterangepicker.css";
import axios from "axios";
import Datepicker from "vuejs-datepicker";
import { ptBR } from "vuejs-datepicker/dist/locale";
import toastr from "toastr";
import { getCurrentUser, getTechnicians, getUsers } from "../../services/user";
import ModalUsers from "../Order/ModalUsers";
import ModalCreateProduct from "./ModalCreateProduct";
import MaintenanceProductModal from "./MaintenanceProductModal";
import _ from "lodash";
import SimpleLightbox from "simplelightbox";
require("simplelightbox/dist/simple-lightbox.min.css");

export default {
	name: "OrderDetails",
	components: {
		Datepicker,
		ModalUsers,
		ModalCreateProduct,
		MaintenanceProductModal,
	},
	props: {
		currentOrder: {
			type: Object,
			required: true,
		},
		isAdminUser: {
			type: Boolean,
			default: false,
		},
	},
	data() {
		return {
			calendar: false,
			dateDeadline: "",
			productSearch: "",
			products: [],
			showListProduct: "",
			portuguese: ptBR,
			form: {
				comment: "",
			},
			errors: {
				comment: "",
			},
			orderStatus: [
				{
					value: "waiting_stock",
					name: "Aguardando peça do fornecedor",
				},
				{ value: "maintenance", name: "Em manutenção" },
				{ value: "no_maintenance", name: "Sem conserto" },
				{ value: "fixed", name: "Consertado" },
				{ value: "no_maintenance_delivered", name: "Sem conserto e entregue" },
			],
			filesUploaded: [],
			selectedStatus: null,
			comments: [],
			showOptionsFinish: false,
			orderFixed: false,
			orderNotFixed: false,
			orderTechIssue: "",
			technicians: [],
			technicianSelected: {},
			users: [],
			currentEditProduct: {},
			currentIndex: null,
		};
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
		async currentOrder(value) {
			this.clearData();
			this.showOptionsFinish = false;
			this.selectedStatus = value.maintenance.os_status;
			await this.getComments();
		},
	},

	computed: {
		remainingTime() {
			const now = moment.now();
			const endDate = this.dateDeadline
				? this.dateDeadline
				: this.currentOrder.maintenance.due_date;
			const end = moment(endDate);
			const duration = moment.duration(end.diff(now));

			const days = Math.floor(duration.asDays());
			duration.subtract(moment.duration(days, "days"));

			const hours = duration.hours();
			duration.subtract(moment.duration(hours, "hours"));
			if (this.currentOrder.maintenance.due_date || this.dateDeadline != "")
				return `Faltam ${days > 0 ? days + " dias" : ""} ${
					days > 0 && hours > 0 ? "e" : ""
				} ${hours ? hours + " horas" : ""}`;
			else return "";
		},
		date() {
			const date =
				this.dateDeadline !== ""
					? this.dateDeadline
					: this.currentOrder.maintenance.due_date;
			if (date) return moment(date).format("DD/MM/YYYY hh:mm");
			else return "Sem prazo definido";
		},
		hasUserTechnician() {
			return this.currentOrder.maintenance.user_id;
		},
		orderNotFinished() {
			return !["fixed", "no_maintenance"].includes(
				this.currentOrder.maintenance.os_status
			);
		},
		cart() {
			return this.currentOrder.by_products;
		},
	},

	async mounted() {
		this.setStatus();
		this.technicians = await getTechnicians(true);
		await this.getComments();
		this.getUser();
		this.users = await getUsers();

		const { data } = await axios.get("/products", {
			params: { product_id: 569, paginate: false },
		});
	},

	methods: {
		async getUser() {
			await getCurrentUser().then((user) => {
				this.currentOrder.user = user;
			});
		},
		handleNotFixed(event) {
			const value = event.target ? event.target.value : event;
			this.orderNotFixed = value;
			this.orderFixed = !value;
			this.selectedStatus = "no_maintenance";
		},
		handleFixed(event) {
			const value = event.target ? event.target.value : event;
			this.orderFixed = value;
			this.orderNotFixed = !value;
			this.selectedStatus = "fixed";
		},
		showCreateProductModal() {
			$("#createProductModal").modal("show");
		},
		async handleCreatedProd(product) {
			await this.addToList({ product });
			this.productSearch = "";
		},
		async handleOrderFinish() {
			this.currentOrder.maintenance.os_status = this.orderFixed
				? "fixed"
				: "no_maintenance";
			await this.updateOrder();
			this.$emit("orderFixed");
		},
		async getProducts() {
			this.showListProduct = true;

			const config = {
				params: {
					search: this.productSearch,
					paginate: false,
					category: 1,
				},
			};

			let url = "/products";

			try {
				let response = await axios.get(url, config);
				this.products = response.data;
			} catch (error) {
				console.error(error);
			}
		},
		async addToList({ product, update, indexProd }) {
			if (["concluded", "canceled"].includes(this.currentOrder.order.status)) {
				return toastr.error(
					"Você não tem permissões para adicionar/remover produtos em ordens concluídas e/ou canceladas"
				);
			}
			if (update) {
				this.$emit("updateProduct", indexProd, product);
			} else {
				const objProduct = {
					product: product,
					amount: 1,
					price: parseFloat(product.price),
				};
				await this.$emit("addProduct", objProduct);
			}

			try {
				this.showListProduct = false;
				const maintenanceId = this.currentOrder.maintenance.id;
				await axios.put(`/maintenances/${maintenanceId}`, this.currentOrder);
				toastr.success("Produto adicionado com sucesso!");
				if (update) {
					$("#productEdit").modal("hide");
				}
				this.$forceUpdate();
			} catch (e) {
				console.error("Error UP", e);
			}
		},
		async removeFromList(index) {
			if (["concluded", "canceled"].includes(this.currentOrder.order.status)) {
				return toastr.error(
					"Você não tem permissões para adicionar/remover produtos em ordens concluídas e/ou canceladas"
				);
			}
			this.currentOrder.by_products[index].delete = 1;

			const { maintenance } = this.currentOrder;

			delete maintenance.brand;
			delete maintenance.brand_model;
			const maintenanceId = this.currentOrder.maintenance.id;

			try {
				await axios.put(`/maintenances/${maintenanceId}`, this.currentOrder);
				toastr.success("Produto removido com sucesso!");
				this.$emit("removeProduct");
			} catch (e) {
				console.error("Error UP", e);
				toastr.error("Erro ao tentar remover produto!");
				delete this.currentOrder.by_products[index].delete;
			}
		},
		showTechnicianModal() {
			$("#usersModal").modal("show");
		},
		startMaintenance() {
			if (this.currentOrder.maintenance.os_status !== "approved") {
				$("#usersModal").modal("hide");
				return toastr.error(
					"Desculpe mas essa manutenção ainda não foi aprovada"
				);
			}
			this.currentOrder.maintenance.user_id = this.technicianSelected.id;
			this.currentOrder.maintenance.os_status = "maintenance";
			this.selectedStatus = "maintenance";
			this.updateOrder();
			this.$emit("setTechnician", this.currentOrder);
			$("#usersModal").modal("hide");
		},
		showCalendar() {
			this.showCalendar = true;
			this.$refs.pickDeadline.showCalendar();
		},
		async changeDealine(d) {
			this.currentOrder.maintenance.due_date = d;
			if (this.currentOrder.user == undefined) {
				await this.getUser();
			}
			d = moment(d).format("DD/MM/YYYY");
			this.calendar = false;

			const newComment = {
				user_id: this.currentOrder.user.id,
				user_name: this.currentOrder.user.name,
				created_at: new Date().toLocaleDateString(),
				text: `${this.currentOrder.user.name} mudou o prazo para o dia ${d}`,
			};

			await axios
				.post(`/orders/${this.currentOrder.id}/comments`, newComment)
				.then((r) => {
					if (r.status === 200) this.comments.push(newComment);
					this.scrollComments();
				})
				.catch((e) => console.error("Error Comment", e));
			await axios
				.put(
					`/maintenances/${this.currentOrder.maintenance.id}`,
					this.currentOrder
				)
				.then((_r) => {
					toastr.success("Ordem de serviço atualizada com sucesso!");
				})
				.catch((e) => {
					console.error(e);
					toastr.error("Erro ao tentar atualizar a ordem de serviço!");
				});
		},
		async submitComment() {
			const newComment = new FormData();
			newComment.append("user_id", this.currentOrder.user_id);
			newComment.append(
				"user_name",
				this.currentOrder.user ? this.currentOrder.user.name : null
			);
			newComment.append(
				"created_at",
				moment(new Date()).format("DD/MM/YYYY hh:mm:ss")
			);
			newComment.append("text", this.form.comment);

			this.filesUploaded.forEach(function logArrayElements(
				element,
				index,
				array
			) {
				newComment.append("attachments[]", element);
			});

			if (this.form.comment.length < 5)
				return (this.errors.comment =
					"Comentários devem conter pelo menos 5 caracteres");
			else this.errors.comment = "";

			await axios
				.post(`/orders/${this.currentOrder.id}/comments`, newComment, {
					headers: {
						"Content-Type": "multipart/form-data",
					},
				})
				.then((r) => {
					if (r.status === 200) {
						this.getComments();
						this.filesUploaded = [];
						this.form.comment = "";
						this.scrollComments();
					}
				})
				.catch((e) => console.error("Error Comment", e));
		},

		listFiles(event) {
			this.filesUploaded.push(event.target.files[0]);
		},

		removeImageFromList(index) {
			this.filesUploaded.splice(index, 1);
		},

		async updateOrder(event = {}) {
			if (
				["concluded", "canceled"].includes(this.currentOrder.order.status) &&
				!this.isAdminUser
			) {
				return toastr.error(
					"Você não tem permissões para alterar status de ordens concluídas e/ou canceladas"
				);
			}

			if (
				event.target &&
				["fixed", "no_maintenance"].includes(event.target.value)
			) {
				this.showOptionsFinish = true;
				const value = event.target.value === "fixed";
				this.orderFixed = value;
				this.orderNotFixed = !value;
				return;
			} else {
				this.showOptionsFinish = false;
			}

			const { maintenance } = this.currentOrder;

			this.currentOrder.status = this.orderFixed
				? "waiting_payment"
				: "canceled";

			if (!_.isEmpty(event)) {
				maintenance.os_status = event.target ? event.target.value : event;
				maintenance.fixed = this.orderFixed;
			}

			const currentOrder = _.clone(this.currentOrder);

			currentOrder.maintenance.brand = null;
			currentOrder.maintenance.brand_model = null;

			await axios
				.put(`/maintenances/${currentOrder.maintenance.id}`, currentOrder)
				.then((_r) => {
					toastr.success("Ordem de serviço atualizada com sucesso!");
					this.$emit("updateOrder", this.currentOrder);
				})
				.catch((e) => {
					console.error(e);
					toastr.error("Erro ao tentar atualizar a ordem de serviço!");
				});
		},

		clearData() {
			this.form.comment = "";
			this.filesUploaded = [];
			this.dateDeadline = "";
			this.comments = [];
			return (this.errors.comment = "");
		},

		setStatus() {
			this.selectedStatus = this.currentOrder.maintenance.os_status;
		},

		scrollComments() {
			setTimeout(() => {
				const el = this.$el.querySelector("#comments");
				el.scrollTop = el.scrollHeight > 1 ? el.scrollHeight : 40;
			}, 75);
		},

		getComments: _.debounce(async function () {
			const config = {
				params: {
					paginate: false,
				},
			};

			try {
				let response = await axios.get(
					`/orders/${this.currentOrder.id}/comments`,
					config
				);
				this.comments = response.data;
				this.$nextTick(() => {
					/* Pega somente os anchors que são 'pais' de uma imagem */
					try {
						const elements = $(".gallery a>img")
							.get()
							.map((el) => {
								return el.parentNode;
							});
						new SimpleLightbox(elements);
					} catch (e) {}
				});
			} catch (error) {
				console.error(error);
			}
		}, 500),

		handleUser(userId) {
			const tech = this.technicians.find((tech) => tech.id === userId);
			let user = null;
			if (!tech) {
				user = this.users.find((user) => user.id == userId);
				return user.name;
			}
			return tech.name;
		},

		getAttachmentName(path) {
			const lastSegment = path.split("/").pop();
			return lastSegment;
		},
		isImage(fileName) {
			const end = new RegExp(/(png|jpg|jpeg|gif)$/);
			return fileName.match(end) ? true : false;
		},
		async addAmountProductCart(index) {
			const cart = this.cart;
			if (
				cart.product &&
				cart.product.quantity_in_stock &&
				cart[index].amount >= cart.product.quantity_in_stock
			)
				return toastr.error("Produto sem quantidade em estoque");

			let quantity = this.cart[index].amount + 1;
			await this.addToList(this.cart[index], true, quantity, index);
		},

		async removeAmountProductCart(index) {
			let quantity = this.cart[index].amount - 1;
			await this.addToList(this.cart[index], true, quantity, index);
		},
		editItem(index) {
			this.currentEditProduct = _.cloneDeep(this.cart[index]);
			this.currentIndex = index;
			$("#productEdit").modal("show");
		},
		getTotal(product) {
			if (parseFloat(product.price) < parseFloat(product.addition)) {
				return (
					parseFloat(product.price) +
					parseFloat(product.addition) * product.amount
				);
			}
			return product.price * product.amount;
		},
	},
};
</script>

<style lang="scss" scoped>
p {
	margin: 0;
}

.file-input {
	position: absolute;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
	opacity: 0;
	cursor: pointer;
}

.bullet {
	position: relative;
	padding-left: 12px;
}

.bullet::before {
	content: "";
	display: block;
	width: 6px;
	height: 6px;
	border-radius: 3px;
	background-color: var(--primary);
	position: absolute;
	top: 50%;
	left: 0;
	transform: translateY(-50%);
}

table td {
	vertical-align: center;
}

table tr {
	display: table;
	width: 100%;
}

tbody {
	display: block;
	max-height: 100px;
	overflow: auto;
}
thead,
tbody tr {
	display: table;
	width: 100%;
	table-layout: fixed;
}
thead {
	width: calc(
		100% - 1em
	); /* scrollbar is average 1em/16px width, remove it from thead width */
}

.section-title {
	font-size: 14px;
}

.order-details-title {
	font-size: 12px;
}

.btn-clean {
	background: none;
	border: none;
	padding: 0;
}

.comment-title {
	display: flex;
}

.observation {
	font-size: 14px;
}

.comment-title p {
	margin-bottom: 2px;
	font-weight: 500;
}

.comment-title-user {
	color: #21316f;
}

.comment {
	margin-bottom: 12px;
	color: var(--secondary);
	font-size: 14px;
}

.comments {
	max-height: 200px;
	overflow-x: auto;
}

.comment-input {
	max-height: 80px;
	resize: none;
	border: 1px solid #e4e7ed;
}

.comment-error {
	color: #ee3939;
}

.form-error {
	border: 1px solid #ee3939;
}

.list-group-item {
	cursor: pointer;
}

.search-popup {
	position: absolute;
	top: 38px;
	z-index: 20;
	width: calc(100% - 16px);
	max-height: 360px;
	overflow: auto;
}

.list-files {
	margin-top: 15px;
	padding: 0 1.5rem;
	max-height: 150px;
	overflow-y: auto;

	.list-group-item {
		border: none;
		background: rgba(228, 231, 237, 0.3);
		border-radius: 4px;
	}

	li {
		padding: 0.75rem 15px 0.75rem 35px;

		span {
			display: list-item;

			&::marker {
				color: #0983e8;
			}
		}
	}
}

.order-finished {
	padding: 20px 30px 30px 20px;

	.cursor-pointer-hover {
		cursor: pointer;
	}
}

.btn-confirm {
	background: #21316f;
	border-radius: 4px;
	font-family: "Roboto";
	font-weight: bold;
	font-size: 14px;
	line-height: 16px;
	letter-spacing: 0.01em;
	color: #ffffff;
	margin-right: 5px;
	padding: 10px 20px;

	&:hover {
		opacity: 0.9;
	}
}

.btn-cancel {
	margin-right: 15px;
	background: #d63030;
	font-family: "Roboto";
	font-size: 14px;
	line-height: 16px;
	letter-spacing: 0.01em;
	color: #ffffff;
	padding: 10px 20px;

	&:hover {
		opacity: 0.8;
	}
}
i.fas.fa-file-pdf {
	color: #d63030;
	transition: 0.5s all;
	&:hover {
		opacity: 0.75;
	}
}
</style>
