<template>
  <div>
    <h2>Sportok</h2>
    <!-- Hibaüzenet -->
    <div
      class="alert alert-danger alert-dismissible fade show"
      role="alert"
      v-if="errorMessage"
    >
      {{ errorMessage }}
      <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="Close"
        @click="onClickCloseErrorMessage()"
      ></button>
    </div>
    <!-- Táblázat -->

    <table class="table table-striped">
      <thead>
        <tr>
          <!-- Módosítás -->
          <th scope="col">Műveletek</th>
          <th scope="col">Sportok</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="dataLine in collection"
          :key="dataLine.id"
          @click="onClickTr(dataLine.id)"
          :class="{ 'table-success': selectedRowDataLineId == dataLine.id }"
        >
          <td class="text-nowrap">
            <OperationsCrud
              :dataLine="dataLine"
              @onClickDeleteButton="onClickDeleteButton"
              @onClickUpdate="onClickUpdate"
              @onClickCreate="onClickCreate"
            />
          </td>
          <!-- Módosítás -->
          <td>{{ dataLine.sportNev }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Modal -->
    <Modal
      :title="title"
      :yes="yes"
      :no="no"
      :size="size"
      @yesEvent="yesEventHandler"
    >
      <div v-if="state == 'Delete'">
        {{ messageYesNo }}
      </div>

      <DataForm
        v-if="state == 'Create' || state == 'Update'"
        :dataLine="dataLine"
        @saveDataLine="saveDataLineHandler"
      />
    </Modal>
  </div>
</template>

<script>
import Modal from "@/components/Modal.vue";
// Módosítás
import DataForm from "@/components/SportsForm.vue";
import OperationsCrud from "@/components/OperationsCrud.vue";
import axios from "axios";
import * as bootstrap from "bootstrap";
import { BASE_URL } from "@/helpers/baseUrls";
import { useAuthStore } from "@/stores/useAuthStore";

// Módosítás
class DataLine {
  constructor(id = null, sportNev = null) {
    this.id = id;
    this.sportNev = sportNev;
  }
}

export default {
  components: { DataForm, OperationsCrud, Modal },
  mounted() {
    this.loadItems();
    this.modal = new bootstrap.Modal("#modal", {
      keyboard: false,
    });
  },
  data() {
    return {
      // Módosítás
      urlApi: `${BASE_URL}/sports`,
      stateAuth: useAuthStore(),
      modal: null,
      dataLine: new DataLine(),
      selectedRowDataLineId: null,
      messageYesNo: null,
      state: "Read",
      title: null,
      yes: null,
      no: null,
      size: null,
      collection: [],
      errorMessage: null,
    };
  },
  methods: {
    async loadItems() {
      const token = this.stateAuth.token;
      console.log(token);
      const url = this.urlApi;
      const headers = {
        "Content-Type": "application/json",
        Accept: "application/json",
        Authorization: `Bearer ${token}`,
      };
      try {
        const response = await axios.get(url, {
          headers: headers,
        });
        this.collection = response.data.data;
      } catch (error) {
        this.errorMessage = "Hiba az adatok betöltésekor";
      }
    },
    async deleteDataLineById() {
      const token = this.stateAuth.token;
      const url = `${this.urlApi}/${this.selectedRowDataLineId}`;
      const headers = {
        "Content-Type": "application/json",
        Accept: "application/json",
        Authorization: `Bearer ${token}`,
      };
      console.log("deleteDataLineById", headers);

      try {
        const response = await axios.delete(url, {
          headers: headers,
        });
        this.loadItems();
      } catch (error) {
        this.errorMessage = "Hiba az adatok betöltésekor";
      }
    },
    async createDataLine() {
      const token = this.stateAuth.token;
      if (!token) {
        this.errorMessage = "Hiányzik a hitelesítési token.";
        return;
      }
      const url = `${this.urlApi}`;
      const headers = {
        "Content-Type": "application/json",
        Accept: "application/json",
        Authorization: `Bearer ${token}`,
      };
      console.log("createDataLine", headers);

      try {
        const response = await axios.post(url, this.dataLine, {
          headers: headers,
        });
        this.loadItems();
      } catch (error) {
        console.error("Hiba:", error.response);
        this.errorMessage =
          error.response?.data?.message || "Hiba az adatok betöltésekor.";
      }
    },
    async updateDataLine() {
      const token = this.stateAuth.token;
      if (!token) {
        this.errorMessage = "Hiányzik a hitelesítési token.";
        return;
      }

      const url = `${this.urlApi}/${this.dataLine.id}`;
      const headers = {
        "Content-Type": "application/json",
        Accept: "application/json",
        Authorization: `Bearer ${token}`,
      };
      console.log("updateDataLine", headers);

      try {
        const response = await axios.patch(url, this.dataLine, {
          headers: headers,
        });
        this.loadItems(); 
        this.state = "Read";
      } catch (error) {
        console.error("Hiba:", error.response);
        this.errorMessage =
          error.response?.data?.message || "Hiba az adatok módosítása közben.";
      }
    },
    yesEventHandler() {
      if (this.state == "Delete") {
        this.deleteDataLineById();
        this.modal.hide();
      }
    },
    onClickDeleteButton(dataLine) {
      this.title = "Törlés";
      // Módosítás
      this.messageYesNo = `Valóban törölniakarod? Név: ${dataLine.sportNev}`;
      this.yes = "Igen";
      this.no = "Nem";
      this.size = null;
      this.state = "Delete";
    },
    onClickUpdate(dataLine) {
      this.state = "Update";
      this.title = "Adat módosítása";
      this.yes = null;
      this.no = "Mégsem";
      this.size = "lg";
      // this.person = person;
      this.dataLine = { ...dataLine };
    },
    onClickCreate() {
      this.title = "Új Adat létrehozása";
      this.yes = null;
      this.no = "Mégsem";
      this.size = "lg";

      this.state = "Create";
      this.dataLine = new DataLine();
    },
    onClickTr(id) {
      this.selectedRowDataLineId = id;
    },
    onClickCloseErrorMessage() {
      this.errorMessage = null;
    },

    saveDataLineHandler(dataLine) {
      this.dataLine = dataLine;
      this.modal.hide();
      if (this.state == "Create") {
        this.createDataLine();
      } else if (this.state == "Update") {
        this.updateDataLine();
      }
    },
  },
  computed: {},
};
</script>

<style></style>
