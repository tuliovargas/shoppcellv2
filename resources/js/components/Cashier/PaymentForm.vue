<template>
	<div>
		<template v-if="currentOrder">
			<div class="card p-4">
				<div class="row mb-0">
					<div class="col-md-4">
						<label class="mb-0">CLIENTE</label>
						<p class="text-gray mb-0">
							{{ currentOrder.client.full_name }}
						</p>
					</div>
					<div
						class="col-md-8 d-flex justify-content-between align-items-center"
					>
						<div>
							<label class="mb-0">CPF</label>
							<p class="text-gray mb-0">
								{{ currentOrder.client.cpf }}
							</p>
						</div>
						<div v-if="!chargeBackForm">
							<!--							<button
								class="btn"
								title="Listar produtos"
								@click="showProductsModal"
							>
								<i class="fas fa-shopping-bag" style="font-size: 25px"></i>
							</button>-->
							<button
								class="btn"
								title="Cancelar pedido"
								@click="handleCancelOrder"
							>
								<i class="fas fa-thumbs-down" style="font-size: 25px"></i>
							</button>
							<button
								v-if="alreadyPaid"
								class="btn"
								@click="handlePrintOrder()"
								title="Imprimir comprovante"
							>
								<i class="fas fa-print" style="font-size: 25px"></i>
							</button>
							<template v-if="currentOrder.status !== 'concluded'">
								<button
									v-if="!alreadyPaid"
									class="btn text-danger"
									title="Cancelar"
									@click="cancel"
								>
									<i class="fas fa-ban" style="font-size: 25px"></i>
								</button>
								<button
									v-if="
										!alreadyPaid &&
										(!totalLeftOver || totalLeftOver <= 0) &&
										$store.getters['cashier/getIsOpen']
									"
									class="btn text-success"
									title="Confirmar Pagamento"
									@click="confirm"
								>
									<i class="fas fa-handshake" style="font-size: 25px"></i>
								</button>
							</template>
						</div>
						<div v-else>
							<button
								class="btn"
								title="Cancelar estorno"
								@click="cancelChargeBackForm()"
							>
								<i class="fas fa-times-circle" style="font-size: 25px"></i>
							</button>
							<button
								class="btn"
								title="Confirmar estorno"
								@click="submitChargeback()"
							>
								<i class="fas fa-check-circle" style="font-size: 25px"></i>
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="card p-4" style="max-height: 200px; overflow-y: scroll">
				<div class="row mb-0">
					<div class="col-md-12">
						<label class="mb-0">PRODUTOS</label>
						<table class="table table-bordered w-100 text-secondary">
							<thead>
								<tr style="cursor: default">
									<th class="th__prod-name" witdth="35%">Produto</th>
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
								</tr>
							</thead>
							<tbody>
								<template v-for="(prod, index) in currentOrder.products">
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
										style="cursor: default; background-color: #ccc"
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
			</div>

			<div
				class="payment-form-wrapper"
				v-if="!chargeBackForm && $store.getters['cashier/getIsOpen']"
			>
				<template v-for="(pay_method, index) in form.payments_method">
					<div class="card" :key="index">
						<div
							class="form-group mb-0"
							v-if="
								!pay_method.type_payment && currentOrder.products.length > 0
							"
						>
							<label>FORMAS DE PAGAMENTO</label>
							<select-square-options
								v-if="!alreadyPaid"
								:options="paymentOptions"
								v-model="pay_method.type_payment"
								@input="addPaymentMethod($event, pay_method)"
							/>
							<div
								v-else
								class="fa-3x d-flex justify-content-center flex-grow-1"
							>
								<i class="fas fa-spinner fa-spin"></i>
							</div>
						</div>

						<!--FORMAS DE PAGAMENTO-->
						<div class="form-group mb-0" v-if="pay_method.type_payment">
							<div class="d-flex justify-content-between mb-3">
								<div>
									<label>FORMA DE PAGAMENTO - </label>
									<span>{{ pay_method.payment_label.text }}</span>
								</div>
								<div>
									<button
										:disabled="alreadyPaid"
										class="btn delete-icon delete-button"
										@click="removePayment(index)"
									>
										<span
											class="d-flex justify-content-center align-items-center"
										>
											<i class="far fa-trash-alt"></i>
										</span>
									</button>
								</div>
							</div>

							<!-- Quando for cartão -->
							<template v-if="[1, 2].includes(pay_method.type_payment)">
								<div class="payment-method__credit-card">
									<div class="row">
										<div class="col-md-6">
											<label>VALOR TOTAL A PAGAR</label>
											<money
												:disabled="alreadyPaid"
												class="money"
												:class="{ 'is-invalid': false }"
												v-model="pay_method.value"
												v-bind="money"
												@keydown="
													$event.key === '-' ? $event.preventDefault() : null
												"
											></money>
											<div class="pt-3">
												<button
													class="btn btn-cc"
													:class="{
														selected: selectedBrand[index] == 'mastercard',
													}"
													@click="
														handleBrandSelected('mastercard', index, pay_method)
													"
												>
													<span>
														<svg
															width="34"
															height="24"
															viewBox="0 0 34 24"
															fill="none"
															xmlns="http://www.w3.org/2000/svg"
														>
															<rect
																x="0.5"
																y="0.5"
																width="33"
																height="23"
																rx="3.5"
																fill="white"
																stroke="#D9D9D9"
															/>
															<path
																fill-rule="evenodd"
																clip-rule="evenodd"
																d="M17.179 16.8294C15.9949 17.8275 14.459 18.43 12.7807 18.43C9.03582 18.43 6 15.4303 6 11.73C6 8.02966 9.03582 5.02997 12.7807 5.02997C14.459 5.02997 15.9949 5.63247 17.179 6.63051C18.363 5.63247 19.8989 5.02997 21.5773 5.02997C25.3221 5.02997 28.358 8.02966 28.358 11.73C28.358 15.4303 25.3221 18.43 21.5773 18.43C19.8989 18.43 18.363 17.8275 17.179 16.8294Z"
																fill="#ED0006"
															/>
															<path
																fill-rule="evenodd"
																clip-rule="evenodd"
																d="M17.1787 16.8294C18.6366 15.6005 19.5611 13.7719 19.5611 11.73C19.5611 9.68801 18.6366 7.85941 17.1787 6.63051C18.3628 5.63247 19.8987 5.02997 21.577 5.02997C25.3219 5.02997 28.3577 8.02966 28.3577 11.73C28.3577 15.4303 25.3219 18.43 21.577 18.43C19.8987 18.43 18.3628 17.8275 17.1787 16.8294Z"
																fill="#F9A000"
															/>
															<path
																fill-rule="evenodd"
																clip-rule="evenodd"
																d="M17.1793 16.8294C18.6372 15.6005 19.5616 13.7719 19.5616 11.73C19.5616 9.68804 18.6372 7.85944 17.1793 6.63054C15.7213 7.85944 14.7969 9.68804 14.7969 11.73C14.7969 13.7719 15.7213 15.6005 17.1793 16.8294Z"
																fill="#FF5E00"
															/>
														</svg>
													</span>
												</button>
												<button
													class="btn btn-cc"
													:class="{
														selected:
															selectedBrand[index] == 'american_express',
													}"
													@click="
														handleBrandSelected(
															'american_express',
															index,
															pay_method
														)
													"
												>
													<span>
														<svg
															width="34"
															height="24"
															viewBox="0 0 34 24"
															fill="none"
															xmlns="http://www.w3.org/2000/svg"
														>
															<rect
																width="34"
																height="24"
																rx="4"
																fill="#1F72CD"
															/>
															<rect
																width="34"
																height="24"
																rx="4"
																fill="url(#paint0_linear)"
															/>
															<path
																fill-rule="evenodd"
																clip-rule="evenodd"
																d="M6.09517 8.5L2.91406 15.7467H6.7223L7.19441 14.5913H8.27355L8.74566 15.7467H12.9375V14.8649L13.311 15.7467H15.4793L15.8528 14.8462V15.7467H24.5706L25.6307 14.6213L26.6232 15.7467L31.1009 15.7561L27.9097 12.1436L31.1009 8.5H26.6927L25.6608 9.60463L24.6995 8.5H15.2156L14.4013 10.3704L13.5678 8.5H9.7675V9.35186L9.34474 8.5H6.09517ZM6.83205 9.52905H8.68836L10.7984 14.4431V9.52905H12.8319L14.4617 13.0524L15.9637 9.52905H17.987V14.7291H16.7559L16.7458 10.6544L14.9509 14.7291H13.8495L12.0446 10.6544V14.7291H9.51179L9.03162 13.5633H6.43745L5.95827 14.728H4.60123L6.83205 9.52905ZM24.1196 9.52905H19.1134V14.726H24.0421L25.6307 13.0036L27.1618 14.726H28.7624L26.436 12.1426L28.7624 9.52905H27.2313L25.6507 11.2316L24.1196 9.52905ZM7.73508 10.4089L6.8804 12.4856H8.58876L7.73508 10.4089ZM20.3497 11.555V10.6057V10.6048H23.4734L24.8364 12.1229L23.413 13.6493H20.3497V12.613H23.0808V11.555H20.3497Z"
																fill="white"
															/>
															<defs>
																<linearGradient
																	id="paint0_linear"
																	x1="3.5"
																	y1="4"
																	x2="17"
																	y2="24"
																	gradientUnits="userSpaceOnUse"
																>
																	<stop stop-color="#85CAE5" />
																	<stop offset="1" stop-color="#2778AF" />
																</linearGradient>
															</defs>
														</svg>
													</span>
												</button>
												<button
													class="btn btn-cc"
													:class="{
														selected: selectedBrand[index] == 'visa',
													}"
													@click="
														handleBrandSelected('visa', index, pay_method)
													"
												>
													<span>
														<svg
															width="34"
															height="24"
															viewBox="0 0 34 24"
															fill="none"
															xmlns="http://www.w3.org/2000/svg"
														>
															<rect
																x="0.5"
																y="0.5"
																width="33"
																height="23"
																rx="3.5"
																fill="white"
																stroke="#D9D9D9"
															/>
															<path
																fill-rule="evenodd"
																clip-rule="evenodd"
																d="M10.7501 15.8582H8.69031L7.14576 9.79237C7.07245 9.51334 6.91679 9.26667 6.68782 9.1504C6.11639 8.85823 5.48672 8.62571 4.7998 8.50843V8.2749H8.11789C8.57583 8.2749 8.91929 8.62571 8.97653 9.03313L9.77793 13.4086L11.8367 8.2749H13.8392L10.7501 15.8582ZM14.984 15.8582H13.0388L14.6406 8.2749H16.5858L14.984 15.8582ZM19.1025 10.3757C19.1597 9.96728 19.5032 9.73374 19.9039 9.73374C20.5336 9.67511 21.2195 9.79238 21.7919 10.0835L22.1354 8.45081C21.5629 8.21727 20.9333 8.1 20.3619 8.1C18.4738 8.1 17.1 9.15041 17.1 10.6082C17.1 11.7173 18.0731 12.2996 18.7601 12.6504C19.5032 13.0002 19.7894 13.2338 19.7322 13.5835C19.7322 14.1082 19.1597 14.3418 18.5883 14.3418C17.9014 14.3418 17.2145 14.1669 16.5858 13.8747L16.2424 15.5085C16.9293 15.7996 17.6724 15.9169 18.3594 15.9169C20.4763 15.9745 21.7919 14.9251 21.7919 13.35C21.7919 11.3665 19.1025 11.2502 19.1025 10.3757ZM28.5998 15.8582L27.0553 8.2749H25.3962C25.0528 8.2749 24.7093 8.50843 24.5948 8.85823L21.7347 15.8582H23.7372L24.1369 14.7502H26.5973L26.8263 15.8582H28.5998ZM25.6824 10.3171L26.2539 13.1751H24.6521L25.6824 10.3171Z"
																fill="#1A1F71"
															/>
														</svg>
													</span>
												</button>
												<button
													class="btn btn-cc"
													:class="{
														selected: selectedBrand[index] == 'elo',
													}"
													@click="handleBrandSelected('elo', index, pay_method)"
												>
													<span>
														<svg
															width="34"
															height="24"
															viewBox="0 0 34 24"
															fill="none"
															xmlns="http://www.w3.org/2000/svg"
														>
															<rect
																x="0.5"
																y="0.5"
																width="33"
																height="23"
																rx="3.5"
																fill="#0E0E11"
																stroke="#D9D9D9"
															/>
															<path
																d="M7.82406 9.49573C8.11175 9.40141 8.41952 9.35019 8.73975 9.35019C10.1373 9.35019 11.3031 10.3241 11.5705 11.6178L13.5509 11.2216C13.0965 9.0229 11.1154 7.3678 8.73975 7.3678C8.19585 7.3678 7.67231 7.45472 7.18359 7.61484L7.82406 9.49573Z"
																fill="#FAEC32"
															/>
															<path
																d="M5.46727 15.8677L6.65306 14.3366C6.12376 13.8012 5.78968 13.0196 5.78968 12.1484C5.78968 11.2778 6.12344 10.4962 6.6526 9.96121L5.46618 8.4303C4.56678 9.33989 4 10.6685 4 12.1484C4 13.629 4.56761 14.9581 5.46727 15.8677Z"
																fill="#2091C3"
															/>
															<path
																d="M11.5711 12.6802C11.3028 13.9737 10.1377 14.9464 8.74142 14.9464C8.42105 14.9464 8.11268 14.895 7.82497 14.8004L7.18359 16.6822C7.67307 16.8432 8.19677 16.9302 8.74142 16.9302C11.1149 16.9302 13.0947 15.2762 13.5509 13.0783L11.5711 12.6802Z"
																fill="#D0352A"
															/>
															<path
																fill-rule="evenodd"
																clip-rule="evenodd"
																d="M23.0798 8.4303V13.8716L24.0563 14.263L23.5944 15.3362L22.6287 14.948C22.4116 14.857 22.2647 14.7183 22.153 14.5616C22.0458 14.4013 21.9664 14.1822 21.9664 13.8865V8.4303H23.0798ZM15.6742 12.4744C15.6984 10.9184 17.0236 9.67627 18.6315 9.70011C19.9963 9.7208 21.1271 10.6452 21.4241 11.8746L16.1457 14.0562C15.8391 13.6029 15.6648 13.0576 15.6742 12.4744ZM16.8819 12.6926C16.8747 12.6273 16.8696 12.5603 16.8715 12.4928C16.8867 11.576 17.6668 10.8445 18.6142 10.8597C19.1298 10.8663 19.5877 11.0947 19.8972 11.4481L16.8819 12.6926ZM19.7861 13.7028C19.468 14.002 19.0366 14.1843 18.5603 14.1778C18.2338 14.1725 17.9312 14.078 17.674 13.9205L17.0364 14.903C17.4731 15.1699 17.988 15.3278 18.5431 15.3361C19.3512 15.3478 20.0877 15.0408 20.6232 14.534L19.7861 13.7028ZM27.1438 10.8596C26.9536 10.8596 26.7708 10.8894 26.6 10.9448L26.22 9.84383C26.51 9.75019 26.8206 9.69933 27.1438 9.69933C28.5541 9.69933 29.7306 10.6681 30.0004 11.9549L28.8242 12.1868C28.6657 11.4294 27.9736 10.8596 27.1438 10.8596ZM25.2127 14.629L26.0074 13.76C25.6524 13.4561 25.4289 13.0123 25.4289 12.5176C25.4289 12.0235 25.6524 11.5799 26.0071 11.2763L25.2118 10.4072C24.6088 10.9236 24.2289 11.678 24.2289 12.5176C24.2289 13.3583 24.6091 14.1125 25.2127 14.629ZM27.1437 14.1763C27.9727 14.1763 28.6648 13.607 28.8242 12.8507L30 13.0836C29.7289 14.369 28.5527 15.3364 27.1437 15.3364C26.8203 15.3364 26.5093 15.2854 26.2185 15.1913L26.5994 14.0908C26.7704 14.146 26.9534 14.1763 27.1437 14.1763Z"
																fill="white"
															/>
														</svg>
													</span>
												</button>
											</div>
										</div>
										<div class="col-md-6">
											<label v-if="pay_method.type_payment === 1"
												>QUANTIDADE DE PARCELAS</label
											>
											<select
												:disabled="alreadyPaid"
												v-if="pay_method.type_payment === 1"
												class="form-control mr-2"
												@change="
													installmentSelection($event.target.value, pay_method)
												"
											>
												<option
													v-if="alreadyPaid"
													:value="null"
													selected
													disabled
												>
													{{ pay_method.installment_data.text }}
												</option>
												<template v-for="(item, index) in installments_data">
													<option :value="index" :key="index">
														{{ item.text }}
													</option>
												</template>
											</select>
											<div
												class="total-parcels"
												v-if="
													pay_method.type_payment === 1 &&
													pay_method.installment_data.installments
												"
											>
												{{ pay_method.installment_data.installments }}x de
												<b>
													{{
														formatReal(
															getCalculedInstallment(pay_method).toFixed(2)
														)
													}}*</b
												>
											</div>
										</div>
									</div>
								</div>
							</template>

							<!-- quando for dinheiro-->
							<template v-else-if="pay_method.type_payment == 3">
								<div class="row">
									<div class="col-md-6">
										<label class="py-2 mb-0">Valor recebido</label>
									</div>
									<div class="col-md-6">
										<money
											:disabled="alreadyPaid"
											class="money"
											:class="{ 'is-invalid': false }"
											v-model="pay_method.value"
											v-bind="money"
											@keydown="
												$event.key === '-' ? $event.preventDefault() : null
											"
										></money>
									</div>
								</div>
							</template>

							<!-- quando for PIX-->
							<template v-else-if="pay_method.type_payment == 4">
								<div class="row">
									<div class="col-md-6">
										<label>NÚMERO DO PIX</label>
										<input
											:disabled="alreadyPaid"
											type="text"
											class="form-control mr-2"
											v-model="pay_method.PIX"
										/>
									</div>
									<div class="col-md-6">
										<label>VALOR</label>
										<money
											:disabled="alreadyPaid"
											class="money"
											:class="{ 'is-invalid': false }"
											v-model="pay_method.value"
											v-bind="money"
											@keydown="
												$event.key === '-' ? $event.preventDefault() : null
											"
										></money>
									</div>
								</div>
							</template>

							<!-- quando for cheque-->
							<template v-else-if="pay_method.type_payment == 5">
								<div class="row">
									<div class="col-md-12 mb-3">
										<label>NÚMERO DO CHEQUE</label>
										<input
											:disabled="alreadyPaid"
											type="text"
											class="form-control mr-2"
											v-model="pay_method.bank_check_data.check_number"
										/>
									</div>
									<div class="col-md-12 mb-3">
										<label>NOME DO CHEQUE</label>
										<input
											:disabled="alreadyPaid"
											type="text"
											class="form-control mr-2"
											v-model="pay_method.bank_check_data.check_name"
										/>
									</div>
									<div class="col-6">
										<label>BANCO</label>
										<select
											:disabled="alreadyPaid"
											class="form-control mr-2"
											v-model="pay_method.bank_check_data.bank_id"
										>
											<option :value="null" disabled selected>
												Selecione o Banco
											</option>
											<template v-for="(bank, i) in banks">
												<option :value="bank.value" :key="i">
													{{ bank.text }}
												</option>
											</template>
										</select>
									</div>

									<div class="col-6">
										<label>VALOR</label>
										<money
											:disabled="alreadyPaid"
											class="money"
											:class="{ 'is-invalid': false }"
											v-model="pay_method.value"
											v-bind="money"
											@keydown="
												$event.key === '-' ? $event.preventDefault() : null
											"
										></money>
									</div>
								</div>
							</template>
						</div>
						<div
							class="form-group mb-0"
							v-if="currentOrder.products.length == 0"
						>
							<div class="d-flex justify-content-between mb-3">
								<div>
									<label>FORMA DE PAGAMENTO - </label>
									<span>Indeterminado</span>
								</div>
							</div>

							<template>
								<div class="row">
									<div class="col-md-12">
										<label class="py-2 mb-0"
											>Venda proveniente de outra base de dados. Forma de
											pagamento não presente na Ordem.</label
										>
									</div>
								</div>
							</template>
						</div>
					</div>
				</template>
				<div
					class="d-flex justify-content-center w-100 mb-2"
					v-if="lastFormSelected && !alreadyPaid"
				>
					<a href="#" @click="addPayment()">
						<i class="fa fa-plus"></i> Adicionar outra forma de pagamento
					</a>
				</div>
			</div>
			<!--FIM FORMAS DE PAGAMENTO-->
			<div class="card p-4 mb-0" v-if="hasPaymentForm && !chargeBackForm">
				<div class="row">
					<!-- OBSERVAÇÂO -->
					<div class="col-6 px-3">
						<div class="form-group">
							<label for="observacao">OBSERVAÇÃO</label>
							<textarea
								:disabled="alreadyPaid"
								class="form-control"
								rows="3"
								id="observacao"
								name="observacao"
								placeholder="Observação"
								v-model="form.observations"
							>
							</textarea>
						</div>
					</div>
					<!-- CUPOM -->
					<div class="col-6 px-3">
						<div class="form-group" :class="{ 'mb-1': couponStatus }">
							<label for="observacao">CUPOM DE DESCONTO</label>
							<input
								:disabled="alreadyPaid || currentOrder.coupon"
								type="text"
								class="form-control"
								v-model="form.coupon_discount"
								@input="discountCouponValidator"
							/>

							<small
								v-if="form.coupon_discount != '' && couponStatus == 'Válido'"
								class="text-success"
								>Cupom {{ couponStatus }}</small
							>
							<small
								v-if="
									(form.coupon_discount != '' && couponStatus == 'Inválido') ||
									couponStatus == 'Não encontrado'
								"
								class="text-danger"
								>Cupom {{ couponStatus }}</small
							>
						</div>

						<div
							class="total-value d-flex justify-content-between mb-3"
							v-if="form.taxValue"
						>
							<span>Juros</span>
							<b>{{ formatReal(form.taxValue) }}</b>
						</div>
						<div
							class="total-value d-flex justify-content-between mb-3"
							v-if="form.discountValue > 0"
						>
							<span>Desconto</span>
							<b>R$ {{ form.discountValue }}</b>
						</div>
						<div
							v-if="couponValueDiscount"
							class="total-value d-flex justify-content-between mb-3"
						>
							<span>Desconto (Cupom)</span>
							<b>R$ {{ couponValueDiscount }}</b>
						</div>
						<div
							v-if="totalLeftOver && parseFloat(totalLeftOver) !== 0"
							class="total-value d-flex justify-content-between mb-3"
							:class="{
								missing: totalLeftOver > 0,
								change: !totalLeftOver > 0,
							}"
						>
							<span>{{
								alreadyPaid
									? "Troco"
									: parseFloat(totalLeftOver) > 0
									? "Restante"
									: "Troco"
							}}</span>
							<b>{{ formatReal(totalLeftOver) }}</b>
						</div>
						<div class="total-value d-flex justify-content-between mb-0">
							<span>Total</span>
							<b>{{ formatReal(form.totalValue) }}</b>
						</div>
					</div>
				</div>
			</div>
			<div v-if="chargeBackForm">
				<div v-if="alreadyPaid" class="card p-4">
					<label>SOLICITAR ESTORNO</label>
					<div class="d-flex">
						<button
							id="btn-money"
							class="btn btn-primary btn-chargeback"
							@click="handleChargebackType('cash')"
						>
							{{ isCardChargeBack ? "Estorno Maquina/Dinheiro" : "Dinheiro" }}
						</button>
						<button
							id="btn-coupon"
							class="btn btn-primary btn-chargeback"
							@click="handleChargebackType('coupon')"
						>
							Cupom
						</button>
					</div>
				</div>
				<div v-if="alreadyPaid" class="card p-4">
					<div class="row">
						<div class="col-md-6 d-flex align-items-center">
							<input
								type="checkbox"
								name="return-total-stock"
								v-model="isTotalChargeBack"
								@change="handleTypeChargeback('total')"
							/>
							<label
								@click="handleTypeChargeback('total')"
								class="mb-0 ml-1 pointer"
								><b>ESTORNO TOTAL</b></label
							>
						</div>
						<div class="col-md-6 d-flex align-items-center">
							<input
								type="checkbox"
								name="return-parcial-stock"
								v-model="isParcialChargeBack"
								@change="handleTypeChargeback('parcial')"
							/>
							<label
								@click="handleTypeChargeback('parcial')"
								class="mb-0 ml-1 pointer"
								><b>ESTORNO PARCIAL</b></label
							>
						</div>
					</div>
				</div>
				<div
					class="card p-4 input-group"
					v-if="formChargeback.type === 'parcial'"
				>
					<label>PRODUTOS</label>
					<template v-for="(prod, i) in chargebackProducts">
						<div class="d-flex align-items-center" :key="i">
							<input
								type="checkbox"
								@change="handleChargebackProduct(prod)"
								v-model="prod.isChecked"
								:disabled="prod.canceled_at"
							/>
							<p
								@click="handleChargebackProduct(prod)"
								class="mb-0 ml-1"
								:class="{ pointer: !prod.canceled_at }"
							>
								{{ prod.product.name }}
								{{
									prod.canceled_at
										? ` estornado em ${moment(prod.canceled_at).format(
												"DD/MM/YYYY HH:MM"
										  )}`
										: ""
								}}
							</p>
						</div>
					</template>
				</div>
				<div class="card p-4">
					<label>OBSERVAÇÃO</label>
					<textarea
						name=".maintenance_info.issue"
						class="form-control w-100"
						rows="3"
						placeholder="Observações do estorno..."
						style="resize: none"
						v-model="formChargeback.observation"
					></textarea>
				</div>
			</div>
		</template>
		<ModalBudget
			ref="modalBudget"
			:lastOrder="lastOrder"
			:warrantyText="warranty_text"
			:orcamentoText="orcamento_text"
			:address="address"
			:cellphone="cellphone"
			:email="email"
			@budgetPrint="budgetPrint"
		/>

		<ListProductsModal :products="currentOrder.products" />
	</div>
