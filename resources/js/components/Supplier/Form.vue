<template>
  <div class="container mx-auto">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              {{
                this.supplierId == "0"
                  ? "Cadastrar Novo Fornecedor"
                  : "Editar Fornecedor"
              }}
            </h3>
          </div>
          <form method="POST" enctype="multipart/form-data" action="/suppliers">
            <!-- /.card-header -->
            <div class="card-body">
              <div class="row">
                <div class="col-2 mx-auto">
                  <div class="card">
                    <img
                      src="/images/default-avatar.png"
                      alt=""
                      class="card-img-top w-100"
                      ref="photo"
                    />
                    <div class="d-flex border">
                      <label
                        class="position-absolute d-flex align-self-center text-center justify-content-center"
                      >
                        <input
                          type="file"
                          class="d-flex custom-file-input"
                          name="photo"
                          id="photo"
                        />
                        <span
                          class="bg-dark bg-gradient-dark w-100 position-absolute"
                          >editar</span
                        >
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-10">
                  <div class="form-group col-12">
                    <label for="name">Nome</label>
                    <input
                      type="text"
                      v-model="newProvider.name"
                      class="form-control"
                      name="name"
                      id="name"
                    />
                  </div>
                  <div class="form-group col-12">
                    <label for="cnpj">CNPJ</label>
                    <input
                      v-model="newProvider.cnpj"
                      v-mask="'##.###.###/####-##'"
                      type="text"
                      class="form-control"
                      name="cnpj"
                      id="cpnj"
                      :class="{ invalid: !cnpjIsValid }"
                      :disabled="supplierId != 0"
                    />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-6">
                  <label for="state_registration">Inscrição Estadual</label>
                  <input
                    v-model="newProvider.state_registration"
                    v-mask="'###.###.###.###'"
                    type="text"
                    class="form-control"
                    name="state_registration"
                    id="state_registration"
                  />
                </div>
                <div class="form-group col-6">
                  <label for="responsible_person">Responsável</label>
                  <input
                    v-model="newProvider.responsible_person"
                    type="text"
                    class="form-control"
                    name="responsible_person"
                    id="responsible_person"
                  />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-6">
                  <label for="cellphone">Celular</label>
                  <input
                    v-model="newProvider.cellphone"
                    v-mask="'(##) 9####-####'"
                    type="text"
                    class="form-control"
                    name="cellphone"
                    id="cellphone"
                  />
                </div>
                <div class="form-group col-6">
                  <label for="phone">Telefone</label>
                  <input
                    v-model="newProvider.phone"
                    v-mask="'(##) ####-####'"
                    type="text"
                    class="form-control"
                    name="phone"
                    id="phone"
                  />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-12">
                  <label for="observation">Observação</label>
                  <input
                    v-model="newProvider.observation"
                    type="text"
                    class="form-control h-100"
                    name="observation"
                    id="observation"
                  />
                </div>
              </div>
              <h5 class="mt-5">Endereço</h5>
              <hr />
              <div class="row">
                <div class="form-group col-2">
                  <label for="postcode">CEP</label>
                  <input
                    v-model="newProvider.postcode"
                    @keyup="handleViacep"
                    v-mask="'#####-###'"
                    type="text"
                    class="form-control"
                    name="postcode"
                    id="postcode"
                  />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-9">
                  <label for="street">Rua/Av.</label>
                  <input
                    type="text"
                    v-model="newProvider.street"
                    class="form-control"
                    name="street"
                    id="street"
                  />
                </div>
                <div class="form-group col-md-3">
                  <label for="number">Numero</label>
                  <input
                    type="text"
                    v-model="newProvider.number"
                    class="form-control"
                    name="number"
                    id="number"
                  />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-6">
                  <label for="neighborhood">Bairro</label>
                  <input
                    type="text"
                    v-model="newProvider.neighborhood"
                    class="form-control"
                    name="neighborhood"
                    id="neighborhood"
                  />
                </div>
                <div class="form-group col-6">
                  <label for="complement">Complemento (Opcional)</label>
                  <input
                    type="text"
                    v-model="newProvider.complement"
                    class="form-control"
                    name="complement"
                    id="complement"
                  />
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-2">
                  <label for="state">Estado</label>
                  <select
                    name="state"
                    v-model="newProvider.state"
                    id="state"
                    class="form-control"
                  >
                    <option value="">Selecione</option>
                    <option
                      :value="item.uf"
                      v-for="(item, i) of states"
                      :key="i"
                      v-html="item.nome"
                    ></option>
                  </select>
                </div>
                <div class="form-group col-md-10">
                  <label for="city">Cidade</label>
                  <input
                    type="text"
                    v-model="newProvider.city"
                    class="form-control"
                    name="city"
                    id="city"
                  />
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <hr />
            <div
              class="d-flex flex-row justify-content-between aling-items-center"
            >
              <div class="form-check ml-5 mt-1">
                <input
                  checked
                  type="checkbox"
                  class="form-check-input"
                  id="is_active"
                  name="is_active"
                />
                <label class="form-check-label" for="is_active">Ativo</label>
              </div>
              <div class="card-footer bg-white">
                <a href="/suppliers" class="btn btn-danger">Cancelar</a>
                <button
                  @click.prevent="sendSupplier"
                  class="btn btn-primary px-4"
                >
                  Confirmar
                </button>
              </div>
            </div>
          </form>
          <!-- /.card -->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import toastr from "toastr";
