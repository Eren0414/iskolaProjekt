<template>
  <div>
      <h2>Osztályok</h2>
      <!-- Táblázat -->

      <table class="table table-striped">
          <thead>
              <tr>
                  <th scope="col">Műveletek</th>
                  <th scope="col">Osztály</th>

              </tr>
          </thead>
          <tbody>
              <tr v-for="dataLine in collection" :key="dataLine.id" @click="onClickTr(dataLine.id)"
                  :class="{ 'table-success': selectedRowDataLineId == dataLine.id }">
                  <td class="text-nowrap">
                      <OperationsCrud :dataLine="dataLine" @onClickDeleteButton="onClickDeleteButton"
                          @onClickUpdate="onClickUpdate" @onClickCreate="onClickCreate" />
                  </td>
                  <td>{{ dataLine.osztalyNev }}</td>
              </tr>
          </tbody>
      </table>

      <!-- Modal -->
      <Modal :title="title" :yes="yes" :no="no" :size="size" @yesEvent="yesEventHandler">
          <div v-if="state == 'Delete'">
              {{ messageYesNo }}
          </div>

          <OsztalyForm v-if="state == 'Create' || state == 'Update'" :dataLine="dataLine"
              @saveDataLine="saveDataLineHandler" />
      </Modal>
  </div>
</template>

<script>
import Modal from "@/components/Modal.vue";
import OsztalyForm from "@/components/OsztalyForm.vue";
import OperationsCrud from "@/components/OperationsCrud.vue";
import axios from "axios";
import * as bootstrap from "bootstrap";

class DataLine {
  constructor(id = null, osztalyNev = null, nev = null, helyseg = null, osztondij = null, atlag = null) {
      this.id = id;
      this.osztalyNev = osztalyNev;
      this.nev = nev;
      this.helyseg = helyseg;
      this.osztondij = osztondij;
      this.atlag = atlag;
  }
}

export default {
  components: { OsztalyForm, OperationsCrud, Modal },
  mounted() {
      this.loadSports(); 
      this.modal = new bootstrap.Modal("#modal", {
          keyboard: false,
      });
  },
  data() {
      return {
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
      };
  },
  methods: {
      loadSports() {
          const token = localStorage.getItem('token');

          axios.get("http://localhost:8000/api/osztalies", {
              headers: {
                  'Content-Type': 'application/json',
                  'Accept': 'application/json',
                  'Authorization': `Bearer ${token}`,  
              }
          })
              .then(response => {
                  console.log("Válasz a backendről:", response);
                  this.collection = response.data.data; 
              })
              .catch(error => {
                  console.error("Hiba a sportok betöltésekor:", error);

              });
      },
      //rename
      deleteDataLineById() {
          this.collection = this.collection.filter(
              (p) => p.id != this.selectedRowDataLineId
          );
      },
      //rename
      createDataLine() {
          this.collection.push(this.dataLine);
          this.state = "Read";
      },
      //reaname
      updateDataLine() {
          const index = this.collection.findIndex((p) => p.id == this.dataLine.id);
          this.collection[index] = this.dataLine;
          this.state = "Read";
      },
      yesEventHandler() {
          if (this.state == "Delete") {
              this.deleteDataLineById();
              this.modal.hide();
          }
      },
      onClickDeleteButton(dataLine) {
          this.title = "Törlés";
          this.messageYesNo = `Valóban törölniakarod? Név: ${dataLine.sportNev}`;
          this.yes = "Igen";
          this.no = "Nem";
          this.size = null;
          this.state = "Delete";
      },
      onClickUpdate(dataLine) {
          this.state = "Update";
          this.title = "Foglalkozás módosítása";
          this.yes = null;
          this.no = "Mégsem";
          this.size = "lg";
          // this.person = person;
          this.dataLine = { ...dataLine };
      },
      onClickCreate() {
          this.title = "Új Foglalkozás létrehozása";
          this.yes = null;
          this.no = "Mégsem";
          this.size = "lg";

          this.state = "Create";
          this.dataLine = new DataLine(this.uniqid());
      },
      onClickTr(id) {
          this.selectedRowDataLineId = id;
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

      uniqid(length = 10) {
          const characters =
              "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
          let result = "";
          for (let i = 0; i < length; i++) {
              const randomIndex = Math.floor(Math.random() * characters.length);
              result += characters.charAt(randomIndex);
          }
          return result;
      },
  },
  computed: {
      // collection(){
      //   //rename
      //   return this.professions
      // }
  },
};
</script>

<style></style>
