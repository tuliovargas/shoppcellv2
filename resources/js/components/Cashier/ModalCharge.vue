<template>
	<div
		class="modal"
		id="chargeModal"
		tabindex="-1"
		role="dialog"
		ref="chargeModal"
	>
		<form class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title font-weight-bold text-center w-100">
						Adicionar troco
					</h5>
				</div>
				<div class="modal-body">
					<h5>Digite abaixo o valor do troco:</h5>
					<money class="money w-100" v-model="chargeValue" v-bind="money" />
				</div>
				<div class="modal-footer">
					<div class="d-flex align-items-center justify-items-end w-75">
						<button
							type="button"
							data-dismiss="modal"
							aria-label="Close"
							class="btn btn-dark ml-auto"
						>
							Cancelar
						</button>
						<button
							type="button"
							aria-label="Confirm"
							data-dismiss="modal"
							class="btn btn-success h-75 ml-2"
							@click="$emit('changedCharge', chargeValue)"
						>
							Confirmar
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</template>

<script>
export default {
	name: "ModalCharge",
	props: {
		charge: "",
	},
	data(props) {
		return {
			chargeValue: props.charge,
			money: {
				decimal: ",",
				thousands: ".",
				prefix: "",
				suffix: " ",
				precision: 2,
				masked: false,
			},
		};
	},
	methods: {
		changeCharge() {},
	},
};
</script>