import "toastr/build/toastr.min.css";

import { mask } from "vue-the-mask";
import { isValidEmail, isValidCNPJ } from "@brazilian-utils/brazilian-utils";
import { getStates } from "../helpers/state";

import { getById } from "../../services/supplier";

const defaultProvider = {
  postcode: "",
  street: "",
  uf: "",
  number: "",
  city: "",
  neighborhood: "",
  name: "",
  is_active: 1,
  phone: "",
  cellphone: "",
  cnpj: "",
  complement: "",
};

export default {
  name: "SupplierForm",
  directives: { mask },

  props: ["supplierId"],
  data() {
    return {
      newProvider: {
        postcode: "",
        street: "",
        state: "",
        number: "",
        city: "",
        neighborhood: "",
        name: "",
        is_active: 1,
        phone: "",
        cellphone: "",
        cnpj: "",
        complement: "",
      },
    };
  },

  computed: {
    cnpjIsValid() {
      if (!this.newProvider.cnpj) return true;
      if (this.newProvider.cnpj.length < 18) {
        return true;
      }
      if (!isValidCNPJ(this.newProvider.cnpj)) {
        toastr.clear();
        toastr.error("CNPJ inválido");
        return false;
      }
      return true;
    },
    emailIsValid() {
      if (!this.newProvider.email) return true;
      return isValidEmail(this.newProvider.email);
    },
    states() {
      return getStates();
    },
  },

  mounted() {
    if (this.supplierId != 0) {
      getById(this.supplierId).then((data) => {
        this.newProvider = data;
        if (data.address) {
          this.newProvider.postcode = data.address.postcode;
          this.newProvider.city = data.address.city;
          this.newProvider.state = data.address.state;
          this.newProvider.neighborhood = data.address.neighborhood;
          this.newProvider.number = data.address.number;
          this.newProvider.street = data.address.street;
          this.newProvider.complement = data.address.complement;
        }
        if (data.photo_url) {
          this.$refs.photo.src = "/storage/" + data.photo_url;
        }
      });
    }
    $("#photo").on("change", this.previewFile);
  },

  methods: {
    async handleViacep() {
      const postcode = this.newProvider.postcode.replace(/\D/g, "");

      if (postcode.length === 8) {
        this.newProvider.street = "";
        this.newProvider.neighborhood = "";
        this.newProvider.city = "";
        this.newProvider.state = "";

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
        this.newProvider.street = data.logradouro;
        this.newProvider.neighborhood = data.bairro;
        this.newProvider.city = data.localidade;
        this.newProvider.state = data.uf;
        this.newProvider.complement = data.complemento;
      }
    },

    sendSupplier() {
      let data = new FormData();
      if (this.newProvider.photo) {
        data.append("photo_url", this.newProvider.photo);
      }

      for (let key of Object.keys(this.newProvider)) {
        if(this.newProvider[key]){
          data.append(key, this.newProvider[key]);
        }
      }

      let url = "/suppliers",
        method = "POST";
      if (this.supplierId == 0) {
        method = "POST";
      } else {
        data.set('_method', 'PUT');
        url += "/" + this.supplierId;
        this.newProvider.id = this.supplierId;
      }
      toastr.clear();
      if (!this.cnpjIsValid) {
        toastr.error("Digite um CNPJ válido");
        return;
      }
      axios({
        method: method,
        url: url,
        data: data,
        headers: {
          "Content-Type": `multipart/form-data; boundary=${data._boundary}`,
        },
      })
        .then(({ data }) => {
          if (this.supplierId == 0) {
            toastr.success("Fornecedor cadastrado com sucesso.");
            this.newProvider = defaultProvider;
          } else {
            toastr.success("Fornecedor atualizado com sucesso.");
          }
          setTimeout(() => {
              window.location="/suppliers";
            }, 1000);
        })
        .catch((error) => {
          const errors = error.response.data.errors;
          let erro = "<ul>";
          for (let err of Object.keys(errors)) {
            erro += `<li>${errors[err][0]}</li>`;
          }
          erro += "</ul>";
          toastr.error(erro, "Erro");
        });
    },

    previewFile(e) {
      if (e.target.files && e.target.files[0]) {
        const supportedMime = ["image/jpeg", "image/png"];
        const { type } = e.target.files[0];
        const [image] = e.target.files;
        this.newProvider.photo = image;
        if (supportedMime.indexOf(type) > -1) {
          const reader = new FileReader();
          reader.readAsDataURL(e.target.files[0]);
          reader.onload = () => {
            this.$refs.photo.src = reader.result;
          };
        }
      } else {
        this.$refs.photo.src = "/images/default-avatar.png";
      }
    },
  },
};
</script>
<style>
a.btn-danger {
  color: white !important;
}
</style>