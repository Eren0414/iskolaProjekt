<template>
    <div>
        <p v-if="debug">{{ dataLine }}</p>
        <form class="row g-4 needs-validation was-validated" novalidate>
            <!-- Name -->
            <div class="col-md-8">
                <label for="name" class="form-label">Adat név:</label>
                <!-- Módosítás -->
                <input type="text" class="form-control" id="sportNev" required v-model="dataLine.osztalyNev" />
                <div class="valid-feedback">&nbsp;</div>
                <div class="invalid-feedback">Adat név kitöltése kötelező</div>
            </div>

            <button type="submit" class="btn btn-success">Mentés</button>
        </form>
    </div>
</template>

<script>
import { DEBUG } from "@/helpers/debug.js";
export default {
    data() {
        return {
            debug: DEBUG,
        }
    },
    props: ["dataLine"],
    emits: ["saveDataLine"],
    mounted() {
        const forms = document.querySelectorAll(".needs-validation");

        Array.from(forms).forEach((form) => {
            form.addEventListener(
                "submit",
                (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    if (form.checkValidity()) {
                        this.onClickSubmit();
                    }
                },
                false
            );
        });
    },
    methods: {
        onClickSubmit() {
            console.log("Mentés");
            this.$emit("saveDataLine", this.dataLine);
        },
    },
    computed: {

    },
};
</script>

<style></style>