</template>

<script>
import { Money } from "v-money";
import axios from "axios";
import toastr from "toastr";
import _ from "lodash";
import moment from "moment";
import { formatReal } from "../helpers/number";
import ModalBudget from "../Order/ModalBudget";
import ListProductsModal from "./ListProductsModal";

export default {
	name: "PaymentForm",

	components: { Money, ModalBudget, ListProductsModal },

	props: {
		currentOrder: {
			required: true,
			default: 0,
		},
		chargeBackForm: {
			default: false,
		},
		user: {
			required: true,
			default: () => {},
		},
		isCashierOwner: {
			required: true,
			default: () => {},
		},
		cashierOwner: {
			required: true,
			default: () => {},
		},
	},

	data() {
		return {
			form: {
				sale: this.currentOrder,
				observations: null,
				payments_method: [
					{
						type_payment: null,
						payment_label: null,
						value: 0,
						installment_data: {},
						bank_check_data: {},
					},
				],
				coupon_discount: "",
				totalValue: null,
				discountValue: 0,
				taxValue: 0,
				chargeBack: {},
			},
			formChargeback: {
				products: [],
				by_products: [],
				observation: null,
				user_id: this.user.id,
				type: "",
				type_chargeback: "",
			},
			paymentOptions: [
				{ value: 1, text: "Cartão crédito" },
				{ value: 2, text: "Cartão débito" },
				{ value: 3, text: "Dinheiro" },
				{ value: 4, text: "Pix" },
				{ value: 5, text: "Cheque" },
			],
			banks: [],
			installments_data: [],
			totalLeftOver: null,
			totalCouponValue: null,
			couponStatus: null,
			couponValueDiscount: 0,
			hasPaymentForm: false,
			selectedBrand: [],
			money: {
				decimal: ",",
				thousands: ".",
				prefix: "R$ ",
				suffix: "",
				precision: 2,
				masked: false,
			},
			lastOrder: null,
			loadingFillData: false,
			totalProductChargeback: false,
			isTotalChargeBack: false,
			isParcialChargeBack: false,
			isCardChargeBack: false,
			isRemoveLastPay: false,
			moment: moment,
			easy_ddd: "",
			easy_postcode: "",
			maintenance_text: "",
			cupom_text: "",
			warranty_text: "",
			orcamento_text: "",
			cellphone: "",
			address: "",
			email: "",
		};
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
		"form.payments_method": {
			handler(paymentMethods) {
				let totalPaid = 0;

				paymentMethods.forEach((payment) => {
					totalPaid += parseFloat(Number(payment.value));
					if (
						(payment.type_payment === 1 || payment.type_payment === 2) &&
						payment.installment_data &&
						payment.installment_data.installments
					) {
						this.form.taxValue = (
							((payment.installment_data.interest_rates * payment.value) /
								100) *
							payment.installment_data.installments
						).toFixed(2);
					}

					payment;
				});

				if (this.isRemoveLastPay) {
					this.isRemoveLastPay = false;
					this.totalLeftOver = this.couponValueDiscount
						? parseFloat(this.currentOrder.total) - this.couponValueDiscount
						: parseFloat(this.currentOrder.total);
					if (this.couponValueDiscount) {
						const pays = this.form.payments_method;
						pays[pays.length - 1].value -= this.couponValueDiscount;
					}
				}

				if (this.currentOrder.total - totalPaid !== 0) {
					const total = Number(this.currentOrder.total);
					const couponValue = this.couponValueDiscount;
					this.totalLeftOver = (total - totalPaid - couponValue).toFixed(2);
				} else {
					const total = this.currentOrder.discount
						? parseFloat(this.currentOrder.total) -
						  parseFloat(this.form.discountValue)
						: parseFloat(this.currentOrder.total);
					this.totalLeftOver = this.couponValueDiscount
						? total - this.couponValueDiscount
						: null;
				}
			},
			deep: true,
		},
		currentOrder() {
			this.form.payments_method = [
				{
					type_payment: null,
					payment_label: null,
					value: 0,
					installment_data: {},
					bank_check_data: {},
				},
			];
			this.total = 0;
			this.couponValueDiscount = 0;
			this.couponStatus = null;
			this.hasPaymentForm = false;
			this.form.totalValue = this.currentOrder.total;
			this.form.observations = this.currentOrder.note;
			this.form.coupon_discount = this.currentOrder.coupon
				? this.currentOrder.coupon.name
				: null;
			this.form.discountValue = parseFloat(this.currentOrder.discount).toFixed(
				2
			);
			// this.$emit("selectedCancelForm", false);
			if (this.currentOrder.payments) this.fillDataPayment();
			if (this.chargeBackForm) this.$emit("cancelChargeBack", false);
			this.cancelChargeBackForm();
		},
		"form.taxValue"(value) {
			this.form.totalValue = (
				parseFloat(this.currentOrder.total) +
				parseFloat(value) -
				parseFloat(this.form.discountValue)
			).toFixed(2);
		},
		couponValueDiscount(value) {
			this.form.totalValue = (
				parseFloat(this.currentOrder.total) -
				parseFloat(value) +
				parseFloat(this.form.taxValue)
			).toFixed(2);
		},
	},

	computed: {
		canConfirm() {
			return (
				this.form.payments_method.filter((pay) => pay.type_payment == 3)
					.length > 0 &&
				this.totalLeftOver &&
				this.totalLeftOver > 0
			);
		},
		hasSelectedPayment() {
			return this.form.payments_method.find((payment) => payment.type_payment);
		},
		lastFormSelected() {
			const lastIndex = this.form.payments_method.length - 1;
			return (
				this.form.payments_method[lastIndex].type_payment > 0 &&
				this.totalLeftOver > 0
			);
		},
		alreadyPaid() {
			return ["concluded", "returned", "partially_returned"].includes(
				this.currentOrder.status
			);
		},
		chargebackMoney() {
			return this.form.chargeBack.type == "money";
		},
		chargebackProducts() {
			return this.currentOrder.products;
		},
		invalidCharge() {
			return (
				!this.form.payments_method.find((pay) => pay.type_payment === 3) &&
				Math.abs(this.totalLeftOver) > 0
			);
		},
	},

	async mounted() {
		this.totalLeftOver = this.currentOrder.total;
		this.form.totalValue = this.currentOrder.total;
		this.form.observations = this.currentOrder.note;
		this.form.coupon_discount = this.currentOrder.coupon
			? this.currentOrder.coupon.name
			: null;
		this.form.discountValue = parseFloat(this.currentOrder.discount).toFixed(2);
		this.$emit("selectedPaymentForm", false);

		if (this.currentOrder.payments) {
			this.fillDataPayment();
			if (
				this.currentOrder.payments.find(
					(pay) =>
						[1, 2].includes(pay.payment_method_id) &&
						moment(pay.created_at).isBefore(new Date())
				)
			)
				this.isCardChargeBack = true;
		}

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
		} catch (error) {
			console.error(error);
		}
	},

	beforeDestroy() {
		this.$emit("selectedPaymentForm", false);
	},

	methods: {
		formatReal,
		async fillDataPayment() {
			this.loadingFillData = true;
			const paysInfo = this.currentOrder.payments;

			if (paysInfo.some((pay) => pay.payment_method_id == 1)) {
				await this.getInstallmentsData();
			}

			if (paysInfo.some((pay) => pay.payment_method_id == 5)) {
				await this.getBankData();
			}

			let taxValue = 0;

			paysInfo.forEach((pay, index) => {
				this.form.payments_method[index] = {
					type_payment: pay.payment_method_id,
					payment_label: this.paymentOptions[pay.payment_method_id - 1],
					value: pay.value,
					installment_data: pay.tax_installment_id
						? this.installments_data[pay.tax_installment_id - 1]
						: {},
					PIX: pay.pix_number ? pay.pix_number : null,
				};
				if (pay.payment_method_id === 5) {
					this.form.payments_method[index].bank_check_data = {
						bank_id: 1,
						check_name: pay.check_name,
						check_number: pay.check_number,
					};
				}
				if (pay.payment_method_id === 1) {
					const payment = this.form.payments_method[index];
					taxValue += parseFloat(
						(
							((payment.installment_data.interest_rates * pay.value) / 100) *
							payment.installment_data.installments
						).toFixed(2)
					);
				}
			});

			this.totalLeftOver = paysInfo.reduce(
				(acc, pay) => (acc += parseFloat(pay.charge)),
				0
			);

			this.form.taxValue = taxValue;
			this.hasPaymentForm = true;
			this.total = paysInfo.value;
			this.$emit("selectedPaymentForm", false);
			this.$emit("selectedCancelForm", true);
			this.loadingFillData = false;
			this.$forceUpdate();
		},

		async handlePrintOrder() {
			await this.showBudgetModalOrder(this.currentOrder.id);
		},

		calculateDiscount() {
			if (this.currentOrder.coupon) {
				return;
			} else {
			}
		},
		async submit() {
			if (this.invalidCharge) {
				return toastr.error(
					"Esta venda não possuí métodos de pagamento que permite troco"
				);
			}
			if (!this.isCashierOwner) {
				return toastr.error(
					`Somente o responsável do caixa (${this.cashierOwner.name}) pode efetuar pagamentos`
				);
			}
			const payMethods = this.form.payments_method;
			let payment_methods;
			try {
				payment_methods = payMethods.map((payment) => {
					if ([3, 5].includes(payment.type_payment)) {
						const paymentLabel =
							payment.type_payment == 3 ? "Dinheiro" : "Cheque";

						if (
							parseFloat(payment.value) <
							parseFloat(Math.abs(this.totalLeftOver))
						) {
							throw new Error(
								`O troco é maior que o valor informado em ${paymentLabel}`
							);
						}
					}

					return {
						id: payment.type_payment,
						value: payment.value,
						tax_installment_id: payment.installment_data.id
							? payment.installment_data.id
							: 1,
						pix_number: payment.PIX,
						check_number: payment.bank_check_data.check_number,
						check_name: payment.bank_check_data.check_name,
						bank_id: payment.bank_check_data.bank_id,
					};
				});
			} catch (error) {
				return toastr.error(error);
			}

			if (Math.abs(this.totalLeftOver) > 0) {
				const payment = payment_methods.find((payment) =>
					[3, 5].includes(payment.id)
				);

				if (payment) payment.charge = Math.abs(this.totalLeftOver);
			}

			payment_methods.forEach((payment) => {
				if (!payment.charge) payment.charge = 0;
			});

			const data = {
				type: "in",
				total_value: this.currentOrder.total,
				note: this.form.observations,
				order_id: this.currentOrder.id,
				user_id: this.currentOrder.user_id,
				coupon_id: this.currentOrder.coupon_id,
				cashier_info_id: this.currentOrder.cashierInfo.id,
				payment_methods,
			};

			if (!this.validatePaymentData()) return;
			try {
				await axios.post("/cashier", data);
				toastr.success("Pagamento registrado com sucesso!");
				this.$emit("successfulPayment", 0);
			} catch (error) {
				console.error("Error in payment", error);
				toastr.error("Erro ao registrar seu pagamento");
			}
		},

		removePayment(index) {
			if (this.form.payments_method.length > 1) {
				this.form.payments_method.splice(index, 1);
			} else {
				this.isRemoveLastPay = true;
				this.form.payments_method[0].type_payment = null;
				this.form.payments_method[0].value = parseFloat(
					this.currentOrder.total
				);
				this.form.taxValue = 0;
				this.form.totalValue = parseFloat(this.currentOrder.total);
				this.hasPaymentForm = false;
				this.totalLeftOver = null;
				this.$emit("selectedPaymentForm", false);
			}
		},

		addPaymentMethod(event, paymentMethod) {
			// TODO: Revisar
			if (event == 1) {
				this.getInstallmentsData();
			}

			if (event == 5) {
				this.getBankData();
			}

			paymentMethod.payment_label = this.paymentOptions[event - 1];
			console.log(this.totalLeftOver);
			paymentMethod.value =
				this.totalLeftOver === 0 ? this.currentOrder.total : this.totalLeftOver;
			this.hasPaymentForm = true;

			this.$emit("selectedPaymentForm", event && event !== 0);
		},

		addPayment() {
			this.form.payments_method.push({
				type_payment: null,
				payment_label: null,
				value: null,
				installment_data: {},
				bank_check_data: {},
			});
		},

		installmentSelection(index_installment_data, payment) {
			payment.installment_data = this.installments_data[index_installment_data];
			this.form.taxValue = (
				(payment.installment_data.interest_rates * this.form.totalValue) /
				100
			).toFixed(2);
			this.$forceUpdate();
		},

		discountCouponValidator: _.debounce(async function (event) {
			if (event.target.value)
				await axios
					.get("/coupons", {
						params: { search: event.target.value, paginate: false },
					})
					.then(({ data }) => {
						if (data) {
							if (data.quantity > 0) {
								this.couponStatus = "Válido";
								this.couponValueDiscount = data.value;
								this.totalCouponValue = parseFloat(data.value);

								this.applyDiscountToCurrentPayment(data.value);
							} else {
								this.couponStatus = "Inválido";
								this.couponValueDiscount = 0;
							}
						} else {
							this.couponStatus = "Não encontrado";
							const lastPay =
								this.form.payments_method[this.form.payments_method.length - 1];
							lastPay.value += parseFloat(this.couponValueDiscount);
							this.couponValueDiscount = 0;
						}
					});
			else {
				this.couponStatus = null;
				this.couponValueDiscount = 0;
			}
		}, 500),

		applyDiscountToCurrentPayment(value) {
			const pays = this.form.payments_method;
			pays[pays.length - 1].value -= parseFloat(value);
			// this.removePayment(pays.length);
			this.$forceUpdate();
		},

		async getInstallmentsData() {
			this.installments_data = await axios
				.get("/tax-installments")
				.then(({ data }) => {
					return data.map((install) => {
						const text =
							install.interest_rates > 0
								? install.interest_rates + "% de juros"
								: "Sem juros";

						return {
							text: `${install.quantity}x - ${text}`,
							id: install.id,
							installments: install.quantity,
							interest_rates: install.interest_rates,
						};
					});
				})
				.catch((error) =>
					console.error("Error fetching list installments", error)
				);
		},

		async getBankData() {
			this.banks = await axios
				.get("/banks")
				.then(({ data }) => {
					return data.map((bank) => ({ text: bank.name, value: bank.id }));
				})
				.catch((error) => console.error("Error fetching list banks", error));
		},

		handleBrandSelected(brand, index, payMethod) {
			this.selectedBrand[index] = brand;
			payMethod.brand_card = brand;
			this.$forceUpdate();
		},

		getCalculedInstallment(payMethod) {
			const installData = payMethod.installment_data;
			const value = payMethod.value;
			const totalWithTaxs =
				value +
				(value * installData.interest_rates * installData.installments) / 100;
			return totalWithTaxs / installData.installments;
		},

		handleChargebackType(type) {
			this.formChargeback.type_chargeback = type;
			if (type === "cash") {
				$("#btn-money").addClass("btn-chargeback-selected");
				$("#btn-coupon").removeClass("btn-chargeback-selected");
			} else {
				$("#btn-money").removeClass("btn-chargeback-selected");
				$("#btn-coupon").addClass("btn-chargeback-selected");
			}
			this.$forceUpdate();
		},
		confirm() {
			this.$emit("confirm");
			/* this.note = ""; */
		},
		cancel() {
			this.$emit("cancel");
		},
		handleCancelOrder() {
			if (!this.isCashierOwner) {
				return toastr.error(
					`Somente o responsável do caixa (${this.cashierOwner.name}) pode efetuar cancelamentos`
				);
			}
			this.$emit("cancelOrder", true);
		},

		cancelChargeBackForm() {
			this.$emit("cancelChargeBack", false);
			this.$forceUpdate();
		},
		handleChargebackProduct(product) {
			if (product.canceled_at) return;
			const indexProduct = this.formChargeback.products.findIndex(
				(prod) => prod.id === product.id
			);

			if (indexProduct === -1) {
				this.formChargeback.products.push(product);
				product.isChecked = true;
			} else {
				this.formChargeback.products.splice(indexProduct, 1);
				product.isChecked = false;
			}
			this.$forceUpdate();
		},
		handleChargebackProductMaintenance(product) {
			if (product.canceled_at) return;
			const indexProduct = this.formChargeback.by_products.findIndex(
				(prod) => prod.id === product.id
			);
			if (indexProduct) {
				this.formChargeback.by_products.push(product);
				product.isChecked = true;
			} else {
				this.formChargeback.by_products.splice(indexProduct, 1);
				product.isChecked = false;
			}
			this.$forceUpdate();
		},
		handleTypeChargeback(type) {
			this.isTotalChargeBack = type === "total";
			this.isParcialChargeBack = type === "parcial";
			this.formChargeback.type = type;
		},
		async submitChargeback() {
			const orderPaid = this.currentOrder.status === "concluded";
			if (!this.formChargeback.type_chargeback && orderPaid)
				return toastr.error(
					`Você deve selecionar a forma de estorno Dinheiro ou Cupom`
				);
			if (!this.formChargeback.type && orderPaid)
				return toastr.error(
					`Você deve selecionar o tipo de estorno Total ou Parcial`
				);

			if (this.isTotalChargeBack) {
				this.formChargeback.products = [];
				this.formChargeback.by_products = [];

				this.currentOrder.products.forEach((prod) => {
					if (prod.product.id !== 1 && !prod.canceled_at)
						this.formChargeback.products.push(prod);
					else
						prod.by_products.forEach((prod) => {
							if (!prod.canceled_at) this.formChargeback.by_products.push(prod);
						});
				});
			}

			if (this.isCardChargeBack) this.formChargeback.type = "none";

			await axios
				.post(`/orders/${this.currentOrder.id}/refund`, this.formChargeback)
				.then((resp) => {
					toastr.success("Estorno realizado com sucesso!");
					this.$emit("cancelChargeBack", false);
					this.$emit("updateOrders");
					this.$forceUpdate();
					this.$emit("showCancelModal", resp.data);
				})
				.catch((e) => {
					console.error("DeuRuim", e);
					toastr.error("Houve um problema em efetuar o estorno");
				});
			this.$emit("canceledOrder", true);
		},
		budgetPrint() {
			$("#budgetModal").modal("hide");
		},
		openBudgetModal() {
			$("#budgetModal").modal("show");
		},
		async showBudgetModalOrder(orderId) {
			const params = { paginate: false, type: "vue" };
			if (orderId) params.search = orderId;
			if (!orderId) params.clientId = this.client.id;

			const url = orderId ? "/orders" : "/orders/last";
			try {
				const { data } = await axios.get(url, { params });
				this.lastOrder = orderId ? data[0] : data;
				this.observation = this.lastOrder.note ? this.lastOrder.note : "";
				this.openBudgetModal();
			} catch (error) {
				console.error("Error", error);
				toastr.error("Error!");
			}
		},
		showProductsModal() {
			$("#listProducts").modal("show");
		},
		validatePaymentData() {
			const payMethods = this.form.payments_method;

			const paymentCardOptions = payMethods.filter((el) =>
				[1, 2].includes(el.type_payment)
			);
			const paymentMoney = payMethods.filter((el) => el.type_payment === 3);
			const paymentPix = payMethods.filter((el) => el.type_payment === 4);
			const paymentCheck = payMethods.filter((el) => el.type_payment === 5);

			// valida cartões de crédito/débito
			if (!_.isEmpty(paymentCardOptions)) {
				// valida se há parcelas selecionadas
				// const noParcelsSelected = paymentCardOptions
				// 	.filter(el => el.type_payment === 1)
				// 	.find(payment => !payment.installment_data.id);
				// if (noParcelsSelected) {
				// 	toastr.error(
				// 		"Selecione a quantidade de parcelas para a opção cartão de crédito"
				// 	);
				// 	return false;
				// }
				// valida se usuário selecionou a bandeira
				// const noBrandSelected = paymentCardOptions.find(
				// 	payment => !payment.brand_card
				// );
				// if (noBrandSelected) {
				// 	toastr.error(
				// 		"Selecione a(s) bandeira(s) para a opção(s) cartão(s) de crédito/débito"
				// 	);
				// 	return false;
				// }
				return true;
				// valida pagamentos em dinheiro
			} else if (!_.isEmpty(paymentMoney)) {
				const noValue = paymentMoney.find((el) => el.value < 0);
				if (noValue) {
					toastr.error(
						"O valor informado para pagamento em dinheiro é inválido!"
					);
					return false;
				}
				return true;
				// valida pagamentos PIX
			} else if (!_.isEmpty(paymentPix)) {
				const noValue = paymentPix.find((el) => el.value < 0);
				if (noValue) {
					toastr.error("O valor informado no PIX é iválido!");
					return false;
				}
				const emptyNumberPix = paymentPix.find((el) => !el.PIX);
				if (emptyNumberPix) {
					toastr.error("Por favor preencher um número do PIX válido");
					return false;
				}
				return true;
				// valida pagamentos em Cheque
			} else if (!_.isEmpty(paymentCheck)) {
				const emptyNumberCheck = paymentCheck.find(
					(el) => !el.bank_check_data.check_number
				);
				if (emptyNumberCheck) {
					toastr.error(
						"Por favor preencha o número na forma de pagamento Cheque"
					);
					return false;
				}
				const emptyNameCheck = paymentCheck.find(
					(el) => !el.bank_check_data.check_name
				);
				if (emptyNameCheck) {
					toastr.error(
						"Por favor preencha o nome na forma de pagamento Cheque"
					);
					return false;
				}
				const emptyBankCheck = paymentCheck.find(
					(el) => !el.bank_check_data.bank_name
				);
				if (emptyBankCheck) {
					toastr.error(
						"Por favor selecione o banco na forma de pagamento Cheque"
					);
					return false;
				}
				return true;
			}
			toastr.error("Por favor selecione uma forma de pagamento!");
			return false;
		},
		getTotal(product) {
			//	if (parseFloat(product.price) < parseFloat(product.addition)) {
			return (
				(parseFloat(product.price) +
					parseFloat(product.addition) -
					parseFloat(product.discount)) *
				product.amount
			);
			//}
			return product.price * product.amount;
		},
	},
};
</script>

