<template>
	<div>
		<div class="card card-observation">
			<div class="form-group mb-0">
				<label class="d-block text-uppercase">Observação</label>
				<textarea
					class="w-100 h-100 txt-observations form-control"
					placeholder="Observação"
					v-model="form.observations"
				>
				</textarea>
			</div>
		</div>
		<div class="card card-images">
			<div class="form-group px-2 mb-0">
				<div class="custom-file custom-file-lg">
					<input
						type="file"
						class="custom-file-input px-2"
						id="customFile"
						lang="pt-br"
						@change="addFile"
						multiple
					/>
					<label
						class="custom-file-label"
						for="customFile"
						data-browse="Selecionar"
						>Escolha um arquivo</label
					>
				</div>
			</div>
			<ul class="list-group list-images px-2">
				<li
					class="list-group-item d-flex justify-content-between align-items-center"
					v-for="(file, i) in form.filesList"
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
</template>

<script>
export default {
	data() {
		return {
			form: {
				observations: null,
				files: [],
				filesList: []
			}
		};
	},

	methods: {
		async addFile(event) {
			const mimeDb = require("mime-db")
			let fileData
			const file = event.target.files[0];
			const base64File = await this.convertToBase64(file)
			if (!_.isEmpty(file.type)) fileData = mimeDb[file.type]

			const data = {
				name: file.name,
				extension: fileData ? fileData.extensions[0] : 'unknow',
				mimeType: fileData ? file.type : 'unknow',
				data: base64File
			}

			this.form.files.push(data);
			this.form.filesList.push(file);
		},

		removeImageFromList(index) {
			this.form.files.splice(index, 1);
			this.form.filesList.splice(index, 1);
		},

		convertToBase64(file) {
			return new Promise((resolve, reject) => {
				const reader = new FileReader();
				reader.readAsDataURL(file);
				reader.onload = () => resolve(reader.result);
				reader.onerror = error => reject(error);
			});
		}
	}
};
</script>

<style lang="scss">
.txt-observations {
	resize: none;
	min-height: 230px;
}

.card-observation,
.card-images {
	padding: 30px;
}

.list-images {
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
</style>
