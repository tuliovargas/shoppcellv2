<template>
	<div class="order-create">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<div class="row">
						<div class="col-md-9 position-relative">
							<input
								type="search"
								ref="searchClient"
								:disabled="isUpdateOrder"
								autocomplete="off"
								@keyup="getClients()"
								v-model="clientSearch"
								class="form-control"
								placeholder="Buscar cliente (Nome / CPF / CNPJ)"
								id="client"
								@keyup.down="onArrowDown"
								@keyup.up="onArrowUp"
								@keyup.enter="onEnter"
							/>
							<ul
								ref="scrollContainer"
								v-if="clientSearch && showListClient"
								class="list-group search-popup"
							>
								<li
									ref="options"
									v-if="clients.length === 0"
									class="list-group-item is-active"
								>
									<div
										class="flex justify-between items-center"
										@click="createClient()"
									>
										Usuário não existe, clique aqui para criá-lo
									</div>
								</li>
								<li
									v-for="(client, i) in clients"
									:key="client.id"
									:class="{ 'is-active': i === arrowCounter }"
									ref="options"
									class="list-group-item"
									@click="selectedClient(client)"
								>
									<div class="flex justify-between items-center">
										{{
											client.full_name +
											(client.cpf
												? " | " +
												  (client.cpf.length >= 18 ? "CNPJ: " : "CPF: ") +
												  client.cpf
												: "")
										}}
									</div>
								</li>
							</ul>
						</div>
						<div class="col-md-3 mt-3 mt-md-0 d-flex">
							<button
								@click="openCreateModal"
								title="Criar/Atualizar usuário"
								type="button"
								class="btn btn-info btn-block btn-flat btn-new-client"
								style="max-width: 62px"
							>
								<i class="fa fa-plus"></i>
							</button>
							<button
								v-if="isUpdateOrder"
								@click="printReceipts"
								:disabled="isNotPrintableOrder"
								title="Imprimir comprovante"
								type="button"
								class="btn btn-info btn-block btn-flat btn-new-client ml-3 mt-0"
								style="max-width: 62px"
							>
								<i class="fa fa-print"></i>
							</button>
							<button
								v-if="!consumidor"
								@click="consumidorFinal()"
								title="Consumidor Final"
								type="button"
								style="max-width: 62px"
								class="btn btn-warning btn-block btn-flat btn-pdv ml-3 mt-0"
							>
								<i class="fa fa-user"></i>
							</button>
						</div>
					</div>
					<div class="row" v-if="lastUpdated()">
						<div class="col-12">
							<a
								@click.prevent="openCreateModal"
								href="#"
								v-html="lastUpdated()"
								class="text-sm mb-0"
								style="color: tomato"
							></a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card-deck mb-3 flex-nowrap" style="height: calc(80vh - 20px)">
			<div class="cw-70 px-2">
				<div class="card mx-0 h-100">
					<div class="card-body p-4">
						<div class="form-group">
							<div class="position-relative">
								<div class="input-group">
									<input
										ref="searchProduct"
										:disabled="isNotEditableOrder"
										type="search"
										autocomplete="off"
										placeholder="Buscar produtos/serviços"
										v-model="productSearch"
										@keyup="getProducts"
										class="form-control"
										@keyup.down="onArrowDownProd"
										@keyup.up="onArrowUpProd"
										@keyup.enter="onEnterProd"
										id="product"
									/>
								</div>
								<ul
									v-if="
										productSearch &&
										products &&
										products.length &&
										showListProduct
									"
									ref="scrollContainerProd"
									class="list-group w-100 search-popup"
								>
									<li
										v-for="(product, i) in products"
										:key="product.id"
										@click="addCart(product)"
										class="list-group-item"
										ref="optionsProd"
										:class="{
											'is-active': i === arrowCounterProd,
										}"
									>
										<div class="flex justify-between items-center">
											<span class="font-weight-bold">{{ product.name }} </span>
											<span
												><span class="font-weight-bold">| Preço:</span>
												{{ product.price | currency }}</span
											>
											<span v-if="product.id !== 1" class="font-weight-bold"
												>| Marca:</span
											>
											{{
												product.brand && product.brand.name && product.id !== 1
													? product.brand.name
													: ""
											}}
											<span
												v-if="product.id != 1"
												:class="{
													'text-danger': product.quantity_in_stock <= 0,
												}"
											>
												<span class="font-weight-bold"> | Estoque:</span>
												{{ product.quantity_in_stock }}
											</span>
										</div>
									</li>
								</ul>
							</div>
						</div>

						<div class="table-responsive h-100" style="overflow-y: hidden">
							<table class="table table-budget h-100 table-orders">
								<thead>
									<tr style="cursor: default">
										<th class="th__prod-name" scope="col">Produtos</th>
										<th class="th__prod-amount" scope="col">Quantidade</th>
										<th class="th__prod-price" scope="col">Preço</th>
										<th class="th__prod-addition" scope="col">Acréscimo</th>
										<th class="th__prod-discount" scope="col">Desconto</th>
										<th class="th__prod-subtotal" scope="col">Subtotal</th>
									</tr>
								</thead>
								<tbody>
									<tr
										style="cursor: default"
										v-for="(item, index) in cart"
										:key="index"
									>
										<td colspan="0" v-if="item.id === 1" class="pl-0 py-0">
											<div class="order-listed warranty-form">
												<div
													class="d-flex item-summary d-flex justify-content-between"
												>
													<label class="item-summary__name mr-2">{{
														item.productName
													}}</label>
													<div
														class="d-flex flex-grow-1 justify-content-between"
													>
														<span class="item-summary__price">
															{{ item.price | currency }}</span
														>
														<div class="item-summary__addition">
															<span class="d-flex justify-content-center">
																<span v-if="!item.editAddition">
																	{{ item.addition | currency }}
																</span>
																<span class="d-flex align-items-center">
																	<money
																		v-if="item.editAddition"
																		class="money"
																		:class="{
																			'is-invalid': false,
																		}"
																		@keypress.enter.native="
																			applyAddition(cart, index)
																		"
																		v-model="item.draftAddition"
																		v-bind="money"
																	/>
																	<button
																		v-if="!item.editAddition"
																		:disabled="isNotEditableOrder"
																		@click.prevent="
																			editProductAddition(cart, index)
																		"
																		class="text-primary ml-2 btn py-0"
																	>
																		<i class="fas fa-pen"></i>
																	</button>
																	<button
																		v-else
																		@click.prevent="applyAddition(cart, index)"
																		class="text-primary ml-2 btn py-0"
																	>
																		<i class="fas fa-save"></i>
																	</button>
																</span>
															</span>
														</div>
														<div class="item-summary__discount">
															<span class="d-flex justify-content-center">
																<span v-if="!cart[index].editDiscount">{{
																	item.discount | currency
																}}</span>
																<span class="d-flex align-items-center">
																	<money
																		v-if="cart[index].editDiscount"
																		class="money"
																		:class="{
																			'is-invalid': errors.discount,
																		}"
																		@keypress.enter.native="
																			applyDiscount(cart, index)
																		"
																		v-model="cart[index].draftDiscount"
																		v-bind="money"
																	/>
																	<button
																		v-if="!cart[index].editDiscount"
																		:disabled="isNotEditableOrder"
																		@click.prevent="
																			editProductDiscount(cart, index)
																		"
																		class="text-primary ml-2 btn py-0"
																	>
																		<i class="fas fa-pen"></i>
																	</button>
																	<button
																		v-else
																		@click.prevent="applyDiscount(cart, index)"
																		class="text-primary ml-2 btn py-0"
																	>
																		<i class="fas fa-save"></i>
																	</button>
																</span>
															</span>
														</div>
														<span class="item-summary__subtotal">{{
															item.subtotal | currency
														}}</span>
													</div>
													<span>
														<button
															@click.prevent="removeProductCart(index)"
															:disabled="isNotEditableOrder"
															class="text-danger btn"
														>
															<i class="far fa-trash-alt"></i>
														</button>
													</span>
												</div>
												<!-- FORM GARANTIA -->
												<div class="form-group">
													<div class="row">
														<div class="col-md-4 mb-3">
															<div class="row">
																<div class="col-md-5">
																	<div class="input-group">
																		<div class="input-group-text d-flex">
																			<label class="mr-2 mb-0">
																				É garantia?
																			</label>
																			<input
																				type="checkbox"
																				:disabled="isNotEditableOrder"
																				@change="
																					handleGaranteeModal($event, index)
																				"
																				v-model="isWarrantyForm"
																				lass="form-control"
																			/>
																		</div>
																	</div>
																</div>
																<div class="col-md-7 d-flex align-items-center">
																	<div
																		v-if="garanteeOrderRef"
																		class="input-group d-flex"
																	>
																		<div class="input-group-prepend">
																			<label class="input-group-text mb-0"
																				>Venda ref:</label
																			>
																		</div>
																		<input
																			disabled
																			type="text"
																			:value="garanteeOrderRef"
																			class="form-control"
																			style="max-width: 50%"
																		/>
																	</div>
																</div>
															</div>
														</div>
														<div class="col-md-8 mb-3">
															<div class="row">
																<div class="col-md-6">
																	<div
																		v-if="item.garanteeBrand"
																		class="input-group"
																	>
																		<input
																			class="form-control"
																			type="text"
																			:disabled="true"
																			:value="item.garanteeBrand.name"
																			name="garanteeBrand"
																			id="garanteeBrand"
																		/>
																	</div>
																	<ValidateError
																		v-else
																		:index="index"
																		property=".brand_id"
																		class="input-group"
																		:error="orderError"
																	>
																		<SelectModelsAndBrands
																			:ref="`sel-brand-${index}`"
																			:key="`sel-brand-${index}`"
																			:items="brands"
																			placeholder="Pesquise uma marca"
																			:show-list="item.showBrandList"
																			:disable="isNotEditableOrder"
																			@select="
																				handleSelectedBrand(item, $event)
																			"
																			@input="getBrands(item, $event)"
																			@close="item.showBrandList = $event"
																			@validate="validateOrders()"
																		/>
																	</ValidateError>
																</div>
																<div class="col-md-6">
																	<div
																		v-if="item.garanteeBrandModel"
																		class="input-group"
																	>
																		<input
																			class="form-control"
																			type="text"
																			:disabled="true"
																			:value="item.garanteeBrandModel.name"
																			name="garanteeBrandModel"
																			id="garanteeBrandModel"
																		/>
																	</div>
																	<!-- <div
																	v-else-if="item.brand_id == 1"
																	class="input-group"
																>
																	<input
																		class="form-control"
																		type="text"
																		@input="item.brand_model = $event"
																		@change="orderValidation($event, index)"
																		name="garanteeBrandModel"
																		id="garanteeBrandModel"
																	/>
																</div> -->
																	<ValidateError
																		v-else
																		:index="index"
																		property=".brand_model"
																		class="input-group"
																		:error="orderError"
																	>
																		<SelectModelsAndBrands
																			:ref="`sel-model-${index}`"
																			:key="`sel-model-${index}`"
																			:items="brandsModels"
																			:disable="
																				!item.brand_id || isNotEditableOrder
																			"
																			:placeholder="
																				item.brand_id
																					? 'Pesquise ou crie um modelo'
																					: 'Selecione uma marca'
																			"
																			:show-list="item.showModelList"
																			:brand-id="item.brand_id"
																			:create-item="item.createNew"
																			@created="handleItemCreated(item)"
																			@select="
																				handleSelectedModel(item, $event)
																			"
																			@input="getBrandsModels(item, $event)"
																			@validate="validateOrders()"
																			@close="item.showModelList = $event"
																		/>
																	</ValidateError>
																</div>
															</div>
														</div>
														<div class="col-md-4">
															<div
																class="d-flex justify-content-center align-items-center input-group"
															>
																<flat-pickr
																	v-model="item.due_date"
																	:disabled="isNotEditableOrder"
																	class="form-control"
																	placeholder="Data de entrega"
																	:config="config"
																	@input="setDueDate(index, item)"
																>
																</flat-pickr>
																<div class="input-group-append">
																	<button
																		:disabled="isNotEditableOrder"
																		class="btn btn-default"
																		type="button"
																		title="Toggle"
																		data-toggle
																	>
																		<i
																			style="color: #0983e8"
																			class="fa fa-calendar"
																		>
																		</i>
																	</button>
																</div>
															</div>
														</div>
														<div class="col-md-4">
															<ValidateError
																:index="index"
																property=".maintenance_info.os_status"
																class="input-group"
																:error="orderError"
															>
																<select
																	v-model="
																		cart[index].maintenance_info.os_status
																	"
																	:disabled="isNotEditableOrder"
																	class="form-control"
																	@change="$forceUpdate()"
																>
																	<option
																		v-for="(status, index) in listOrderStatus(
																			item
																		)"
																		:value="status.value"
																		:key="index"
																		:selected="index === 0"
																	>
																		{{ status.name }}
																	</option>
																</select>
															</ValidateError>
														</div>
														<div class="col-md-4">
															<div class="input-group">
																<select
																	v-model="cart[index].maintenance_info.user_id"
																	class="form-control mr-2"
																	:disabled="isNotEditableOrder"
																>
																	<option :value="undefined" selected disabled>
																		Selecione o Técnico
																	</option>
																	<option
																		v-for="tech in technicians"
																		:value="tech.id"
																		:key="tech.id"
																	>
																		{{ tech.name }}
																	</option>
																</select>
															</div>
														</div>
														<div class="col-md-12 py-2 text-left">
															<label>Checklist</label>
															<div class="d-flex flex-wrap">
																<div
																	v-for="(item, i) in checklist"
																	class="mr-3 cursor-pointer-hover"
																	style="user-select: none"
																	@click="handleChecklist(i, index)"
																	:key="i"
																>
																	<input
																		type="checkbox"
																		:disabled="isNotEditableOrder"
																		v-model="cart[index].checklist[i].checked"
																		@input="handleChecklist(i, index, true)"
																	/>
																	{{ item.name }}
																</div>
															</div>
														</div>
														<div class="col-md-12">
															<ValidateError
																:index="index"
																property=".maintenance_info.issue"
																class="form-group text-left"
																:error="orderError"
															>
																<label>Problema</label>
																<textarea
																	name=".maintenance_info.issue"
																	:disabled="isNotEditableOrder"
																	class="form-control w-100"
																	maxlength="191"
																	rows="3"
																	placeholder="Descrição do problema..."
																	style="resize: none"
																	v-model="cart[index].maintenance_info.issue"
																	@change="orderValidation($event, index)"
																	@input="validateOrders()"
																></textarea>
																<p
																	class="text-danger text-sm"
																	v-if="
																		cart[index].maintenance_info.issue &&
																		cart[index].maintenance_info.issue.length ==
																			191
																	"
																>
																	Limite de caracteres atingido
																</p>
															</ValidateError>
														</div>
														<div
															v-if="
																(isUpdateOrder && showTechnicalReport) ||
																(item.maintenance_info &&
																	item.maintenance_info.os_status ===
																		'finished')
															"
															class="col-md-12"
														>
															<div class="form-group">
																<div class="d-flex justify-content-between">
																	<label>
																		<i
																			class="far fa-file-alt mr-1"
																			style="font-size: 16px; color: #0983e8"
																		></i>
																		Laudo técnico
																	</label>
																	<button
																		v-if="comments && comments.length"
																		class="btn btn-comments"
																		@click="showComments"
																	>
																		{{ `Ver comentários (${comments.length})` }}
																	</button>
																</div>
																<textarea
																	class="form-control w-100"
																	rows="3"
																	:disabled="isNotEditableOrder"
																	placeholder="Detalhes do laudo técnico..."
																	v-model="
																		cart[index].maintenance_info
																			.technical_report
																	"
																	style="resize: none"
																	maxlength="191"
																></textarea>
																<p
																	class="text-danger text-sm"
																	v-if="
																		cart[index].maintenance_info
																			.technical_report &&
																		cart[index].maintenance_info
																			.technical_report.length == 191
																	"
																>
																	Limite de caracteres atingido
																</p>
															</div>
														</div>
														<div class="divider"></div>
														<div class="col-md-12">
															<div class="form-group">
																<div class="position-relative">
																	<div class="input-group">
																		<input
																			ref="searchMaintenanceProduct"
																			:disabled="isNotEditableOrder"
																			type="search"
																			autocomplete="no"
																			placeholder="Buscar peças reposição e manutenção (Nome / Código)"
																			v-model="productGaranteeSearch"
																			@keyup="getProductsMaintenance"
																			class="form-control"
																			@keyup.down="onArrowDownProd"
																			@keyup.up="onArrowUpProd"
																			@keyup.enter="onEnterGaranteeProd"
																			id="product"
																		/>
																	</div>
																	<ul
																		v-if="
																			productGaranteeSearch &&
																			garanteeProducts &&
																			garanteeProducts.length &&
																			showListProductMaintenance
																		"
																		ref="scrollContainerProd"
																		class="list-group w-100 search-popup"
																		style="z-index: 100"
																	>
																		<li
																			v-for="(
																				product, i
																			) in productsCategoryMaintenace"
																			:key="product.id"
																			@click="
																				addMaintenanceProduct(product, index)
																			"
																			class="list-group-item"
																			ref="optionsProd"
																			:class="{
																				'is-active': i === arrowCounterProd,
																			}"
																		>
																			<div
																				class="flex justify-between items-center"
																			>
																				<span class="font-weight-bold"
																					>{{ product.name }}
																				</span>
																				<span
																					:class="{
																						'text-danger':
																							product.quantity_in_stock <= 0,
																					}"
																					v-if="product.type != 'sv'"
																					><span class="font-weight-bold"
																						>| Estoque:</span
																					>
																					{{ product.quantity_in_stock }}</span
																				>

																				<span class="font-weight-bold"
																					>| Marca:</span
																				>
																				{{
																					product.brand && product.brand.name
																						? product.brand.name
																						: ""
																				}}
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="col-md-12">
															<div class="table-responsive">
																<table class="table-budget">
																	<thead>
																		<tr style="cursor: default">
																			<th width="35%">Produtos</th>
																			<th>Preço</th>
																			<th>Acrescimo</th>
																			<th>Desconto</th>
																			<th>Subtotal</th>
																			<th width="5%"></th>
																		</tr>
																	</thead>
																	<tbody style="max-height: 150px">
																		<tr
																			style="cursor: default"
																			v-for="(garanteeProd, idx) in cart[index]
																				.by_products"
																			:key="idx"
																		>
																			<td width="30%">
																				{{
																					garanteeProd.productName ||
																					garanteeProd.product.name
																				}}
																			</td>
																			<td>
																				{{
																					garanteeProd.product
																						? garanteeProd.product.price
																						: garanteeProd.price | currency
																				}}
																			</td>
																			<td>
																				<span>
																					<span
																						v-if="!garanteeProd.editAddition"
																						>{{
																							garanteeProd.addition | currency
																						}}</span
																					>
																					<span>
																						<money
																							v-if="garanteeProd.editAddition"
																							class="money"
																							:class="{
																								'is-invalid': errors.discount,
																							}"
																							@keypress.enter.native="
																								applyAddition(
																									cart[index].by_products,
																									idx
																								)
																							"
																							v-model="
																								garanteeProd.draftAddition
																							"
																							v-bind="money"
																						></money>
																						<button
																							v-if="!garanteeProd.editAddition"
																							:disabled="isNotEditableOrder"
																							@click.prevent="
																								editProductAddition(
																									cart[index].by_products,
																									idx
																								)
																							"
																							class="text-primary ml-2 btn py-0"
																						>
																							<i class="fas fa-pen"></i>
																						</button>
																						<button
																							v-else
																							@click.prevent="
																								applyAddition(
																									cart[index].by_products,
																									idx
																								)
																							"
																							class="text-primary ml-2 btn py-0"
																						>
																							<i class="fas fa-save"></i>
																						</button>
																					</span>
																				</span>
																			</td>
																			<td>
																				<span>
																					<span
																						v-if="!garanteeProd.editDiscount"
																						>{{
																							garanteeProd.discount | currency
																						}}</span
																					>
																					<span>
																						<money
																							v-if="garanteeProd.editDiscount"
																							class="money"
																							:class="{
																								'is-invalid': errors.discount,
																							}"
																							@keypress.enter.native="
																								applyDiscount(
																									cart[index].by_products,
																									idx
																								)
																							"
																							v-model="
																								garanteeProd.draftDiscount
																							"
																							v-bind="money"
																						></money>
																						<button
																							:disabled="isNotEditableOrder"
																							v-if="!garanteeProd.editDiscount"
																							@click.prevent="
																								editProductDiscount(
																									cart[index].by_products,
																									idx
																								)
																							"
																							class="text-primary ml-2 btn py-0"
																						>
																							<i class="fas fa-pen"></i>
																						</button>
																						<button
																							v-else
																							@click.prevent="
																								applyDiscount(
																									cart[index].by_products,
																									idx
																								)
																							"
																							class="text-primary ml-2 btn py-0"
																						>
																							<i class="fas fa-save"></i>
																						</button>
																					</span>
																				</span>
																			</td>
																			<td>
																				{{ garanteeProd.subtotal | currency }}
																			</td>
																			<td width="5%">
																				<button
																					:disabled="isNotEditableOrder"
																					@click.prevent="
																						removeMaintenanceProduct(
																							cart[index].by_products,
																							idx
																						)
																					"
																					class="text-danger btn py-0"
																				>
																					<i class="far fa-trash-alt"></i>
																				</button>
																			</td>
																		</tr>
																	</tbody>
																</table>
															</div>
														</div>
													</div>
												</div>
											</div>
										</td>
										<td colspan="0" v-else class="pl-0 py-0">
											<div class="order-listed product-only">
												<div
													class="d-flex item-summary d-flex justify-content-between"
												>
													<label class="item-summary__name">{{
														item.productName
													}}</label>
													<div class="item-summary__amount">
														<span class="d-flex">
															<button
																:disabled="isNotEditableOrder"
																@click.prevent="removeAmountProductCart(index)"
																class="text-primary mr-2 btn py-0"
															>
																<i class="fas fa-minus-circle"></i>
															</button>
															{{ item.amount }}
															<button
																:disabled="isNotEditableOrder"
																@click.prevent="addAmountProductCart(index)"
																class="text-primary ml-2 btn py-0"
															>
																<i class="fas fa-plus-circle"></i></button
														></span>
													</div>
													<span class="item-summary__price">
														{{ item.price | currency }}</span
													>
													<div class="item-summary__addition">
														<span class="d-flex justify-content-center">
															<span v-if="!cart[index].editAddition">{{
																item.addition | currency
															}}</span>
															<span class="d-flex">
																<money
																	v-if="cart[index].editAddition"
																	class="money"
																	:class="{
																		'is-invalid': errors.discount,
																	}"
																	@keypress.enter.native="
																		applyAddition(cart, index)
																	"
																	v-model="cart[index].draftAddition"
																	v-bind="money"
																></money>
																<button
																	v-if="!cart[index].editAddition"
																	:disabled="isNotEditableOrder"
																	@click.prevent="
																		editProductAddition(cart, index)
																	"
																	class="text-primary ml-2 btn py-0"
																>
																	<i class="fas fa-pen"></i>
																</button>
																<button
																	v-else
																	@click.prevent="applyAddition(cart, index)"
																	class="text-primary ml-2 btn py-0"
																>
																	<i class="fas fa-save"></i>
																</button>
															</span>
														</span>
													</div>
													<div class="item-summary__discount">
														<span class="d-flex justify-content-center">
															<span v-if="!cart[index].editDiscount">{{
																item.discount | currency
															}}</span>
															<span class="d-flex">
																<money
																	v-if="cart[index].editDiscount"
																	class="money"
																	:class="{
																		'is-invalid': errors.discount,
																	}"
																	@keypress.enter.native="
																		applyDiscount(cart, index)
																	"
																	v-model="cart[index].draftDiscount"
																	v-bind="money"
																></money>
																<button
																	v-if="!cart[index].editDiscount"
																	:disabled="isNotEditableOrder"
																	@click.prevent="
																		editProductDiscount(cart, index)
																	"
																	class="text-primary ml-2 btn py-0"
																>
																	<i class="fas fa-pen"></i>
																</button>
																<button
																	v-else
																	@click.prevent="applyDiscount(cart, index)"
																	class="text-primary ml-2 btn py-0"
																>
																	<i class="fas fa-save"></i>
																</button>
															</span>
														</span>
													</div>
													<span class="item-summary__subtotal">
														{{ item.subtotal | currency }}
													</span>
													<span>
														<button
															:disabled="isNotEditableOrder"
															@click.prevent="removeProductCart(index)"
															class="text-danger btn"
														>
															<i class="far fa-trash-alt"></i>
														</button>
													</span>
												</div>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="cw-30 px-2">
				<div class="card observations-card mx-0">
					<div class="form-group">
						<label for="observacao">OBSERVAÇÃO</label>
						<textarea
							:disabled="isNotEditableOrder"
							class="form-control mb-2"
							rows="3"
							id="observacao"
							name="observacao"
							v-model="note"
							placeholder="Observação"
						>
						</textarea>
						<ValidateError
							:index="0"
							property=".coupon"
							class="input-group"
							:error="errorCoupon"
						>
							<label for="Cupom de desconto">CUPOM DE DESCONTO</label>
							<input
								:disabled="isNotEditableOrder"
								v-model="searchCoupon"
								class="form-control"
								type="text"
								name="coupon-discount"
								id=""
								@input="getCoupons"
							/>
						</ValidateError>
						<div
							class="mt-2"
							v-if="
								cart[0] &&
								cart[0].maintenance_info &&
								cart[0].maintenance_info.os_status ===
									'no_maintenance_delivered'
							"
						>
							<label for="observacao">ENTREGUE NO DIA</label>
							<p>
								{{
									moment(originalServiceOrder.maintenance.due_date).format(
										"DD/MM/YYYY"
									) +
									" às " +
									moment(originalServiceOrder.maintenance.due_date).format(
										"hh:mm"
									)
								}}
							</p>
						</div>
						<div class="total-card mt-3">
							<div
								v-if="discount && discount > 0"
								class="d-flex justify-content-between"
							>
								<span>Desconto</span>
								<span>{{ discount | currency }}</span>
							</div>
							<div v-if="coupon" class="d-flex justify-content-between">
								<span>Valor Cupom</span>
								<span
									><b>{{ coupon.value | currency }}</b></span
								>
							</div>
							<div
								v-if="discount && discount > 0"
								class="d-flex justify-content-between"
							>
								<span>Subtotal</span>
								<span class="value"
									><b>{{ subtotal | currency }}</b></span
								>
							</div>
							<div class="d-flex justify-content-between">
								<span>Total</span>
								<span class="value"
									><b>{{ total | currency }}</b></span
								>
							</div>
						</div>
					</div>
				</div>
				<div class="row mt-4">
					<div class="col-md-5 mb-3 mb-md-0">
						<button
							type="button"
							v-if="
								(this.consumidor || this.clientSearch) &&
								Object.keys(this.errors).length === 0
							"
							@click="clearAllOrders"
							class="btn btn-block btn-danger btn-lg"
						>
							Limpar
						</button>
					</div>

					<div class="col-md-7 mb-3 mb-md-0">
						<button
							type="button"
							v-if="
								cart.length > 0 &&
								Object.keys(this.errors).length === 0 &&
								!isUpdateOrder
							"
							@click="openSellerModal"
							class="btn btn-block btn-primary btn-lg"
						>
							Fechar pedido
						</button>
						<button
							type="button"
							v-if="isUpdateOrder && !isOrderCanceled"
							:disabled="isNotEditableOrder"
							@click="openSellerModal"
							class="btn btn-block btn-lg"
							:class="isBudgetCanceled ? 'btn-primary' : 'btn-success'"
						>
							{{
								isNotEditableOrder
									? "Serviço finalizado"
									: isBudgetCanceled
									? "Cancelar OS"
									: "Atualizar Ordem"
							}}
						</button>
						<button
							type="button"
							v-if="
								isUpdateOrder &&
								isOrderCanceled &&
								cart[0] &&
								cart[0].maintenance_info &&
								cart[0].maintenance_info.os_status !==
									'no_maintenance_delivered'
							"
							@click="handleDeliverNoMaintenance"
							class="btn btn-block btn-lg"
							:class="isBudgetCanceled ? 'btn-primary' : 'btn-success'"
						>
							Entregue sem conserto
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- botões -->
		<div class="row">
			<div class="col-md-8">
				<div class="row">
					<div class="col-md-3 mb-5 mb-md-0">
						<button
							type="button"
							class="btn btn-block btn-outline-secondary btn-lg"
							:disabled="isUpdateOrder"
							@click="openBudgetOrBudgetListModal"
						>
							Orçamento/Vendas
						</button>
					</div>
					<div class="col-md-3 mb-5 mb-md-0">
						<button
							@click="openBudgetListMaintenances"
							type="button"
							class="btn btn-block btn-outline-secondary btn-lg"
						>
							Ordem de serviço
						</button>
					</div>
					<div class="col-md-3 mb-3 mb-md-0">
						<button
							type="button"
							class="btn btn-block btn-outline-secondary btn-lg"
							@click="generateReceipts()"
						>
							Pedido
						</button>
					</div>
				</div>
			</div>
		</div>

		<!--Modal de garantia-->
		<ModalGarantee
			@confirmedGarantee="fillGaranteeData"
			@cancelGarantee="handleCancelGarantee"
			:selected-client="client"
			ref="modalGarantee"
		/>
		<!--Modal Criar Usuário-->
		<div class="modal" id="createUserModal" tabindex="-1" role="dialog">
			<form class="modal-dialog" @submit.prevent="submitCreateClient">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title font-weight-bold">
							{{ client && client.id ? "Atualizar cliente" : "Novo cliente" }}
						</h5>
						<button
							type="button"
							class="close"
							@click="clearModalClient"
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
									v-model="newClient.full_name"
									class="form-control my-3"
									placeholder="Nome Completo"
									autocomplete="no"
									ref="clientName"
									required
								/>
								<input
									v-model="newClient.cpf"
									:class="{ 'is-invalid': !cpfCnpjIsValid }"
									:disabled="client && client.id"
									class="form-control my-3"
									placeholder="CPF/CNPJ"
									v-mask="['###.###.###-##', '##.###.###/####-##']"
								/>
							</div>
						</div>
						<input
							v-model="newClient.rg"
							autocomplete="no"
							class="form-control my-3"
							placeholder="RG/IE"
							v-mask="[
								'##.###.###-##',
								'###.###.###.###.###.###.###.###.###.###',
							]"
						/>
						<div class="row my-3">
							<div class="col">
								<select
									v-model="newClient.gender"
									name="gender"
									id="gender"
									class="form-control"
								>
									<option :value="undefined" disabled>Sexo</option>
									<option value="f">Feminino</option>
									<option value="m">Masculino</option>
								</select>
							</div>
							<div class="col">
								<input
									v-model="newClient.birthdate"
									autocomplete="no"
									class="form-control"
									type="date"
									placeholder="Data de nascimento"
								/>
							</div>
						</div>
						<input
							type="email"
							autocomplete="no"
							:class="{ 'is-invalid': !emailIsValid }"
							v-model="newClient.email"
							class="form-control my-3"
							placeholder="E-mail"
						/>
						<div class="row my-3">
							<div class="col">
								<input
									v-model="newClient.cellphone"
									class="form-control"
									placeholder="Celular"
									v-mask="'(##) #####-####'"
								/>
							</div>
							<div class="col">
								<input
									v-model="newClient.phone"
									class="form-control"
									placeholder="Telefone"
									v-mask="'(##) ####-####'"
								/>
							</div>
						</div>
						<div class="row my-3">
							<div class="col-6">
								<input
									v-model="newClient.postcode"
									@keyup="handleViacep"
									autocomplete="no"
									class="form-control"
									placeholder="CEP"
									v-mask="'#####-###'"
									ref="postcode"
								/>
							</div>
						</div>
						<div class="row my-3">
							<div class="col-8">
								<div class="position-relative">
									<div class="input-group">
										<input
											v-model="newClient.street"
											autocomplete="off"
											class="form-control"
											placeholder="Endereço"
											type="search"
											@keyup="getAddresses"
										/>
									</div>
									<ul
										v-if="
											newClient.street &&
											addresses &&
											addresses.length &&
											showListAddresses
										"
										ref="scrollContainerProd"
										class="list-group w-100 search-popup"
									>
										<li
											v-for="street in addresses"
											:key="street.id"
											@click="fillAddress(street)"
											class="list-group-item"
											ref="optionsProd"
										>
											<div class="flex justify-between items-center">
												<span class="font-weight-bold"
													>{{ street.logradouro }},</span
												>
												<span
													>{{ street.bairro }},
													{{
														street.postcode.replace(
															/^([\d]{5})-*([\d]{3})/,
															"$1-$2"
														)
													}}</span
												>
											</div>
										</li>
									</ul>
								</div>
							</div>

							<div class="col-4">
								<input
									v-model="newClient.number"
									class="form-control"
									placeholder="Numero"
								/>
							</div>
						</div>
						<div class="row my-3">
							<div class="col-5">
								<input
									v-model="newClient.neighborhood"
									autocomplete="no"
									class="form-control my-3"
									placeholder="Bairro"
								/>
							</div>
							<div class="col-7">
								<input
									v-model="newClient.complement"
									class="form-control my-3"
									placeholder="Complemento"
								/>
							</div>
						</div>
						<div class="row my-3">
							<div class="col-8">
								<input
									v-model="newClient.city"
									class="form-control"
									placeholder="Cidade"
								/>
							</div>
							<div class="col-4">
								<input
									v-model="newClient.state"
									class="form-control"
									placeholder="Estado"
								/>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-lg btn-primary">
							Confirmar
						</button>
					</div>
				</div>
			</form>
		</div>

		<!--Modal Cancelamento-->
		<div class="modal" id="cancelModal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-body py-4">
						<button
							type="button"
							class="close"
							data-dismiss="modal"
							aria-label="Close"
						>
							<span aria-hidden="true">&times;</span>
						</button>
						<div class="text-center">
							<p class="h5 mb-3">Deseja cancelar toda a venda?</p>
							<button @click="handleCancel" class="btn btn-lg btn-danger">
								Confirmar
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!--Modal Orçamento-->
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
		<!--Modal Vendedor-->
		<ModalUsers
			ref="modalUsers"
			@selectedUser="sellerSelected = $event"
			@confirmedUser="handleOrderFinish()"
			:users="sellers"
			:userSelected="sellerSelected"
		/>

		<ModalBugetList
			ref="modalBudgetList"
			:orders="budgets"
			:client="client"
			:user="user"
			:technicians="technicians"
			:is-maintenance="isMaintenanceBudgets"
			:cart="cart"
			@clearCart="cart = []"
			@selectedOrder="handleFillDataBudget"
			@getOrders="handleBudgetList"
		/>

		<ModalComments
			ref="modalComment"
			:comments="comments"
			:current-order="originalOrder"
			:user="user"
			@updateComments="getComments()"
		/>

		<ModalService
			id="modalService"
			:maintenances="lastMaintenance"
			:sellers="sellers"
			:user="user"
			:maintenance_text="maintenance_text"
			:cellphone="cellphone"
			:address="address"
			:email="email"
			v-show="false"
			ref="modalService"
		/>

		<!--Modal password admin-->
		<div
			ref="modalPassAdmin"
			class="modal"
			id="modalPassAdmin"
			tabindex="-1"
			role="dialog"
		>
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
								ref="inputPass"
								v-model="adminPass"
								type="password"
								name="adminPass"
								id="adminPass"
								class="w-100"
								readonly
								@focus="$refs.inputPass.removeAttribute('readonly')"
								autocomplete="off"
								@keypress.enter.prevent="validateAdminPassword(adminPass)"
							/>
						</div>
					</div>
					<div class="modal-footer">
						<button
							type="button"
							@click="validateAdminPassword(adminPass)"
							class="btn btn-lg btn-primary"
						>
							Confirmar
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</template>

