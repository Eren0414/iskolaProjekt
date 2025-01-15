<template>
    <div>
        <h2>Sportok</h2>
        <!-- Táblázat -->

        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Műveletek</th>
                    <th scope="col">Sportok</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="dataLine in collection" :key="dataLine.id" @click="onClickTr(dataLine.id)"
                    :class="{ 'table-success': selectedRowDataLineId == dataLine.id }">
                    <td class="text-nowrap">
                        <OperationsCrud :dataLine="dataLine" @onClickDeleteButton="onClickDeleteButton"
                            @onClickUpdate="onClickUpdate" @onClickCreate="onClickCreate" />
                    </td>
                    <td>{{ dataLine.sportNev }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Modal -->
        <Modal :title="title" :yes="yes" :no="no" :size="size" @yesEvent="yesEventHandler">
            <div v-if="state == 'Delete'">
                {{ messageYesNo }}
            </div>

            <SportsForm v-if="state == 'Create' || state == 'Update'" :dataLine="dataLine"
                @saveDataLine="saveDataLineHandler" />
        </Modal>
    </div>
</template>

<script>
import Modal from "@/components/Modal.vue";
import SportsForm from "@/components/SportsForm.vue";
import OperationsCrud from "@/components/OperationsCrud.vue";
import axios from "axios";
import * as bootstrap from "bootstrap";

class DataLine {
    constructor(id = null, sportNev = null) {
        this.id = id;
        this.sportNev = sportNev;
    }
}

export default {
    components: { SportsForm, OperationsCrud, Modal },
    mounted() {
        this.loadSports(); // Betöltjük az adatokat a komponens betöltésekor
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
            state: "Read", // CRUD: Create, Read, Update, Delete
            title: null,
            yes: null,
            no: null,
            size: null,
            collection: [], // A sportok itt tárolódnak
        };
    },
    methods: {
        loadSports() {
            const token = localStorage.getItem('token');

            axios.get("http://localhost:8000/api/sports", {
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
