<template>
	<div>
		<div class="row mb-3 justify-content-between">
			<div class="col-md-6 col-lg-7 col-xl-6">
				<div class="row justify-content-between">
					<div class="col-md-8">
						<div class="small-box cashier-card">
							<div
								class="pl-3 py-3 row w-100 row justify-content-xl-between justify-content-lg-start"
							>
								<div class="row w-100">
									<div class="col-md-2">
										<img
											class="cashier-img"
											:class="{
												'closed-cashier': !cashierIsOpen,
											}"
											:src="
												cashierInfo &&
												cashierInfo.user &&
												cashierInfo.user.avatar
													? '/storage/' + cashierInfo.user.avatar
													: activeUser && activeUser.avatar
													? '/storage/' + activeUser.avatar
													: '/images/default-avatar.png'
											"
										/>
									</div>
									<div class="col-md-8">
										<div class="ml-3 d-flex flex-column justify-content-center">
											<label class="card-title text-white uppercase"
												>{{
													cashierIsOpen ? "CAIXA ABERTO," : "CAIXA FECHADO,"
												}}
												<span style="color: #ffd25c">
													{{
														cashierIsOpen ? openCashierDate : closeCashierDate
													}}
												</span>
											</label>
											<p class="text-white">
												{{ userCashierName }}
											</p>
										</div>
									</div>
									<div class="col-md-2">
										<svg
											v-if="cashierIsOpen"
											class="lock-cashier-ico"
											width="60"
											height="60"
											viewBox="0 0 60 60"
											fill="none"
											xmlns="http://www.w3.org/2000/svg"
										>
											<path
												d="M43.6479 22.2599H43.4018V13.4024C43.4017 6.01295 37.3891 0 29.9994 0C22.6096 0 16.5971 6.01295 16.5971 13.4023C16.5971 15.3574 18.1825 16.9428 20.1371 16.9428C22.0922 16.9428 23.6776 15.3574 23.6776 13.4023C23.6776 9.9167 26.5135 7.0804 29.9995 7.0804C33.4855 7.0804 36.3214 9.9167 36.3214 13.4023V22.2598H16.353C13.0229 22.2598 10.3232 24.9598 10.3232 28.2895V53.9707C10.3232 57.3002 13.0228 60 16.353 60H43.6478C46.9767 60 49.6762 57.3004 49.6762 53.9707V28.2895C49.6764 24.9599 46.9767 22.2599 43.6479 22.2599ZM33.8408 44.2941C33.8408 46.4146 32.1211 48.1348 29.9994 48.1348C27.8785 48.1348 26.1589 46.4146 26.1589 44.2941V37.9672C26.1589 35.8464 27.8785 34.1267 30.0002 34.1267C32.1211 34.1267 33.8408 35.8464 33.8408 37.9672V44.2941Z"
												fill="#657C98"
											/>
										</svg>
										<svg
											v-else
											class="lock-cashier-ico"
											width="60"
											height="60"
											viewBox="0 0 60 60"
											fill="none"
											xmlns="http://www.w3.org/2000/svg"
										>
											<path
												d="M43.6464 22.2599H43.4015V13.4024C43.4015 6.01296 37.389 0 29.9992 0C22.6095 0 16.5964 6.01296 16.5964 13.4024V22.2599H16.3516C13.0227 22.2599 10.3232 24.9599 10.3232 28.2895V53.9708C10.3232 57.3003 13.0227 60 16.3516 60H43.6464C46.9764 60 49.676 57.3004 49.676 53.9708V28.2895C49.676 24.9599 46.9764 22.2599 43.6464 22.2599ZM23.677 13.4024C23.677 9.91671 26.5132 7.08052 29.9992 7.08052C33.4847 7.08052 36.3211 9.91671 36.3211 13.4024V22.2599H23.677V13.4024ZM33.8405 44.2941C33.8405 46.4147 32.1208 48.1348 29.9992 48.1348C27.8783 48.1348 26.1586 46.4146 26.1586 44.2941V37.9672C26.1586 35.8463 27.8784 34.1267 30.0001 34.1267C32.121 34.1267 33.8406 35.8464 33.8406 37.9672V44.2941H33.8405Z"
												fill="#657C98"
											/>
										</svg>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4 d-flex justify-content-end">
						<button
							v-if="cashierIsOpen"
							@click="handleExpense"
							class="btn action-btns expenses btn-primary btn-block h-75"
						>
							Lançamento de Despesas
						</button>
					</div>
				</div>
			</div>

			<div class="col-md-6 col-lg-4 col-xl-6 row justify-content-end">
				<div class="col-md-4 col-lg-5 col-xl-3 mb-md-2">
					<button
						v-if="true"
						class="btn action-btns btn-default btn-block h-75"
						@click="handleShowStatus"
					>
						Status
					</button>
				</div>

				<div class="col-md-4 col-lg-5 col-xl-3 mb-md-2">
					<button
						v-if="isCashierOwner"
						:disabled="disableCashier"
						class="btn action-btns btn-dark btn-block h-75"
						:class="{ 'btn-open-cashier': !cashierIsOpen }"
						@click="handleCashierAction()"
					>
						{{ cashierIsOpen ? "Fechar Caixa" : "Abrir Caixa" }}
					</button>
				</div>
			</div>
		</div>

		<div class="row">
			<div :class="closeScreen ? 'col-md-7' : 'col-md-6'">
				<div class="card card-body">
					<cashier-status
						ref="cashierStatus"
						v-if="closeScreen"
						@updateDiference="difference = $event"
					/>
					<orders-list
						ref="orderList"
						:cashierInfo="cashierInfo"
						@sale="handleOrderSelected"
					></orders-list>
				</div>

				<div
					v-if="closeScreen && cashierIsOpen"
					class="card total-and-diff-cashier d-flex flex-row justify-content-between align-items-center"
				>
					<div class="sub-total">
						Sub-total
						<b>{{ subtotal | currency }}</b>
					</div>

					<div class="input-group mb-0" style="max-width: 250px">
						<div class="input-group-prepend">
							<span class="input-group-text">TROCO</span>
						</div>
						<money class="form-control" v-model="charge" v-bind="money" />
					</div>

					<div class="difference">
						Diferença
						<b>{{ difference | currency }}</b>
					</div>

					<div class="difference deposit">
						Depósito
						<b>{{
							(depositValue - charge - difference - expenseValue) | currency
						}}</b>
					</div>
				</div>
			</div>

			<div :class="closeScreen ? 'col-md-5' : 'col-md-6'">
				<cashier-evidence
					ref="cashierEvidence"
					v-if="closeScreen && numForm !== 3"
				/>
				<!--				<div
									v-if="numForm === 0 && !closeScreen"
									class="d-flex align-items-center h-100 justify-content-center"
								><span>
										{{
											cashierIsOpen
												? "Selecione uma venda ao lado"
												: "Abra o caixa para poder efetuar operações"
										}}
									</span>
								</div>-->
				<div class="w-100 h-100" v-if="showStatus">
					<div class="card card-primary card-tabs">
						<div class="card-header p-0 pt-1">
							<ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
								<li class="nav-item" v-if="cashierIsOpen">
									<a
										:class="cashierIsOpen ? 'nav-link active' : 'nav-link'"
										id="current-status-tab"
										data-toggle="pill"
										href="#current-status"
										role="tab"
										aria-controls="current-status"
										aria-selected="false"
										@click.prevent.self="changeNumForm(3)"
									>
										Atual Caixa Aberto
									</a>
								</li>
								<li class="nav-item">
									<a
										:class="cashierIsOpen ? 'nav-link' : 'nav-link active'"
										id="history-tab"
										data-toggle="pill"
										href="#history"
										role="tab"
										aria-controls="history"
										@click.prevent.self="changeNumForm(3)"
										aria-selected="true"
										>Histórico</a
									>
								</li>
							</ul>
						</div>
						<div class="card-body">
							<div class="tab-content" id="tabs">
								<div
									:class="
										cashierIsOpen
											? 'tab-pane fade active show'
											: 'tab-pane fade'
									"
									id="current-status"
									role="tabpanel"
									aria-labelledby="custom-tabs-one-profile-tab"
								>
									<current-status-table
										ref="history"
										v-if="numForm === 3"
										:cashier-is-open="cashierIsOpen"
										:logged-user="activeUser"
										:user-is-admin="isAdminUser"
										@totalHistoryTable="totalHistory = $event"
										@showCashierPrint="showCashierPrint"
										@updateExpenseValue="handleExpenseValue"
									></current-status-table>
								</div>
								<div
									:class="
										cashierIsOpen
											? 'tab-pane fade'
											: 'tab-pane fade active show'
									"
									id="history"
									ref="history"
									role="tabpanel"
									aria-labelledby="custom-tabs-one-messages-tab"
								>
									<history-table
										ref="history"
										v-if="numForm === 3"
										:cashier-is-open="false"
										:logged-user="activeUser"
										:user-is-admin="isAdminUser"
										@totalHistoryTable="totalHistory = $event"
										@showCashierPrint="showCashierPrint"
										@updateExpenseValue="handleExpenseValue"
									></history-table>
								</div>
							</div>
						</div>
						<!-- /.card -->
					</div>
				</div>
				<payment-form
					v-if="numForm === 1"
					ref="paymentForm"
					:current-order="currentOrder"
					:charge-back-form="chargebackForm"
					:user="activeUser"
					:is-cashier-owner="isCashierOwner"
					:cashier-owner="cashierOwner"
					@selectedPaymentForm="selectedPaymentForm = $event"
					@selectedCancelForm="selectedCancelForm = $event"
					@successfulPayment="handlePaymentSucessful"
					@cancelOrder="showModalPassword()"
					@cancelChargeBack="cancelChargeBack()"
					@updateOrders="updateOrders()"
					@canceledOrder="handleCanceledOrder"
					@cancel="changeNumForm(0)"
					@confirm="handleConfirmButton(numForm)"
					@showCancelModal="showCancelModal"
				/>
				<expense-form
					v-if="numForm === 2"
					:maxValue="totalMoney"
					ref="expenseForm"
					@savedExpense="numForm = 0"
				/>
			</div>
		</div>

		<!--Modal password admin-->
		<div class="modal" id="passwordCancelOrder" tabindex="-1" role="dialog">
			<form
				class="modal-dialog modal-lg"
				role="document"
				style="max-width: 300px; margin: 20% auto"
				id="formAdminPass"
			>
				<div class="modal-content">
					<div class="modal-header">
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
						<div class="form-group">
							<label>SENHA DE APROVAÇÃO ADMIN</label>
							<input
								v-model="adminPass"
								type="password"
								name="adminPass"
								id="adminPass"
								class="w-100"
								ref="inputPass"
								readonly
								autocomplete="off"
								@focus="$refs.inputPass.removeAttribute('readonly')"
								@keypress.enter.prevent="validatePasswordAddition(adminPass)"
							/>
						</div>
					</div>
					<div class="modal-footer">
						<button
							type="button"
							@click="validatePasswordAddition(adminPass)"
							class="btn btn-lg btn-primary"
						>
							Confirmar
						</button>
					</div>
				</div>
			</form>
		</div>
		<CashierReportModal
			:cashier-data="cashierClosingData"
			:cashiers="cashiersData"
			@merge-cashiers="mergeCashiers"
			@restore-merge-cashiers="restoreMerge"
			@printReport="handlePrint()"
		/>
		<ModalBudget
			ref="modalBudget"
			:sellerSelected="seller_id"
			:lastOrder="lastOrder"
			:observation="observation"
			:warrantyText="warranty_text"
			:orcamentoText="orcamento_text"
			:address="address"
			:cellphone="cellphone"
			:email="email"
			@budgetPrint="budgetPrint"
		/>
		<ModalCancel
			ref="cancelModal"
			:cancel-order="cancelOrder"
			:address="address"
			:cellphone="cellphone"
			:email="email"
		/>
		<ModalCharge
			ref="chargeModal"
			:charge="charge"
			@changedCharge="handleChangeCharge"
		/>
	</div>