<style lang="scss" scoped>
textarea {
	resize: none;
	min-height: 100px;
}
.sale-info {
	margin-top: 1rem;
	width: 100%;
	border-radius: 4px;
	background-color: #fbfcfc;
	border: 1px solid #e4e7ed;
	list-style: none;
	padding: 0 2rem;

	li {
		display: flex;
		justify-content: space-between;
		align-items: center;
		width: 100%;
		padding: 1rem 0;
		border-bottom: 1px solid #e4e7ed;
	}

	h2,
	p {
		margin: 0;
		display: inline;
		font-size: 1rem;
	}

	.input {
		padding: 1.125rem;
		border-radius: 4px;
	}
}

.payment-form-wrapper {
	max-height: 515px;
	overflow-y: auto;
	margin-bottom: 0px;

	.card {
		padding: 30px;
	}

	.fa-plus {
		font-size: 0.8rem;
	}

	.delete-button {
		background: #d63030;
		border-radius: 4px;
		width: 25px;
		height: 25px;
		padding: 0;

		.fa-trash-alt {
			font-size: 0.8rem;
			color: white;
		}
	}

	.money {
		height: calc(2.25rem + 2px);
		padding: 0.275rem 0.75rem;
		font-size: 1rem;
		font-weight: 400;
		line-height: 1.5;
		color: #495057;
		background-color: #fff;
		background-clip: padding-box;
		border: 1px solid #ced4da;
		border-radius: 0.25rem;
		box-shadow: inset 0 0 0 transparent;
		transition: border-color 0.15s;
		width: 100%;
	}

	.total-parcels {
		background: #f5f6f8;
		opacity: 0.7;
		border-radius: 4px;
		width: 100%;
		padding: 10px 0;
		text-align: center;
		margin-top: 20px;
	}

	.btn-cc {
		padding: 5px 3px;
		margin: 0 5px;
		opacity: 0.3;
		&.selected {
			opacity: 1;
		}
	}
}
.total-value {
	background: #fbfcfc;
	border: 1px solid #e4e7ed;
	box-sizing: border-box;
	border-radius: 4px;
	padding: 10px 30px;

	&.change {
		background: #fceafc;
	}

	&.missing {
		background: #fcdffc;
	}
}

.btn-chargeback {
	min-height: 75px;
	background: #fff;
	color: #4b545c;
	border: 1px dashed #c2cfe0;
	width: 100%;

	& + & {
		margin-left: 10px;
	}
}

.btn-primary:not(:disabled):not(.disabled).active,
.btn-primary:not(:disabled):not(.disabled):active,
.show > .btn-primary.dropdown-toggle,
.btn-chargeback-selected {
	color: #fff;
	background-color: #0983e8;
	border-color: #0983e8;
}

.pointer {
	cursor: pointer;
	user-select: none;
}

/* .is-invalid {
    border: 1px solid red !important;
} */
</style>