<script>
import {
	isValidCNPJ,
	isValidCPF,
	isValidEmail,
} from "@brazilian-utils/brazilian-utils";
import axios from "axios";
import "flatpickr/dist/flatpickr.css";
import { Portuguese } from "flatpickr/dist/l10n/pt";
import moment from "moment";
import toastr from "toastr";
import "toastr/build/toastr.min.css";
import { Money } from "v-money";
import flatPickr from "vue-flatpickr-component";
import { mask } from "vue-the-mask";
import ValidateError from "./helper/ValidateError";
import { validateOrders } from "./helper/validateOrders";
import ModalBudget from "./ModalBudget";
import ModalBudgetList from "./ModalBugetList";
import ModalComments from "./ModalComments";
import ModalGarantee from "./ModalGarantee";
import ModalService from "./ModalService";
import ModalUsers from "./ModalUsers";
import SelectModelsAndBrands from "./SelectModelsAndBrands";

export default {
	name: "OrderCreate",

	components: {
		Money,
		ModalGarantee,
		ModalBudget,
		ModalBudgetList,
		ModalUsers,
		ModalComments,
		ModalService,
		ValidateError,
		flatPickr,
		SelectModelsAndBrands,
	},

	directives: { mask },

	props: {
		user: {
			required: true,
			type: Object,
		},
	},

	data() {
		return {
			moment: moment,
			clientSearch: "",
			consumidor: false,
			clients: [],
			client: {},
			showListClient: false,
			onEnterSearchClient: false,
			productSearch: "",
			products: [],
			showListProduct: false,
			showListAddresses: false,
			productGaranteeSearch: "",
			garanteeProducts: [],
			addresses: [],
			showListProductMaintenance: false,
			newClient: {
				street: "",
				state: "",
				number: "",
				city: "",
				neighborhood: "",
				full_name: "",
				is_active: 1,
				phone: this.easy_ddd,
				email: "",
				cpf: "",
				rg: "",
				gender: "",
				birthdate: "",
				cellphone: this.easy_ddd,
				postcode: this.easy_postcode,
			},
			garantee: [],
			showGaranteeModal: false,
			garanteeOrderRef: null,
			garanteeCartIndex: null,
			brands: [],
			brandsModels: [],
			cart: [],
			amount: 0,
			discount: 0,
			addition: 0,
			total: 0,
			subtotal: 0,
			observation: "",
			seller_id: null,
			money: {
				decimal: ",",
				thousands: ".",
				prefix: "",
				suffix: " ",
				precision: 2,
				masked: false,
			},
			arrowCounter: 0,
			arrowCounterProd: 0,
			errors: {},
			orders: [],
			budgets: [],
			isMaintenanceBudgets: false,
			originalOrder: null,
			originalServiceOrder: null,
			lastOrder: "",
			lastMaintenance: [],
			users: [],
			sellers: [],
			sellerSelected: {},
			searchCoupon: "",
			coupon: null,
			note: "",
			productsByFilter: [],
			clientDefault: {},
			isWarranty: null,
			isWarrantyForm: false,
			checklist: [],
			adminPass: null,
			adminPassValidate: {},
			technicians: [],
			orderStatus: [
				{
					value: "waiting_approval",
					name: "Aguardando aprovação cliente",
				},
				{ value: "approved", name: "Aprovado" },
				{ value: "finished", name: "Finalizado" },
			],
			approvedOrderStatus: [
				{
					value: "waiting_stock",
					name: "Aguardando peça do fornecedor",
				},
				{ value: "maintenance", name: "Em manutenção" },
				{ value: "no_maintenance", name: "Sem conserto" },
				{ value: "fixed", name: "Consertado" },
				{ value: "finished", name: "Finalizado" },
				{ value: "no_maintenance_delivered", name: "Sem conserto/Entregue" },
			],
			isUpdateOrder: false,
			isNotEditableOrder: false,
			isNotPrintableOrder: false,
			isOrderCanceled: false,
			isBudgetOrder: false,
			comments: [],
			selectedStatus: null,
			showCalendar: false,
			showTechnicalForm: false,
			maintenanceProdValid: false,
			orderError: null,
			errorCoupon: null,
			searchBrand: null,
			showSearchList: false,
			easy_ddd: "",
			easy_postcode: "",
			maintenance_text: "",
			cupom_text: "",
			warranty_text: "",
			orcamento_text: "",
			cellphone: "",
			address: "",
			email: "",
			config: {
				wrap: true, // set wrap to true only when using 'input-group'
				altFormat: "d - F - Y",
				altInput: true,
				dateFormat: "Y-m-d H:i:S",
				minDate: new Date(),
				locale: Portuguese,
			},
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

	created() {
		let self = this;
		window.addEventListener("click", function () {
			self.showListClient = false;
			self.showListProduct = false;
		});
	},

	async mounted() {
		await this.setClientDefault();
		await this.getSellers();
		this.clientDefault = Object.assign(this.clientDefault, this.clients[0]);
		this.getCheckLists();
		this.getTechnicians();

		const _this = this;

		$("#modalPassAdmin").on("hide.bs.modal", function () {
			_this.handleCloseModalAdminPass();
		});

		$("#createUserModal").on("show.bs.modal", function () {
			if (_this.client.full_name == undefined) {
				_this.$refs.postcode.dispatchEvent(new Event("keyup")); // Força a busca inicial pelo CEP
			}
		});

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
		} catch (error) {
			console.error(error);
		}
	},

	computed: {
		async cpfCnpjIsValid() {
			if (!this.newClient.cpf || (this.client && this.client.id)) return true;

			let cpf = this.newClient.cpf.replace(/[^\d]/g, "");

			let valid = false;

			if (cpf.length == 14 || cpf.length == 11) {
				valid = cpf.length == 14 ? isValidCNPJ(cpf) : isValidCPF(cpf);

				if (valid) {
					toastr.clear();
					await axios
						.get("/clients/validateCpfCnpj", {
							params: {
								cpfCnpj: cpf,
							},
						})
						.then(({ data }) => {
							if (!data) {
								toastr.error(
									"Já existe um cliente cadastrado com esse CPF/CNPJ"
								);
							}
						})
						.catch((error) => {
							toastr.error("Erro ao validar CPF/CNPJ", "Erro");
						});
				} else {
					toastr.error("Digite um CPF/CNPJ válido", "Erro");
				}
				return valid;
			}
			return false;
		},
		emailIsValid() {
			if (!this.newClient.email) return true;
			return isValidEmail(this.newClient.email);
		},
		haveClientSelected() {
			return !_.isEmpty(this.client);
		},
		productsCategoryMaintenace() {
			return this.garanteeProducts;
		},
		isBudgetCanceled() {
			const order =
				this.originalOrder && this.originalOrder
					? this.originalOrder
					: this.originalServiceOrder;
			return (
				this.isUpdateOrder &&
				order.maintenance &&
				order.maintenance.os_status === "no_maintenance"
			);
		},

		showTechnicalReport() {
			return (
				this.originalOrder &&
				this.originalOrder.maintenance &&
				this.originalOrder.maintenance.os_status &&
				["no_maintenance", "conclued", "finish"].includes(
					this.originalOrder.maintenance.os_status
				)
			);
		},
		hasBudgetFinishedStatus() {
			return;
		},
	},

	watch: {
		clientSearch(value) {
			if (_.isEmpty(value)) {
				this.client = {};
				this.newClient = {
					cellphone: this.easy_ddd,
					postcode: this.easy_postcode,
					phone: this.easy_ddd,
				};
			}
		},
		searchCoupon(value) {
			if (_.isEmpty(value)) {
				this.coupon = null;
				this.calculateCart();
			}
		},
	},

	methods: {
		lastUpdated() {
			if (this.client.id != undefined) {
				const up = moment(this.client.updated_at);
				const today = moment();
				const diff = today.diff(up, "days");
				const start = "Cliente atualizado pela última vez";
				if (diff > 30) {
					return (
						start +
						" há <b>" +
						diff +
						"</b> dias - CLIQUE AQUI E ATUALIZE OS DADOS"
					);
				}
			}
			return false;
		},
		printReceipts() {
			const { maintenance: isMaintenance } = this.originalServiceOrder
				? this.originalServiceOrder
				: this.originalOrder.products.find((el) => el.product_id === 1) || {};

			if (
				!isMaintenance ||
				(isMaintenance && isMaintenance.os_status === "finished")
			)
				this.showBudgetModalOrder(this.originalOrder.id);

			if (
				isMaintenance &&
				isMaintenance.id &&
				isMaintenance.os_status !== "finished"
			) {
				return this.showServicesModalOrder(isMaintenance.id);
			}
		},
		handleOrderFinish() {
			if (!this.isUpdateOrder) this.storeOrder(this.isBudgetOrder);
			else {
				this.updateBudgetOrder();
			}
		},
		listOrderStatus(order) {
			return order &&
				order.maintenance_info.os_status !== "waiting_approval" &&
				order.maintenance_info.os_status !== "approved"
				? this.approvedOrderStatus
				: this.orderStatus;
		},
		async generateReceipts(receipts, isBudget) {
			if (receipts.includes("products")) {
				await this.showBudgetModalOrder();
			}

			if (receipts.includes("services") && !isBudget) {
				await this.showServicesModalOrder();
			}
		},
		handleBudgetList(event) {
			const { type, dateIni, dateFim, status, search } = event;
			if (type === "budget")
				return this.getBudgetList(status, dateIni, dateFim, search, true);
			else if (type === "maintenance")
				return this.getMaintenances(status, dateIni, dateFim, search, true);
		},
		handleChecklist(index, indexCart, isInput) {
			if (!isInput) {
				if (!this.cart[indexCart].checklist[index].checked)
					this.cart[indexCart].checklist[index].checked = true;
				else
					this.cart[indexCart].checklist[index].checked =
						!this.cart[indexCart].checklist[index].checked;
			}

			this.$forceUpdate();
		},
		onArrowDown(ev) {
			ev.preventDefault();
			if (this.arrowCounter < this.clients.length - 1) {
				this.arrowCounter = this.arrowCounter + 1;
				this.fixScrolling();
			}
		},

		onArrowUp(ev) {
			ev.preventDefault();
			if (this.arrowCounter > 0) {
				this.arrowCounter = this.arrowCounter - 1;
				this.fixScrolling();
			}
		},

		onEnter() {
			this.showListClient = false;
			this.onEnterSearchClient = true;
			if (this.clients.length > 0) {
				this.client = this.clients[this.arrowCounter];
				this.clientSearch = this.client.full_name;
			} else {
				this.createClient();
			}
			this.arrowCounter = 0;
			this.$refs.searchProduct.focus();
		},

		fixScrolling() {
			const liH = this.$refs.options[this.arrowCounter].clientHeight;
			if (
				liH * this.arrowCounter >
				this.$refs.scrollContainer.offsetHeight - liH
			) {
				this.$refs.scrollContainer.scrollTop = liH * this.arrowCounter;
			} else {
				this.$refs.scrollContainer.scrollTop = 0;
			}
		},

		onArrowDownProd(ev) {
			ev.preventDefault();
			if (this.arrowCounterProd < this.products.length - 1) {
				this.arrowCounterProd = this.arrowCounterProd + 1;
				this.fixScrollingProd();
			}
		},

		onArrowUpProd(ev) {
			ev.preventDefault();
			if (this.arrowCounterProd > 0) {
				this.arrowCounterProd = this.arrowCounterProd - 1;
				this.fixScrollingProd();
			}
		},

		onEnterGaranteeProd() {
			const product = this.garanteeProducts[this.arrowCounterProd];
			this.addCart(product);
			this.productSearch = "";
			this.arrowCounterProd = 0;
			this.showListProductMaintenance = false;
		},

		async onEnterProd() {
			const product = this.products[this.arrowCounterProd];
			if (!product) return;

			const { ordersError, hasErrors } = validateOrders(this.cart);
			this.orderError = ordersError;

			if (hasErrors && this.cart.length > 0 && product.id === 1) {
				return toastr.error(
					"Por favor confira os dados preenchidos nas orders"
				);
			}

			this.$forceUpdate();
			this.addCart(product);
			// focus no campo pesquise uma marca
			setTimeout(() => {
				if (product.id === 1) {
					const key = `sel-brand-${this.cart.length - 1}`;
					this.$refs[key][0].focus();
				}
			}, 150);

			this.productSearch = "";
			this.arrowCounterProd = 0;
			this.showListProduct = false;
		},

		fixScrollingProd() {
			const liH = this.$refs.optionsProd[this.arrowCounterProd].clientHeight;
			if (
				liH * this.arrowCounterProd >
				this.$refs.scrollContainerProd.offsetHeight - liH
			) {
				this.$refs.scrollContainerProd.scrollTop = liH * this.arrowCounterProd;
			} else {
				this.$refs.scrollContainerProd.scrollTop = 0;
			}
		},

		getClients: _.debounce(async function (clientId) {
			if (this.onEnterSearchClient) return (this.onEnterSearchClient = false);
			this.showListClient = true;
			addEventListener("keydown", this.closePopup);
			if (_.isEmpty(this.clientSearch) && !clientId) return;

			const config = {
				params: {
					search: this.clientSearch,
					paginate: false,
					type: "vue",
					clientId: clientId ? clientId : null,
				},
			};

			if (clientId) config.params.clientId = clientId;

			let url = "/clients";

			try {
				let response = await axios.get(url, config);
				this.clients = response.data;
				if (this.clients.length == 0) {
					this.client = {
						id: undefined,
					};
					this.newClient = {};
					this.newClient.full_name = this.clientSearch;
				}
			} catch (error) {
				console.error(error);
			}
		}, 400),

		async getTechnicians() {
			const config = {
				params: {
					paginate: true,
					type: "vue",
					onlyTechnicians: true,
					withAdmin: true,
				},
			};

			let url = "/users";

			try {
				let response = await axios.get(url, config);
				this.technicians = response.data;
			} catch (error) {
				console.error(error);
			}
		},

		getCoupons: _.debounce(async function () {
			const config = {
				params: {
					search: this.searchCoupon,
					paginate: false,
				},
			};

			let url = "/coupons";

			try {
				if (_.isEmpty(this.searchCoupon)) return;
				let { data } = await axios.get(url, config);
				if (data) {
					this.coupon = data;
					this.errorCoupon = null;
					this.calculateCart();
				} else {
					this.coupon = null;
					this.errorCoupon = [{ coupon: "Cupom inválido" }];
					this.calculateCart();
				}
			} catch (error) {
				console.error(error);
			}
		}, 500),

		async getUsers() {
			const config = {
				params: {
					paginate: false,
				},
			};

			let url = "/users";

			try {
				let response = await axios.get(url, config);
				this.users = response.data;
			} catch (error) {
				console.error(error);
			}
		},

		async getSellers() {
			const config = {
				params: {
					paginate: false,
					onlySellers: true,
					withAdmin: true,
				},
			};

			let url = "/users";

			try {
				let response = await axios.get(url, config);
				this.sellers = response.data;
			} catch (error) {
				console.error(error);
			}
		},

		closePopup(e) {
			if (e.key === "Escape" || e.keyCode === 27) {
				this.showListProductMaintenance = false;
				this.showListProduct = false;
				this.showListClient = false;
				this.showListAddresses = false;
			}
		},
		consumidorFinal() {
			this.clientSearch = "Consumidor Final";
			this.client.id = 1;
			this.consumidor = true;
			this.$nextTick(function () {
				this.$refs.searchProduct.focus();
			});

			this.calculateCart();
		},
		createClient() {
			if (!this.emailIsValid || !this.cpfCnpjIsValid) return;
			this.newClient.full_name = this.clientSearch;
			this.newClient.phone = this.easy_ddd;
			this.newClient.cellphone = this.easy_ddd;
			this.newClient.postcode = this.easy_postcode;

			this.$nextTick(function () {
				this.$refs.clientName.focus();
			});
			this.client.id = undefined;

			$("#createUserModal").modal("show");
		},

		getProductsMaintenance: _.debounce(async function () {
			this.showListProductMaintenance = true;
			addEventListener("keydown", this.closePopup);

			const CATEGORY_MAINTENANCE = 1;

			const config = {
				params: {
					search: this.productGaranteeSearch,
					paginate: false,
					category: CATEGORY_MAINTENANCE,
				},
			};

			let url = "/products";

			try {
				let response = await axios.get(url, config);
				this.garanteeProducts = response.data;
			} catch (error) {
				console.error(error);
			}
		}, 400),
		getProducts: _.debounce(async function () {
			this.showListProduct = true;
			addEventListener("keydown", this.closePopup);

			const config = {
				params: {
					search: this.productSearch,
					paginate: false,
					category: -1,
				},
			};

			let url = "/products";

			try {
				let response = await axios.get(url, config);
				this.products = response.data;
			} catch (error) {
				console.error(error);
			}
		}, 400),

		getAddresses: _.debounce(async function () {
			this.showListAddresses = true;
			addEventListener("keydown", this.closePopup);

			const config = {
				params: {
					search: this.newClient.street,
					paginate: false,
				},
			};

			let url = "/streets";

			try {
				let response = await axios.get(url, config);
				this.addresses = response.data;
			} catch (error) {
				console.error(error);
			}
		}, 400),

		selectedClient(client) {
			this.arrowCounter = 0;
			this.client = client;
			this.clientSearch = client.full_name;
			this.showListClient = false;
			removeEventListener("keydown", this.closePopup);
		},

		fillAddress(street) {
			this.newClient.postcode = street.postcode;
			this.newClient.neighborhood = street.bairro;
			this.newClient.city = street.cidade;
			this.newClient.state = street.uf;
			this.newClient.street = street.logradouro;
			this.showListAddresses = false;
			removeEventListener("keydown", this.closePopup);
		},

		selectedProduct(product) {
			this.productSearch = product.name;
			this.showListProduct = false;
			this.showListProductMaintenance = false;
			this.showListAddresses = false;
			removeEventListener("keydown", this.closePopup);
		},

		openCreateModal() {
			if (this.client && this.client.id !== undefined) {
				this.newClient = {};
				this.newClient.full_name = this.client.full_name;
				this.newClient.is_active = this.client.is_active;
				this.newClient.phone =
					this.client.phone != undefined && this.client.phone.length > 0
						? this.client.phone
						: this.easy_ddd;
				this.newClient.cellphone =
					this.client.cellphone != undefined && this.client.cellphone.length > 0
						? this.client.cellphone
						: this.easy_ddd;
				this.newClient.email = this.client.email;
				this.newClient.cpf = this.client.cpf;
				this.newClient.rg = this.client.rg;
				this.newClient.birthdate = this.client.birthdate
					? this.client.birthdate
					: "";
				this.newClient.gender = this.client.gender ? this.client.gender : "";
				if (this.client.address) {
					this.newClient.postcode =
						this.client.address.postcode != undefined &&
						this.client.address.postcode.length > 0
							? this.client.address.postcode
							: this.easy_postcode;
					this.newClient.street = this.client.address.street;
					this.newClient.state = this.client.address.state;
					this.newClient.number = this.client.address.number;
					this.newClient.city = this.client.address.city;
					this.newClient.neighborhood = this.client.address.neighborhood;
					this.newClient.complement = this.client.address.complement;
				} else {
					this.newClient.postcode = this.easy_postcode;
				}
			} else {
				if (this.clientSearch) {
					this.newClient.full_name = this.clientSearch;
				}
				this.newClient.cellphone = this.easy_ddd;
				this.newClient.postcode = this.easy_postcode;
				this.newClient.phone = this.easy_ddd;
				this.$refs.postcode.dispatchEvent(new Event("keyup")); // Força a busca inicial pelo CEP
			}
			this.$nextTick(function () {
				this.$refs.clientName.focus();
				$("#createUserModal").modal("show");
			});
		},

		openCancelModal() {
			$("#cancelModal").modal("show");
		},

		openBudgetModal() {
			$("#budgetModal").modal("show");
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

		async showServicesModalOrder(maintenanceId) {
			const params = {
				paginate: false,
			};

			if (!maintenanceId) params.last = true;
			else params.maintenance_id = maintenanceId;
			const _this = this;

			try {
				const { data } = await axios.get("/maintenances", { params });
				this.lastMaintenance = data;
				this.observation = this.note;
				setTimeout(async function () {
					await _this.$htmlToPaper("modalService");
				}, 200);
			} catch (error) {
				console.error("Error when try show modal service order", error);
			}
		},

		openSellerModal() {
			const hasOrderService = this.cart.find((order) => order.id == 1);
			if (hasOrderService) {
				// this.cart[0].selectedBrand = this.garanteeBrand;
				// this.cart[0].selectedModel = this.garanteeBrandModel;
				const { ordersError, hasErrors } = validateOrders(this.cart);
				this.orderError = ordersError;
				if (hasErrors) {
					return toastr.error(
						"Por favor confira os dados preenchidos nas orders"
					);
				}
			}

			$("#usersModal").modal("show");
		},

		openBudgetListModal() {
			const modalBudgetList = this.$refs.modalBudgetList;

			modalBudgetList.dateSelected = `${moment().format(
				"YYYY-MM-DD 00:00:00"
			)} até ${moment().subtract(7, "d").format("YYYY-MM-DD 00:00:00")}`;

			modalBudgetList.callStackUpdateCalendar = 0;
			modalBudgetList.dateIni = moment().format("YYYY-MM-DD 00:00:00");
			modalBudgetList.dateFim = moment()
				.subtract(7, "d")
				.format("YYYY-MM-DD 00:00:00");

			modalBudgetList.selectedStatus = this.isMaintenanceBudgets
				? [
						{ text: "Aguardando aprovação cliente", value: "waiting_approval" }, // Aguardando aprovação do cliente || tela::PDV || listagem de ordens
						{ text: "Sem conserto", value: "no_maintenance" }, //sem concerto || tela::Manutençao || listagem de ordens
						{ text: "Consertado", value: "fixed" }, //finalizado/consertado || tela::Manutençao || listagem de ordens
						{ text: "Aprovado", value: "approved" }, // Cliente liberou para que faça a manutenção || tela::PDV
						{ text: "Aguardando peça do fornecedor", value: "waiting_stock" }, //Aguardando peça do fornecedor || tela::Manutençao
						{ text: "Em manutenção", value: "maintenance" }, //Em manutenção || tela::Manutençao
						{ text: "Finalizado", value: "finished" },
						{
							text: "Sem conserto/Entregue",
							value: "no_maintenance_delivered",
						}, // Sem conserto e entregue
				  ]
				: [{ text: "Orçamento", value: "is_budget" }];

			$("#budgetListModal").modal("show");
		},
		openGaranteeModal() {
			if (this.isWarrantyForm) {
				this.$refs.modalGarantee.getOrders();
				$("#selectGaranteeModal").modal("show");
			}
		},

		handleGaranteeModal(event, index) {
			if (event.target.value) {
				this.openGaranteeModal();
			}
			this.cart[index].is_warranty = event.target.value ? 1 : 0;
			this.garanteeCartIndex = event.target.value ? index : null;
		},

		async getOrders(orderId) {
			const config = {
				params: {
					paginate: false,
					type: "vue",
				},
			};

			if (orderId) config.params.search = orderId;

			let url = "/orders";

			try {
				let { data } = await axios.get(url, config);
				if (orderId) {
					this.originalOrder = data[0];
					return;
				}
				this.orders = data.orders;
			} catch (error) {
				console.error(error);
			}
		},

		async handleDeliverNoMaintenance() {
			this.originalOrder.status = "no_maintenance_delivered";
			this.originalServiceOrder.maintenance.os_status =
				"no_maintenance_delivered";
			(this.originalServiceOrder.maintenance.due_date = moment(
				new Date()
			).format()),
				await axios
					.put(
						`/maintenances/${this.originalServiceOrder.maintenance.id}`,
						this.originalServiceOrder
					)
					.then((_r) => {
						toastr.success("Ordem de serviço atualizada com sucesso!");
					})
					.catch((e) => {
						console.error(e);
						toastr.error("Erro ao tentar atualizar a ordem de serviço!");
					});
		},
		async getBudgetList(status, dateIni, dateFim, search, isUpdating) {
			this.isMaintenanceBudgets = false;
			const config = {
				params: {
					paginate: false,
					clientId: this.client.id,
					type: "vue",
					status: status,
					search: search,
				},
			};

			if (dateIni) config.params.date_ini = dateIni;
			if (dateFim) config.params.date_fim = dateFim;

			let url = "/orders";

			try {
				let response = await axios.get(url, config);
				this.budgets = response.data;
				if (isUpdating) toastr.success("Orçamentos filtrados com sucesso!");
			} catch (error) {
				console.error(error);
			}
		},

		async getMaintenances(status, dateIni, dateFim, search, isUpdating) {
			const config = {
				params: {
					paginate: false,
					client_id: this.client && this.client.id ? this.client.id : null,
					// maintenanceStatus: "",
					// no_maintenance: true,
					// query_status: status,
					search: search,
					type: "vue",
				},
			};
			if (dateIni) config.params.date_ini = dateIni;
			if (dateFim) config.params.date_fim = dateFim;
			if (status) config.params.query_status = status;

			let url = "/maintenances";

			try {
				let response = await axios.get(url, config);
				this.budgets = response.data;
				this.isMaintenanceBudgets = true;
				if (isUpdating) toastr.success("Manutenções filtradas com sucesso!");
			} catch (error) {
				console.error(error);
			}
		},

		async openBudgetOrBudgetListModal() {
			if (!this.client || !this.client.id || this.client.id === 1) {
				await this.getBudgetList("is_budget");
				return this.openBudgetListModal();
			}

			if (this.client.id !== 1 && this.cart.length === 0) {
				await this.getBudgetList();
				return this.openBudgetListModal();
			}

			if (this.client.id !== 1 && this.cart.length > 0) {
				this.isBudgetOrder = true;
				this.openSellerModal();
			}
		},

		async openBudgetListMaintenances() {
			const dateIni = moment().subtract(7, "d").format("YYYY-MM-DD 00:00:00");
			const dateFim = moment().format("YYYY-MM-DD 00:00:00");

			if (!this.client || !this.client.id || this.client.id === 1) {
				const status = [
					"waiting_approval",
					"no_maintenance",
					"no_maintenance_delivered",
					"fixed",
					"approved",
					"waiting_stock",
					"maintenance",
					"finished",
				];
				await this.getMaintenances(status, dateIni, dateFim);
				this.openBudgetListModal();
			} else {
				await this.getMaintenances();
				this.openBudgetListModal();
			}
		},

		async storeOrder(budget) {
			let url = "/orders";
			let promiseOrders = [];
			let receipts = [];

			if (_.isEmpty(this.client) && this.cart.some((order) => order.id === 1)) {
				this.$refs.modalUsers.waitingSubmiting = false;
				return toastr.error(
					"Não é possível criar uma ordem de serviço sem um cliente, por favor selecione um cliente"
				);
			}

			if (
				this.cart.some(
					(order) =>
						order.id !== 1 ||
						(order.id === 1 && order.maintenance_info.os_status === "finished")
				)
			) {
				receipts.push("products");
			}

			if (
				this.cart.some(
					(order) =>
						order.id === 1 && order.maintenance_info.os_status !== "finished"
				)
			) {
				receipts.push("services");
			}

			if (!this.client || this.client === "") {
				await this.setClientDefault();
			}

			this.cart.forEach((item) => {
				if (item.checklist) {
					item.checklists = item.checklist
						.filter((el) => el.checked)
						.map((el) => el.id);
				}
			});

			// muda o status da manutenção para aguardando aprovação quando for ORÇAMENTO
			if (budget)
				this.cart.forEach((item) => {
					if (item.id === 1) {
						item.maintenance_info.os_status = "waiting_approval";
						item.is_warranty;
					}
				});

			/**
			 * Tipos
			 *
			 * Orçamento
			 * Venda
			 * Ordem de serviço (finalizada)
			 * Ordem de serviço (não finalizada)
			 *
			 * pode ou não ser splitada
			 */

			const ordersNotFinished = this.cart.filter(
				(order) =>
					order.maintenance_info &&
					order.maintenance_info.os_status !== "finished"
			);

			if (!_.isEmpty(ordersNotFinished) && this.cart.length > 1) {
				const ordersFinished = this.cart.filter(
					(order) =>
						!order.maintenance_info ||
						(order.maintenance_info &&
							order.maintenance_info.os_status === "finished")
				);
				const couponValue =
					this.coupon && this.coupon.value ? this.coupon.value : 0;

				const totalOrdersFinished = this.calculateCart(
					ordersFinished,
					couponValue
				);

				const data = {
					client_id: this.client ? this.client.id : 1,
					subtotal: totalOrdersFinished.subtotal,
					discount: totalOrdersFinished.discount,
					total: totalOrdersFinished.total,
					status: "waiting_payment",
					seller_id: this.sellerSelected ? this.sellerSelected.id : "",
					user_id: this.user.id,
					note: this.note,
					coupon_id: this.coupon ? this.coupon.id : null,
					products: ordersFinished,
					is_warranty: this.isWarrantyForm,
					order_id: this.isWarrantyForm ? this.garanteeOrderRef : null,
				};

				promiseOrders.push(axios.post(url, data));

				ordersNotFinished.forEach((order) => {
					const totalOrder = this.calculateCart([order]);

					const data = {
						client_id: this.client ? this.client.id : 1,
						subtotal: totalOrder.subtotal,
						discount: totalOrder.discount,
						total: totalOrder.total,
						status: budget ? "is_budget" : "waiting_maintenance",
						seller_id: budget
							? null
							: this.sellerSelected
							? this.sellerSelected.id
							: "",
						user_id: this.user.id,
						note: this.note,
						coupon_id: null,
						products: [order],
						is_warranty: this.isWarrantyForm,
						order_id: this.isWarrantyForm ? this.garanteeOrderRef : null,
					};

					promiseOrders.push(axios.post(url, data));
				});
			} else {
				let data = {
					client_id: this.client ? this.client.id : 1,
					subtotal: this.total,
					discount: this.discount,
					total: this.total,
					status: budget ? "is_budget" : "waiting_payment",
					seller_id: this.sellerSelected ? this.sellerSelected.id : "",
					user_id: this.user.id,
					note: this.note,
					coupon_id: this.coupon ? this.coupon.id : null,
					products: this.cart,
					is_warranty: this.isWarrantyForm,
					order_id: this.isWarrantyForm ? this.garanteeOrderRef : null,
				};
				promiseOrders.push(axios.post(url, data));
			}

			await Promise.allSettled(promiseOrders)
				.then((results) => {
					const rejected = results.find(
						(result) => result.status === "rejected"
					);
					if (rejected) {
						const { data } = rejected.reason.response;
						console.error("Error on storeOrder()", data);
						toastr.clear();
						toastr.error("Houve um problema na criação da ordem");
					} else {
						this.seller_id = budget
							? null
							: this.sellerSelected
							? this.sellerSelected.id
							: "";
						this.clearAllOrders();
						$("#usersModal").modal("hide");
						!_.isEmpty(this.sellerSelected)
							? toastr.success("Pedido criado com sucesso")
							: toastr.success("Orçamento criado com sucesso");
						this.errorCoupon = null;
						this.generateReceipts(receipts, budget);
					}
				})
				.catch((error) => {
					console.error("Error on storeOrder()", error);
					toastr.error("Houve um problema na criação da ordem");
				})
				.finally(() => {
					this.sellerSelected = {};
					this.products = [];
					this.garanteeProducts = [];
					this.$refs.modalUsers.waitingSubmiting = false;
					this.isBudgetOrder = false;
				});
		},

		async updateBudgetOrder() {
			const promises = [];
			let maintenance_ids = [];
			// atualiza total e subtotal da order
			this.originalOrder.subtotal =
				parseFloat(this.total) + parseFloat(this.discount);
			this.originalOrder.total = parseFloat(this.total);

			// adiciona cliente
			if (!this.originalOrder.client) {
				this.originalOrder.client = this.originalOrder.order.client;
				// this.originalOrder.client_id = this.originalOrder.order.client
				// 	? this.originalOrder.order.client.id
				// 	: 1;
			}

			// adiona vendedor
			if (this.sellerSelected) {
				this.originalOrder.seller_id = this.sellerSelected.id;
				this.originalOrder.seller = this.sellerSelected;
				this.seller_id = this.sellerSelected.id;
			}

			this.originalOrder.coupon_id =
				this.coupon && this.coupon.id ? this.coupon.id : null;

			this.originalOrder.note = this.note;

			// atualiza os dados dos produtos
			this.cart.forEach((el) => (el.product_id = el.id));
			this.originalOrder.products = this.cart;

			const maintenanceOrders = this.originalOrder.products.filter(
				(el) => el.product_id === 1
			);

			if (maintenanceOrders) {
				maintenanceOrders.forEach(async (order) => {
					const maintenanceInfo = order.maintenance_info;
					delete maintenanceInfo.tecnician;

					maintenance_ids.push(maintenanceInfo.id);

					order.maintenance = maintenanceInfo;
					promises.push(
						await axios.put(`/maintenances/${maintenanceInfo.id}`, order)
					);
				});
			}

			if (
				(_.isEmpty(maintenanceOrders) && !this.originalServiceOrder) ||
				(maintenanceOrders &&
					maintenanceOrders.every(
						(order) => order.maintenance_info.os_status === "finished"
					))
			) {
				/**
				 * "waiting_payment"
				 * Se não tem nenhuma ordem de serviço
				 * Se tem ordem de serviço e todas estão como finalizadas
				 */
				this.originalOrder.status = "waiting_payment";
			} else if (
				maintenanceOrders &&
				maintenanceOrders.some(
					(order) => order.maintenance_info.os_status === "no_maintenance"
				)
			) {
				/**
				 * "canceled"
				 * Se tem alguma ordem de serviço como "Sem conserto"
				 */
				this.originalOrder.status = "canceled";
			} else {
				this.originalOrder.status = "approved";
			}

			this.$forceUpdate();
			promises.push(
				await axios.put(`/orders/${this.originalOrder.id}`, this.originalOrder)
			);

			await Promise.all(promises)
				.then((_r) => {
					toastr.success("Orçamento atualizado com sucesso!");

					if (
						!_.isEmpty(maintenanceOrders) &&
						this.originalOrder.products[0].maintenance_info.os_status !==
							"finished"
					) {
						const maintenanceId =
							this.originalOrder.products[0].maintenance_info.id;
						this.showServicesModalOrder(maintenanceId);
					}

					if (this.originalOrder.status === "waiting_payment") {
						this.showBudgetModalOrder(this.originalOrder.id);
					}
					this.clearAllOrders();
				})
				.catch((e) => {
					console.error("Error on updateBudgetOrder()", e);
					toastr.error("Não foi possível atualizar o orçamento");
				})
				.finally(() => {
					this.sellerSelected = {};
					this.products = [];
					this.garanteeProducts = [];
					this.$refs.modalUsers.waitingSubmiting = false;
					$("#usersModal").modal("hide");
				});
		},

		handleCancel() {
			$("#cancelModal").modal("hide");
			this.client = this.clientDefault;
			this.cart = [];
			this.clientSearch = "";
			this.productSearch = "";
			this.calculateCart();
		},

		async submitCreateClient() {
			if (this.client && this.client.id != undefined) {
				await this.submitUpdateClient();
			} else {
				toastr.options = {
					positionClass: "toast-bottom-right",
				};
				toastr.info("Criando cliente...");
				try {
					toastr.clear();
					let response = await axios.post("/clients", this.newClient);
					//this.newClient = {}; // Clear new client form
					this.client = response.data;
					this.clientSearch = this.client.full_name;
					this.showListClient = false;
					$("#createUserModal").modal("hide");
					toastr.success("Cliente criado com sucesso");
				} catch (error) {
					toastr.clear();
					if (error.response.data.errors.cpf) {
						toastr.error(error.response.data.errors.cpf);
					} else {
						toastr.error("Erro ao cadastrar cliente");
					}
				}
			}
		},

		async submitUpdateClient() {
			toastr.options = {
				positionClass: "toast-bottom-right",
			};
			toastr.info("Atualizando cliente...");

			try {
				toastr.clear();
				if (this.newClient["email"] === null) {
					this.newClient["email"] = "";
				}

				let response = await axios.put(
					"/clients/" + this.client.id,
					this.newClient
				);
				this.client = response.data;
				this.clientSearch = this.client.full_name;
				//this.newClient = {}; // Clear new client form
				this.showListClient = false;
				$("#createUserModal").modal("hide");
				toastr.success("Cliente atualizado com sucesso");
			} catch (error) {
				toastr.clear();
				if (error.response.data.errors.cpf) {
					toastr.error(error.response.data.errors.cpf);
				} else {
					toastr.error("Erro ao atualizar cliente");
				}
			}
		},

		async handleViacep() {
			const postcode = this.newClient.postcode.replace(/\D/g, "");

			if (postcode.length === 8) {
				this.newClient.street = "";
				this.newClient.neighborhood = "";
				this.newClient.city = "";
				this.newClient.state = "";

				const { data } = await axios.get(
					`https://viacep.com.br/ws/${postcode}/json`
				);
				if (data.erro) {
					toastr.options = {
						positionClass: "toast-bottom-right",
					};
					toastr.error("CEP não encontrado");
					return;
				}
				this.newClient.street = data.logradouro;
				this.newClient.neighborhood = data.bairro;
				this.newClient.city = data.localidade;
				this.newClient.state = data.uf;
				this.$forceUpdate();
			}
		},

		previewFile(e) {
			const supportedMime = ["image/jpeg", "image/png"];
			const { type } = e.target.files[0];
			const [image] = e.target.files;
			this.newClient.photo = image;
			if (supportedMime.indexOf(type) > -1) {
				const reader = new FileReader();
				reader.readAsDataURL(e.target.files[0]);
				reader.onload = () => {
					this.$refs.formFile.style.background = `url('${reader.result}') no-repeat center center / cover`;
				};
			}
		},

		async clearSelectedClient() {
			this.client = {};
			this.clientSearch = "";
			this.showListClient = false;
		},
		async setClientDefault() {
			this.clientSearch = " ";
			await this.getClients(1);
			this.clientSearch = "";
			this.client = this.clients[0];
			this.showListClient = false;
		},

		addCart(product) {
			this.arrowCounterProd = 0;

			let productInCart = this.cart.find(
				(element) => element.productName === product.name && element.id !== 1
			);

			if (productInCart === undefined) {
				this.cart.push({
					id: product.id,
					productName: product.name,
					barcode: product.barcode,
					amount: 1,
					price: parseFloat(product.price),
					editDiscount: false,
					editAddition: false,
					discount: product.discount ? product.discount : 0,
					discount_percentage: product.discount_percentage,
					stock: 10,
					subtotal: parseFloat(product.price),
					by_products: [],
					maintenance_info:
						product.id === 1
							? {
									os_status: "approved",
									due_date: moment().add(3, "d").format("YYYY-MM-DD 00:00:00"),
							  }
							: null,
					checklist: product.id === 1 ? _.cloneDeep(this.checklist) : null,
					due_date: product.id === 1 ? moment().add(3, "d").format() : null,
				});
			} else {
				let productIndex = this.cart.findIndex(
					(element) => element.productName === product.name
				);
				if (this.cart[productIndex].amount >= this.cart[productIndex].stock)
					return;

				let subtotal =
					parseFloat(this.cart[productIndex].subtotal) +
					parseFloat(product.price);

				this.cart[productIndex].amount++;
				this.cart[productIndex].subtotal = subtotal;
			}

			this.productSearch = "";
			this.productGaranteeSearch = "";
			this.showListProduct = false;

			this.calculateCart();
		},

		addMaintenanceProduct(product, index) {
			this.arrowCounterProd = 0;

			this.cart[index].by_products.push({
				id: product.id,
				productName: product.name,
				barcode: product.barcode,
				amount: 1,
				price: parseFloat(product.price),
				editDiscount: false,
				editAddition: false,
				addition: 0,
				discount: product.discount ? product.discount : 0,
				discount_percentage: product.discount_percentage,
				stock: product.quantity_in_stock,
				subtotal: parseFloat(product.price),
			});

			this.productGaranteeSearch = "";
			this.showListProductMaintenance = false;

			this.calculateCart();
		},

		calculateCart(cart, couponValue = 0) {
			let discount = 0;
			let total = 0;
			let amount = 0;
			let addition = 0;
			const cartCalculate = cart ? cart : this.cart;

			cartCalculate.map((item) => {
				if (!item.addition) item.addition = 0;
				let amountSubtotal = 0;
				amount += item.amount;
				discount += item.discount * item.amount;
				addition += item.addition * item.amount;

				amountSubtotal =
					(item.price - item.discount + item.addition) * item.amount;

				item.subtotal =
					amountSubtotal < 0 || typeof amountSubtotal !== "number"
						? 0
						: amountSubtotal;

				total += item.subtotal > 0 ? item.subtotal : 0;

				if (item.by_products)
					item.by_products.map((el) => {
						if (el.product) el.price = el.product.price;
						let amountSubtotal = 0;
						discount += el.discount * el.amount;
						amountSubtotal = (el.price - el.discount + el.addition) * el.amount;
						el.subtotal = amountSubtotal > 0 ? amountSubtotal : 0;
						total += el.subtotal > 0 ? el.subtotal : 0;
					});
			});

			if (cart)
				return {
					total: parseFloat(total) - parseFloat(couponValue),
					subtotal: parseFloat(total) + parseFloat(discount),
					discount: discount,
				};

			if (this.coupon) {
				this.discount = discount + parseFloat(this.coupon.value);
				this.total = total - parseFloat(this.coupon.value);
			} else {
				this.discount = discount;
				this.total = total;
			}

			if (this.total < 0) this.total = 0;
			this.subtotal = this.total + this.discount;
			this.addition = addition;
			this.amount = amount;
			this.$forceUpdate();
		},

		addAmountProductCart(index) {
			if (this.cart[index].amount >= this.cart[index].stock) return;
			this.cart[index].amount++;
			this.calculateCart();
		},

		removeAmountProductCart(index) {
			this.cart[index].amount--;
			if (this.cart[index].amount === 0) {
				this.removeProductCart(index);
			} else {
				this.calculateCart();
			}
		},

		removeProductCart(index) {
			this.isWarrantyForm = false;
			this.garanteeOrderRef = null;
			this.cart.splice(index, 1);
			this.calculateCart();
		},

		removeMaintenanceProduct(cart, index) {
			cart.splice(index, 1);
			this.calculateCart();
		},

		editProductDiscount(cart, index) {
			cart[index].editDiscount = true;
			this.$forceUpdate();
		},
		editProductAddition(cart, index) {
			cart[index].editAddition = true;
			this.$forceUpdate();
		},

		async validateAdminPassword(password) {
			const { cart, index } = this.adminPassValidate;

			const config = {
				password: password,
			};

			await axios
				.post("secretPassword", config)
				.then((r) => {
					if (r.status === 200) {
						cart[index].discount = _.clone(cart[index].draftDiscount);
						cart[index].editDiscount = false;
						this.calculateCart();
						$("#modalPassAdmin").modal("hide");
						this.adminPassValidate = {};
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
			this.$forceUpdate();
		},

		handleCloseModalAdminPass() {
			const { cart, index } = this.adminPassValidate;
			cart[index].draftDiscount = cart[index].discount || 0;
			cart[index].editDiscount = false;
		},

		applyDiscount(cart, index) {
			this.errors = {};
			toastr.options = {
				positionClass: "toast-bottom-right",
			};
			var percentageDiscount =
				(cart[index].draftDiscount / cart[index].price) * 100;
			if (percentageDiscount > cart[index].discount_percentage) {
				$("#modalPassAdmin").modal("show");
				this.$refs.inputPass.focus();
				this.adminPassValidate.cart = cart;
				this.adminPassValidate.index = index;
				return;
			} else if (cart[index].discount < 0) {
				toastr.error("O valor de desconto não pode ser negativo");
				cart[index].discount = 0;
				cart[index].editDiscount = true;
				this.errors["discount"] = "O valor de desconto não pode ser negativo";
			} else {
				cart[index].discount = _.clone(cart[index].draftDiscount);
				cart[index].editDiscount = false;
			}
			this.calculateCart();
			this.$forceUpdate();
		},

		applyAddition(cart, index) {
			cart[index].addition = cart[index].draftAddition;
			cart[index].editAddition = false;
			this.calculateCart();
			this.$forceUpdate();
		},

		clearModalClient() {
			this.newClient = {
				street: "",
				uf: "",
				number: "",
				city: "",
				neighborhood: "",
				full_name: "",
				is_active: 1,
				phone: this.easy_ddd,
				cpf: "",
				email: "",
				rg: "",
				cellphone: this.easy_ddd,
				postcode: this.easy_postcode,
			};
		},

		addProductsOrderToCart(order) {
			order.products.map((product) => {
				this.addCart(product);
			});
			this.discount = order.discount;
			this.total = order.total;
			$("#budgetListModal").modal("hide");
		},
		validateOrders: _.debounce(function () {
			const { ordersError } = validateOrders(this.cart);
			this.orderError = ordersError;
		}, 500),
		getBrands: _.debounce(async function (item, e) {
			if (e.key === "Enter") return;
			this.searchBrand = e.target.value;
			axios
				.get("/brands", {
					params: {
						paginate: false,
						search: this.searchBrand,
						order_by: "name",
						order: "asc",
					},
				})
				.then(({ data }) => {
					this.brands = data;
					item.showBrandList = true;
				})
				.catch((e) => console.error("Error in getBrands", e));
		}, 150),
		handleSelectedBrand(item, event) {
			item.brand_id = event;
			this.brands = [];
		},
		getBrandsModels: _.debounce(async function (item, e) {
			if (e.key === "Enter") return;
			const search = e.target.value;
			const brandId = item.brand_id;
			axios
				.get("/brand-models", {
					params: {
						brand_id: brandId,
						search: search,
						all: true,
						paginate: false,
					},
				})
				.then(({ data, status }) => {
					this.brandsModels = data;
					item.showModelList = true;
					item.createNew = false;
					if (_.isEmpty(data) && status === 200) item.createNew = true;
				})
				.catch((e) => console.error("Error in getBrandsModels", e));
		}, 150),
		handleSelectedModel(item, event) {
			item.brand_model = event;
			this.brandsModels = [];
		},
		handleItemCreated(item) {
			item.createNew = false;
			this.$forceUpdate();
		},
		async getCheckLists() {
			axios
				.get("/configurations/checklists", {
					params: { paginate: false },
				})
				.then(({ data }) => (this.checklist = data))
				.catch((e) => console.error("Error in getChecklists", e));
		},
		fillGaranteeData({ order, product }) {
			this.garanteeOrderRef = order.id;
			const currenteGaranteeCart = this.cart[this.garanteeCartIndex];

			if (product.maintenance && product.maintenance.brand) {
				currenteGaranteeCart.garanteeBrand = product.maintenance.brand;
				currenteGaranteeCart.brand_id = product.maintenance.brand.id;
			} else {
				currenteGaranteeCart.garanteeBrand = { name: "Shopp Cell" };
				currenteGaranteeCart.brand_id = 1;
			}

			if (product.maintenance && product.maintenance.brand_model) {
				currenteGaranteeCart.garanteeBrandModel =
					product.maintenance.brand_model;

				currenteGaranteeCart.brand_model_id =
					product.maintenance.brand_model.id;

				currenteGaranteeCart.brand_model = product.maintenance.brand_model;
			} else {
				currenteGaranteeCart.garanteeBrandModel = { name: "" };
				currenteGaranteeCart.brand_model = 5573;
			}
			this.isWarrantyForm = true;
			$("#selectGaranteeModal").modal("hide");
		},
		handleCancelGarantee() {
			this.garanteeOrderRef = null;
			this.isWarrantyForm = false;
			$("#selectGaranteeModal").modal("hide");
		},
		clearAllOrders() {
			this.cart = [];
			this.total = 0;
			this.discount = 0;
			this.coupon = null;
			this.searchCoupon = "";
			this.client = {};
			this.clientSearch = "";
			this.clientDefault = "";
			this.consumidor = false;
			this.productSearch = "";
			this.productGaranteeSearch = "";
			this.errorCoupon = null;
			this.orderError = null;
			this.garanteeOrderRef = null;
			this.isWarrantyForm = false;
			this.note = "";
			this.addition = null;
			this.checklist = [];
			this.isUpdateOrder = false;
			this.isOrderCanceled = false;
			this.isNotEditableOrder = false;
			this.isNotPrintableOrder = false;
			this.comments = null;
			this.originalOrder = null;
			this.originalServiceOrder = null;
			this.isBudgetOrder = false;
			this.$forceUpdate();
			this.getCheckLists();
		},
		setDueDate(index, item) {
			this.cart[index].maintenance_info.due_date = moment(item.due_date)
				.utc(false)
				.format("YYYY-MM-DD hh:mm:ss");
			this.$forceUpdate();
		},
		typeMaintenance(index) {
			return ["no_maintance", "fixed"].includes(
				this.cart[index].maintenance_info.os_status
			);
		},

		async handleFillDataBudget(order) {
			if (order.product_id === 1) {
				await this.getOrders(order.id);
				await this.handleFillMaintenanceData(order);
			} else await this.handleFillBudgetData(order);
			this.budgets = [];
		},

		async handleFillBudgetData(order) {
			this.isUpdateOrder = true;
			this.isNotEditableOrder =
				order.status === "concluded" || order.status === "canceled";
			this.isOrderCanceled = order.status === "canceled";
			this.originalOrder = order;
			this.isNotPrintableOrder = order.status === "canceled";

			this.client = order.client;
			this.clientSearch = order.client.full_name;

			this.note = order.note;

			if (order.coupon) {
				this.coupon = order.coupon;
				this.searchCoupon = order.coupon.name;
			}

			const cartProducts = [];
			const brandAndModelsNames = [];

			order.products.forEach((item, index) => {
				if (item.product_id !== 1) {
					const subtotal =
						(parseFloat(item.price) -
							parseFloat(item.discount) +
							parseFloat(item.addition)) *
						item.amount;

					cartProducts.push({
						id: item.product_id,
						productName: item.product.name,
						amount: item.amount,
						price: parseFloat(item.price),
						addition: parseFloat(item.addition),
						discount: parseFloat(item.discount),
						discount_percentage: item.product.discount_percentage,
						subtotal: subtotal,
						editDiscount: false,
						editAddition: false,
					});
				} else {
					item.by_products.forEach((item) => {
						item.productName = item.product.name;
						item.subtotal =
							(parseFloat(item.price) -
								parseFloat(item.discount) +
								parseFloat(item.addition)) *
							item.amount;
						item.price = parseFloat(item.price);
						item.discount = parseFloat(item.discount);
						item.addition = parseFloat(item.addition);
					});

					const subtotal =
						(parseFloat(item.price) -
							parseFloat(item.discount) +
							parseFloat(item.addition)) *
						item.amount;

					const maintenance = _.cloneDeep(item.maintenance) || {};
					delete maintenance.brand;
					delete maintenance.brand_model;

					cartProducts.push({
						id: item.product_id,
						productName: item.product.name,
						amount: item.amount,
						price: parseFloat(item.price),
						addition: parseFloat(item.addition),
						discount: parseFloat(item.discount),
						discount_percentage: item.product.discount_percentage,
						subtotal: subtotal,
						by_products: item.by_products,
						maintenance_info: maintenance,
						checklist: _.cloneDeep(this.checklist),
						editDiscount: false,
						editAddition: false,
						brand_id: maintenance.brand_id,
						brand_model: maintenance.brand_model_id,
						due_date: maintenance.due_date,
					});

					if (item.maintenance) {
						const { brand, brand_model } = item.maintenance;
						brandAndModelsNames.push({
							brandName: brand.name,
							modelName: brand_model.name,
							index: index,
						});
					}

					const { checklists } = item.product;
					checklists.forEach((checklist) => {
						cartProducts[cartProducts.length - 1].checklist.find(
							(el) => el.id === checklist.id
						).checked = true;
					});
				}
			});

			this.cart = cartProducts;
			setTimeout(() => {
				brandAndModelsNames.forEach((el) => {
					this.$refs[`sel-brand-${el.index}`][0].search = el.brandName;
					this.$refs[`sel-model-${el.index}`][0].search = el.modelName;
				});
			}, 200);

			$("#budgetListModal").modal("hide");
			this.isMaintenanceBudgets = false;
			this.calculateCart();
		},

		async handleFillMaintenanceData(order) {
			this.isUpdateOrder = true;
			this.isNotEditableOrder =
				order.order.status === "concluded" || order.order.status === "canceled";
			this.isOrderCanceled = order.order.status === "canceled";
			this.originalServiceOrder = order;
			this.originalOrder = order.order;
			this.isNotPrintableOrder = order.order.status === "canceled";
			order.subtotal = order.price;

			const { coupon, client, note } = order.order;
			const discountCoupon =
				coupon && coupon.value ? parseFloat(coupon.value) : 0;

			this.coupon = coupon;
			this.searchCoupon = coupon && coupon.name ? coupon.name : "";
			this.clientSearch = client.full_name;
			this.note = note;

			const {
				due_date,
				os_status,
				issue,
				user_id,
				brand,
				brand_model,
				technical_report,
				id: maintenanceId,
			} = order.maintenance ? order.maintenance : {};

			// parseFloat
			order.by_products.forEach((prod) => {
				(prod.addition = parseFloat(prod.addition)),
					(prod.discount = parseFloat(prod.discount)),
					(prod.price = parseFloat(prod.price));
			});

			this.cart[0] = {
				id: 1,
				amount: parseInt(order.amount),
				productName: order.product.name,
				price: parseFloat(order.price),
				addition: parseFloat(order.addition),
				discount: parseFloat(order.discount),
				subtotal: parseFloat(order.price),
				checklist: _.cloneDeep(this.checklist),
				maintenance_info: {
					id: maintenanceId,
					due_date: due_date,
					os_status: os_status,
					issue: issue,
					user_id: user_id,
					technical_report: technical_report,
					brand_id: brand.id,
					brand_model_id: brand_model.id,
				},
				due_date: due_date,
				by_products: order.by_products,
				brand_id: brand.id,
				brand_model: brand_model.id,
			};

			setTimeout(() => {
				this.$refs[`sel-brand-0`][0].search = brand.name;
				this.$refs[`sel-model-0`][0].search = brand_model.name;
			}, 250);

			const { checklists } = order.product;
			checklists.forEach((checklist) => {
				this.cart[0].checklist.find(
					(el) => el.id === checklist.id
				).checked = true;
			});

			$("#budgetListModal").modal("hide");
			this.isMaintenanceBudgets = false;
			this.calculateCart();

			this.total = order.order.total;
			this.subtotal =
				parseFloat(order.order.total) + parseFloat(order.order.discount);
			this.discount = discountCoupon;
			this.$forceUpdate();
			await this.getComments();
		},
		async getComments() {
			const config = {
				params: {
					paginate: false,
				},
			};

			const orderId = this.originalOrder
				? this.originalOrder.id
				: this.originalServiceOrder.id;

			try {
				let response = await axios.get(`/orders/${orderId}/comments`, config);
				this.comments = response.data;
				this.$refs.modalComment.scrollComments();
			} catch (error) {
				console.error(error);
			}
		},
		async submitComment(newComment) {
			await axios
				.post(`/orders/${this.currentOrder.id}/comments`, newComment, {
					headers: {
						"Content-Type": "multipart/form-data",
					},
				})
				.then((r) => {
					if (r.status === 200) {
						this.comments.push(newComment);
						this.$refs.modalComment.clearData();
						this.$refs.modalComment.scrollComments();
					}
				})
				.catch((e) => console.error("Error Comment", e));
		},
		budgetPrint() {
			$("#budgetModal").modal("hide");
		},
		showComments() {
			$("#commentModal").modal("show");
		},
		orderValidation: _.debounce(function (event, index) {
			const elementKey = event.srcElement.name;

			if (elementKey === ".maintenance_info.issue") {
				if (
					this.orderError &&
					this.orderError[index] &&
					this.orderError[index].maintenance_info
				)
					this.orderError[index].maintenance_info.issue = validateOrders(
						this.cart
					).ordersError[index].maintenance_info.issue;
			} else {
				if (this.orderError && this.orderError[index])
					this.orderError[index][elementKey] = validateOrders(
						this.cart
					).ordersError[index][elementKey];
			}
		}, 300),
	},
};
</script>

<style lang="scss">
.order-create {
	.table-orders {
		border-collapse: separate;
		border-spacing: 0 10px;
		margin-top: -10px; /* correct offset on first border spacing if desired */
	}

	li:hover {
		background-color: #f2f4f6;
		cursor: pointer;
	}

	.btn-pdv {
		border: 1px solid #e4e7ed;
		border-radius: 4px;

		i {
			font-size: 15px;
		}
	}

	.btn-new-client {
		background: #21316f;
		border: 1px solid #e4e7ed;
		border-radius: 4px;

		i {
			font-size: 15px;
		}
	}

	.box-secondary {
		background: rgba(228, 231, 237, 0.5);
		border: 1px solid rgba(228, 231, 237, 0.5);
		box-sizing: border-box;
		border-radius: 4px;
		margin-top: 15px;
	}

	.card-body {
		height: auto;
	}

	.search-popup {
		position: absolute;
		top: 38px;
		z-index: 20;
		width: calc(100% - 16px);
		overflow: auto;
		color: #4b545c;
		box-shadow: 0 4px 12px 0 rgb(0, 0, 0, 20%);
	}

	.search-popup li.is-active {
		color: #0983e8;
		background-color: #e3f2ff;
	}

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

	.list-group {
		max-height: 360px;
	}

	@media (min-width: 1024px) {
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
		max-width: 90px;
	}

	.observations-card {
		background: #fafbfc;
		border-radius: 0px 0px 4px 4px;
		padding: 30px;

		.total-card {
			background: rgba(241, 244, 246, 0.7);
			border: 1px solid #e4e7ed;
			box-sizing: border-box;
			border-radius: 4px;
			padding: 10px 20px;

			div {
				border-bottom: 1px solid #e4e7ed;
				padding-right: 30px;

				&:last-child {
					border: none;
				}

				span {
					color: #4b545c;
					line-height: 3;
				}

				.value {
					color: #0983e8;
				}
			}
		}
	}

	.product-card {
		position: relative;
		overflow: hidden;
	}

	.product-card:after {
		content: " ";
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 32px;
		font-weight: bold;
		color: white;
		line-height: 100%;
		cursor: pointer;
		width: 100%;
		height: 100%;
		background-color: transparent;
		transition: 0.4s all ease-in-out;
		position: absolute;
		top: 0;
		right: 0;
	}

	.product-card:hover:after {
		content: "+";
		background-color: rgba(0, 0, 0, 0.5);
	}

	.available {
		color: #0983e8;
	}

	.unavailable {
		color: #d63030;
	}

	.card-body {
		margin: 0;
		padding: 0 8px;
		padding-top: 4px;
		margin-bottom: 8px;
	}

	.modal-footer {
		justify-content: center;
	}

	.border-bottom-dashed {
		border-bottom: 1px dashed #dee2e6 !important;
	}

	.card-seller:hover {
		background-color: rgba(228, 231, 237, 0.5);
		cursor: pointer;
	}

	.hover {
		background-color: rgba(228, 231, 237, 0.5);
	}

	.modal-body-seller {
		height: 400px;
		overflow-y: auto;
	}

	.is-invalid {
		border: 1px solid red !important;
	}

	.table-budget {
		width: 100%;
		height: 100%;
		min-width: 900px;
		overflow-x: auto;

		tbody {
			display: block;
			max-height: calc(68vh - 52px);
			overflow: auto;
		}

		thead,
		tbody tr {
			display: table;
			width: 100%;
			table-layout: fixed;
		}

		thead {
			width: calc(100% - 1em);
		}
	}

	.order-listed {
		&.product-only {
			&:hover {
				// TODO HOVER LINE
			}
		}

		.item-summary {
			padding: 10px;

			&__name {
				min-width: 25%;
			}

			&__price {
				width: 15%;
				text-align: center;
			}

			&__addition {
				width: 15%;
			}

			&__discount {
				width: 15%;
			}

			&__subtotal {
				width: 15%;
				text-align: center;
			}
		}

		.divider {
			height: 1px;
			background-color: #e4e7ed;
			width: 100%;
			margin: 20px 0;
		}
	}

	.btn-comments {
		&:hover {
			color: #0983e8;
		}
	}

	.warranty-form {
		background: #fafbfc;
		border: 1px solid #e4e7ed;
		box-sizing: border-box;
		border-radius: 0px 0px 4px 4px;
		min-height: 500px;
		padding: 10px;

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
			max-width: 90px;
		}

		.item-summary {
			&__name {
				min-width: 35%;
			}

			&__price {
				width: 15%;
				text-align: center;
			}

			&__addition {
				width: 15%;
			}

			&__discount {
				width: 15%;
			}

			&__subtotal {
				width: 15%;
				text-align: center;
			}
		}
	}

	.td,
	.th {
		&__prod-name,
		&__prod-amount,
		&__prod-price,
		&__prod-addition,
		&__prod-discount,
		&__prod-subtotal {
			span {
				display: block;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}
		}

		&__prod-name {
			width: 25%;
			min-width: 25%;
		}

		&__prod-amount,
		&__prod-price {
			min-width: 15%;
		}

		&__prod-addition,
		&__prod-discount {
			width: 17%;
		}

		&__prod-subtotal {
			width: 12%;
		}
	}

	.opacity-1 {
		opacity: 1;
	}

	.opacity-03 {
		opacity: 0.3;
	}

	.card-deck .cw-70 {
		flex: 1 0 69.2%;
		max-width: 69.2%;
	}

	.card-deck .cw-30 {
		flex: 1 0 30.8%;
		max-width: 30.8%;
	}

	.cursor-pointer-hover {
		&:hover {
			cursor: pointer;
		}
	}

	.label-calendar {
		font-size: 14px;
		color: #4b545c;
		font-family: Roboto;
		line-height: 20px;
	}

	.btn-calendar {
		padding: 8px 12px;
	}

	.dp-calendar {
		max-width: 100% !important;
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
}
</style>