</template>

<script>
import axios from "axios";
import _ from "lodash";
import moment from "moment";
import toastr from "toastr";
import { Money } from "v-money";
import ModalBudget from "../Order/ModalBudget";
import CashierEvidence from "./CashierEvidence";
import CashierReportModal from "./CashierReportModal";
import CashierStatus from "./CashierStatus";
import CurrentStatusTable from "./CurrentStatusTable";
import ExpenseForm from "./ExpenseForm";
import HistoryTable from "./HistoryTable";
import ModalCancel from "./ModalCancel";
import OrdersList from "./OrdersList";
import PaymentForm from "./PaymentForm";
import ModalCharge from "./ModalCharge.vue";

export default {
	name: "Payment",

	components: {
		CurrentStatusTable,
		"orders-list": OrdersList,
		"payment-form": PaymentForm,
		"expense-form": ExpenseForm,
		"history-table": HistoryTable,
		"current-status-table": CurrentStatusTable,
		"cashier-status": CashierStatus,
		"cashier-evidence": CashierEvidence,
		Money,
		CashierReportModal,
		ModalBudget,
		ModalCharge,
		ModalCancel,
	},

	props: {
		loggedUser: {
			required: true,
		},
		userIsAdmin: {
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
		currencyWithouRS(value) {
			if (!value) return "0,00";
			return `${parseFloat(value).toLocaleString("pt-br", {
				minimumFractionDigits: 2,
			})}`;
		},
	},
	watch: {
		cashierIsOpen(val) {
			this.$store.commit("cashier/setIsOpen", val);
		},
		"cashierInfo.orders"(val) {
			let items = [];
			let initialValue = 0;
			items = val.map((order) => {
				return order.payments.filter((pay) => {
					return pay.payment_method.name === "Dinheiro";
				});
			});
			items.map((item) => {
				return item.forEach((pay) => {
					initialValue = Number(pay.value) + Number(initialValue);
				});
			});
			this.totalMoney = initialValue;
		},
	},
	data() {
		return {
			disableCashier: true,
			totalMoney: 0,
			moment: moment,
			currentOrder: null,
			numForm: 0,
			lastFormSelected: null,
			selectedPaymentForm: false,
			selectedCancelForm: false,
			chargebackForm: false,
			adminPass: null,
			totalHistory: null,
			cashierIsOpen: false,
			cashierInfo: {},
			closeScreen: false,
			subtotal: 0,
			difference: 0,
			charge: "",
			showCharge: false,
			shouldContinueCharge: false,
			cashierClosingData: {},
			depositValue: 0,
			expenseValue: 0,
			money: {
				decimal: ",",
				thousands: ".",
				prefix: "R$ ",
				suffix: "",
				precision: 2,
				masked: false,
				max: this.depositValue,
			},
			lastOrder: null,
			cancelOrder: null,
			easy_ddd: null,
			easy_postcode: null,
			maintenance_text: null,
			cupom_text: null,
			warranty_text: null,
			orcamento_text: null,
			address: null,
			cellphone: null,
			email: null,
			cashiersData: [],
			is_merged: false,
			showStatus: false,
		};
	},

	computed: {
		openCashierDate() {
			if (this.cashierInfo.created_at) {
				moment.locale("pt-br");
				return moment(this.cashierInfo.created_at).utc(-3).calendar();
			} else {
				return "Data desconhecida";
			}
		},
		closeCashierDate() {
			if (this.cashierInfo.close_time) {
				moment.locale("pt-br");
				return moment(this.cashierInfo.close_time).utc(-3).calendar();
			} else {
				return "";
			}
		},
		activeUser() {
			return JSON.parse(this.loggedUser);
		},
		userCashierName() {
			return this.cashierInfo.user ? this.cashierInfo.user.name : "";
		},
		isCashierOwner() {
			return (
				!this.cashierIsOpen ||
				(this.cashierIsOpen &&
					this.activeUser.id === this.cashierInfo.user_id) ||
				this.userIsAdmin
			);
		},
		cashierOwner() {
			return this.cashierInfo.user ? this.cashierInfo.user : {};
		},
		seller_id() {
			return this.lastOrder ? this.lastOrder.seller_id : null;
		},
		observation() {
			return this.lastOrder ? this.lastOrder.seller_id : null;
		},
		userLogged() {
			return this.userIsAdmin;
		},
		isAdminUser() {
			return this.userIsAdmin == 1;
		},
	},

	async created() {
		await this.getCashierInfo();
	},
	beforeMount() {
		this.disableCashier = true;
	},
	async mounted() {
		this.$root.collapseMenu();
		await this.getCashierInfo();

		$("#formAdminPass").on(
			"webkitAnimationEnd oanimationend msAnimationEnd animationend",
			function (e) {
				$("#formAdminPass").delay(200).removeClass("shakeeffect");
			}
		);

		try {
			let response = await axios.get("/configurations/get", {
				params: {
					keys: "easy_ddd,easy_postcode,maintenance_text,cupom_text,warranty_text,orcamento_text,address,cellphone,email",
				},
			});

			Object.keys(response.data).forEach((key) => {
				let obj = response.data[key];

				switch (obj.key) {
					case "easy_ddd":
						this.easy_ddd = obj.value.replace("55", "") + " ";
						break;
					case "easy_postcode":
						this.easy_postcode = obj.value;
						break;
					case "maintenance_text":
						this.maintenance_text = obj.value;
						break;
					case "cupom_text":
						this.cupom_text = obj.value;
						break;
					case "warranty_text":
						this.warranty_text = obj.value;
						break;
					case "orcamento_text":
						this.orcamento_text = obj.value;
						break;
					case "address":
						this.address = obj.value;
						break;
					case "cellphone":
						this.cellphone = obj.value;
						break;
					case "email":
						this.email = obj.value;
						break;
				}
			});
			this.disableCashier = false;
		} catch (error) {
			console.error(error);
			this.disableCashier = false;
		}
	},

	methods: {
		showCancelModal(cancelOrder) {
			this.cancelOrder = cancelOrder;
			$("#cancelModal").modal("show");
		},
		async handlePrint() {
			await this.showBudgetModalOrder(this.currentOrder.id || null);
		},
		cancelChargeBack() {
			this.chargebackForm = false;
			this.selectedCancelForm = false;
		},
		changeNumForm(numForm) {
			if (numForm === 3) {
				this.numForm = 3;
				this.showStatus = true;
				return;
			}

			if (numForm !== this.lastFormSelected || this.lastFormSelected === 0) {
				this.numForm = numForm;
				this.lastFormSelected = numForm;
				this.closeScreen = false;
			} else {
				this.numForm = 0;
				this.lastFormSelected = 0;
			}
		},
		handleShowStatus() {
			if (this.showStatus === false) {
				this.showStatus = true;
				this.changeNumForm(3);
				return;
			} else {
				this.showStatus = false;
				return;
			}
		},
		handleExpense() {
			if (this.cashierInfo.expenses.length > 0)
				return toastr.error("Uma despesa já foi lançada");
			else {
				this.showStatus = false;
				this.changeNumForm(2);
			}
		},
		async handleOrderSelected(event) {
			if (event.order) {
				this.currentOrder = event.order;
				this.currentOrder.cashierInfo = this.cashierInfo;

				if (
					["concluded", "returned", "partially_returned"].includes(
						event.order.status
					)
				) {
					this.currentOrder = await this.getOrderDetail(event.order.id);
				}
				this.numForm = event.formSelected;
			}
			this.showStatus = false;
		},
		async getOrderDetail(orderId) {
			return await axios
				.get(`/orders/${orderId}`, { params: { type: "vue", paginate: false } })
				.then(({ data }) => data);
		},
		async handleConfirmButton(numberForm) {
			try {
				if (numberForm === 1) {
					await this.$refs.paymentForm.submit();
				} else if (numberForm === 2) {
					await this.$refs.expenseForm.submit();
				}
			} catch (error) {
				console.error(error);
			}
		},
		handlePaymentSucessful(numberForm) {
			this.numForm = numberForm;
			this.$refs.orderList.getOrders(true);
		},
		async openCashier() {
			const { files, observations } = this.$refs.cashierEvidence.form;
			const postData = {
				observations,
				files,
			};
			try {
				const { data } = await axios.post("/cashierInfo", postData);

				if (data.id && data.created_at) {
					this.cashierIsOpen = true;
					this.cashierInfo = data;
					this.closeScreen = false;
					toastr.success("Caixa aberto com sucesso!");
				}
			} catch (e) {
				console.error("Error", e);
				toastr.error("Erro ao tentar abrir o caixa");
			}
		},
		async handleChangeCharge(e) {
			this.charge = e;
			this.shouldContinueCharge = true;
			return await this.closeCashier();
		},
		async closeCashier() {
			if (parseFloat(this.charge) > parseFloat(this.depositValue)) {
				return toastr.error(
					"O valor de troco, não pode exceder o valor de depósito."
				);
			}
			if (parseFloat(this.charge) <= 0 && this.shouldContinueCharge === false) {
				toastr.error("Sem troco");
				return $("#chargeModal").modal("show");
			}

			await this.$refs.cashierStatus.updateCashierInfo(this.cashierInfo.id);
			const { files, observations } = this.$refs.cashierEvidence.form;
			const postData = {
				observations,
				files,
				charge: this.charge,
				deposit: parseFloat(this.depositValue) - parseFloat(this.charge),
			};

			try {
				const resp = await axios.delete("/cashierInfo", {
					data: { ...postData },
				});
				if (resp.status === 200) {
					toastr.success("Caixa fechado com sucesso!");
					this.shouldContinueCharge = false;
					this.cashierInfo.close_time = resp.data.close_time;
					this.cashierIsOpen = false;
					this.numForm = 0;
					this.closeScreen = false;

					this.cashierClosingData = resp.data;

					this.showCashierPrint(resp.data);
					this.$forceUpdate();
				}
			} catch (e) {
				console.error("Error", e);
				toastr.error("Erro ao tentar fechar o caixa");
			}
		},
		async getCashierInfo() {
			try {
				const { data } = await axios.get("/cashierInfo", {
					params: {
						"with-closed": true,
					},
				});
				console.log("data");
				this.cashierInfo = data;
				if (data.id && !data.close_time) this.cashierIsOpen = true;
			} catch (error) {
				console.error("Error", error);
			}
		},
		async handleCashierAction() {
			this.showStatus = false;
			if (!this.closeScreen) {
				this.numForm = -1;
				this.closeScreen = true;
				await this.updateSubtotal();
				if (!this.cashierIsOpen) await this.getCashierHistory();
				return;
			}
			if (this.cashierIsOpen) await this.closeCashier();
			else this.openCashier();
		},
		async getCashierHistory() {
			try {
				const { data } = await axios.get("/cashierInfo/history", {
					params: { limit: 1 },
				});

				if (data[0] && data[0].earnings) {
					const earnings = data[0].earnings;
					data[0].earnings = [earnings[earnings.length - 1]];
					this.$refs.cashierStatus.cashierInfo = data[0];
				}
			} catch (error) {
				console.error("Error on fetching Cashier History", error);
			}
		},
		async updateSubtotal() {
			let url = "/cashierInfo";

			try {
				const { data } = await axios.get(url);

				if (data.earnings) {
					this.subtotal = data.earnings.reduce(
						(acc, hist) => (acc += parseFloat(hist.total)),
						0
					);

					this.depositValue = data.earnings
						.filter((el) => [null, 3, 5].includes(el.id))
						.reduce((acc, payment) => acc + parseFloat(payment.total), 0);
				}

				if (data.expenses && data.expenses.length > 0) {
					this.expenseValue = data.expenses
						.filter((exp) => [3, 5].includes(exp.payment_method_id))
						.reduce((acc, exp) => (acc += parseFloat(exp.value)), 0);
				}

				let previousCharge = 0;
				if (data.earnings) {
					previousCharge = data.earnings.find((el) => el.id === null).total;
				}

				console.table({
					subtotal: this.subtotal,
					"valor-deposito": this.depositValue,
					"valor-despesas": this.expenseValue,
					"troco-caixa-anterior": parseFloat(previousCharge),
				});
			} catch (error) {
				console.error(error);
			}
		},
		showModalPassword() {
			$("#passwordCancelOrder").modal("show");
			this.$refs.inputPass.focus();
		},
		async validatePasswordAddition(password) {
			const isOldOrder = moment(this.currentOrder.created_at).isBefore(
				new Date(),
				"day"
			);
			const data = isOldOrder
				? { oldPassword: password }
				: { password: password };
			await axios
				.post("secretPassword", data)
				.then((r) => {
					if (r.status === 200) {
						this.chargebackForm = true;
						$("#passwordCancelOrder").modal("hide");
					} else {
						toastr.options.positionClass = "toast-top-center";
						toastr.error("Senha de aprovação admin inválida!");
					}
				})
				.catch((e) => {
					console.error("Error in adminPass", e);
					$("#formAdminPass").addClass("shakeeffect");
					toastr.options.positionClass = "toast-top-center";
					toastr.error("Senha de aprovação admin inválida!");
				})
				.finally(() => {
					this.adminPass = null;
				});
		},
		updateOrders() {
			this.$refs.orderList.getOrders(true);
		},
		handleCanceledOrder() {
			this.numForm = 0;
			this.currentOrder = null;
		},
		async showBudgetModalOrder(orderId) {
			const params = { paginate: false, type: "vue" };
			if (orderId) params.search = orderId;
			if (!orderId) params.clientId = this.client.id;

			const url = orderId ? "/orders" : "/orders/last";

			await axios.get(url, { params }).then(({ data }) => {
				this.lastOrder = orderId ? data[0] : data;
				this.observation = this.note;
				this.openBudgetModal();
			});
		},
		openBudgetModal() {
			$("#budgetModal").modal("show");
		},
		budgetPrint() {
			$("#budgetModal").modal("hide");
		},
		handleExpenseValue(value) {
			this.expenseValue = value;
			this.$forceUpdate();
		},
		async showCashierPrint(cashier) {
			let payments = {};
			let formatCashier = cashier;
			const orders = cashier.orders;
			orders.forEach((order) => {
				if (order.payments.length > 1) {
					order["color"] = this.getColor();
				}
				order.payments.forEach((pay) => {
					if (_.isEmpty(payments[pay.payment_method.name])) {
						payments[pay.payment_method.name] = {
							total: 0,
							orders: [],
							id: pay.payment_method.id,
						};
					}
					if (pay.charge) console.log("CHaaarge", pay.charge);
					// if (!["returned", "partially_returned"].includes(order.status)) {
					let payValue = parseFloat(pay.value) - parseFloat(pay.charge);
					payments[pay.payment_method.name].total += payValue;
					// }
					payments[pay.payment_method.name].orders.push(order);
				});
			});
			formatCashier.payments = payments;
			this.cashierClosingData = formatCashier;
			await this.getCashierInfoMerge();
			await this.$forceUpdate();
			$("#cashierReportModal").modal("show");
		},
		async getCashierInfoMerge() {
			let url = "/cashierInfo";
			const config = {
				params: {
					"with-closed": true,
				},
			};
			config.params.date_ini = this.moment(
				this.cashierClosingData.created_at
			).format("YYYY-MM-DD");
			config.params.date_fim = this.moment(
				this.cashierClosingData.created_at
			).format("YYYY-MM-DD");

			try {
				let { data } = await axios.get(url, config);
				let allPayments = [];
				if (data.length > 1) {
					this.paymentsArray = [];
					this.cashiersData = data.map((cashier) => {
						let payments = [];
						const orders = cashier.orders;
						orders.forEach((order) => {
							order.payments.forEach((pay) => {
								if (_.isEmpty(payments[pay.payment_method.name])) {
									payments[pay.payment_method.name] = {
										total: 0,
										orders: [],
										id: pay.payment_method.id,
									};
								}
								if (pay.charge);
								// if (!["returned", "partially_returned"].includes(order.status)) {
								let payValue = parseFloat(pay.value) - parseFloat(pay.charge);
								payments[pay.payment_method.name].total += payValue;
								// }
								payments[pay.payment_method.name].orders.push(order);
							});
						});
						cashier.payments = payments;
						return cashier;
					});
				}
			} catch (error) {
				console.error(error);
			}
		},
		async mergeCashiers(date) {
			let url = "/cashierInfo";
			const config = {
				params: {
					"with-closed": true,
				},
			};
			config.params.date_ini = date;
			config.params.date_fim = date;
			let copyCashier = JSON.parse(JSON.stringify(this.cashierClosingData));
			copyCashier.orders = [];
			copyCashier.payments = [];
			this.cashierClosingData = null;
			try {
				let { data } = await axios.get(url, config);

				let app = this;
				if (data.length > 1) {
					let subtotalMerge = 0;
					let depositValueMerge = 0;
					data = data.map((cashier) => {
						const orders = cashier.orders;
						copyCashier.orders = copyCashier.orders.concat(orders);
						orders.forEach((order) => {
							if (order.payments.length > 1) {
								order["color"] = this.getColor();
							}
							order.payments.forEach((pay) => {
								if (_.isEmpty(copyCashier.payments[pay.payment_method.name])) {
									copyCashier.payments[pay.payment_method.name] = {
										total: 0,
										orders: [],
										id: pay.payment_method.id,
									};
								}
								let payValue = parseFloat(pay.value) - parseFloat(pay.charge);
								copyCashier.payments[pay.payment_method.name].total += payValue;
								copyCashier.payments[pay.payment_method.name].orders.push(
									order
								);
							});
						});
						if (cashier.earnings) {
							subtotalMerge += cashier.earnings.reduce(
								(acc, hist) => (acc += parseFloat(hist.total)),
								0
							);

							depositValueMerge += cashier.earnings
								.filter((el) => [null, 3, 5].includes(el.id))
								.reduce((acc, payment) => acc + parseFloat(payment.total), 0);
						}

						if (cashier.expenses && cashier.expenses.length > 0) {
							this.expenseValue = cashier.expenses
								.filter((exp) => [3, 5].includes(exp.payment_method_id))
								.reduce((acc, exp) => (acc += parseFloat(exp.value)), 0);
						}
						return cashier;
					});
					this.subtotal = subtotalMerge;
					this.depositValue = depositValueMerge;
					this.cashierClosingData = copyCashier;
					this.cashierClosingData.deposit = depositValueMerge;
					await this.$forceUpdate();
				}
			} catch (error) {
				console.error(error);
			}

			this.is_merged = true;
		},
		async restoreMerge() {
			this.is_merged = false;
			await this.getCashierInfo();
			await this.showCashierPrint(this.cashierInfo);
		},
		getColor() {
			return (
				"hsl(" +
				360 * Math.random() +
				"," +
				(25 + 70 * Math.random()) +
				"%," +
				(85 + 10 * Math.random()) +
				"%)"
			);
		},
	},
};
</script>

<style lang="scss" scoped>
.small-box {
	margin-bottom: 0;
}

.cashier-card {
	background-color: #3f5b7e;
	max-width: 420px;
	height: 75px;
}

.lock-cashier-ico {
	width: 2.5rem;
	height: 2.5rem;
	@media (max-width: 1080px) {
		width: 2.5rem;
		height: 2.5rem;
	}
}

.card-title {
	font-size: 0.75rem;
	margin-bottom: 0;
}

.cashier-card p {
	margin-bottom: 0;
	font-weight: 300;
	font-size: 1.375rem;
}

.cashier-img {
	border-radius: 4px;
	width: 3rem;
	height: 3rem;
}

.closed-cashier {
	filter: grayscale(1);
}

.total-and-diff-cashier {
	padding: 30px;

	.sub-total {
		color: #4b545c;
		width: auto;
	}

	.difference {
		width: auto;
		background: rgba(241, 244, 246, 0.7);
		border: 1px solid #e4e7ed;
		box-sizing: border-box;
		border-radius: 4px;
		color: #4b545c;
		padding: 15px;

		b {
			color: #d63030;
		}
	}

	.deposit {
		b {
			color: #24963e;
		}
	}
}

.total-sells {
	background-color: #24963e;

	& .total-info {
		border-bottom: 1px solid #66b578;
	}

	& p {
		margin-bottom: 0;
		font-weight: 300;
		font-size: 1.375rem;
	}

	& .last-access {
		font-size: 14px;
		font-weight: 700;
	}
}

.cancel-button {
	background-color: #d63030;
	opacity: 0.75;
	padding: 10px 40px;
}

.confirm-button {
	background-color: #21316f;
	padding: 10px 40px;
}

.action-btns {
	line-height: 1.2;
	min-height: 55px;

	/* @media (max-width: 768px) {
    min-height: 100px;
    margin-bottom: 15px !important;
    padding-right: 0;
  } */

	&.expenses {
		max-width: 150px;

		@media (max-width: 768px) {
			min-height: 55px;
			margin-bottom: 15px !important;
			width: 100%;
		}
	}
}

.btn-open-cashier {
	background: #299348;
	border: 1px solid #e4e7ed;
	box-sizing: border-box;
	border-radius: 4px;
}

// shake effect on AdminPassModal
.shakeeffect {
	-webkit-animation: kf_shake 0.4s 1 linear;
	-moz-animation: kf_shake 0.4s 1 linear;
	-o-animation: kf_shake 0.4s 1 linear;
}

@-webkit-keyframes kf_shake {
	0% {
		-webkit-transform: translate(30px);
	}
	20% {
		-webkit-transform: translate(-30px);
	}
	40% {
		-webkit-transform: translate(15px);
	}
	60% {
		-webkit-transform: translate(-15px);
	}
	80% {
		-webkit-transform: translate(8px);
	}
	100% {
		-webkit-transform: translate(0px);
	}
}

@-moz-keyframes kf_shake {
	0% {
		-moz-transform: translate(30px);
	}
	20% {
		-moz-transform: translate(-30px);
	}
	40% {
		-moz-transform: translate(15px);
	}
	60% {
		-moz-transform: translate(-15px);
	}
	80% {
		-moz-transform: translate(8px);
	}
	100% {
		-moz-transform: translate(0px);
	}
}

@-o-keyframes kf_shake {
	0% {
		-o-transform: translate(30px);
	}
	20% {
		-o-transform: translate(-30px);
	}
	40% {
		-o-transform: translate(15px);
	}
	60% {
		-o-transform: translate(-15px);
	}
	80% {
		-o-transform: translate(8px);
	}
	100% {
		-o-origin-transform: translate(0px);
	}
}
</style>
