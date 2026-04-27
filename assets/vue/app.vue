
<template>
    <div v-if="modalVisible" class="overlay"></div>
    
    <h1 :class="{'blurred':modalVisible}">Veuillez choisir votre salle et votre créneau :</h1>
    <div :class="{'blurred':modalVisible}">
        <table class="table table-light table-bordered align-middle">
            <thead>
                <tr class="first_line">
                    <th scope="">Salle</th>
                        <th v-for="heure in heures" :key="heure" class="text-center">{{ heure }}:00</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="salle in salles" :key="salle">
                    <th class="first_column">{{ salle }}</th>
                    <td v-for="heure in heures" :key="heure" @click="ouvrirModale(salle, heure)" class="text-center"></td>
                </tr>
            </tbody>
        </table>
        <div>
            <a href="">Précédent</a>
            <span>Page 6/            </span>
            <a href="">Suivant</a>
        </div>
    </div>
    <div v-if="modalVisible" class="modale">
        <div class="modale-content shadow-sm border border-secondary">
            <div class="d-flex justify-content-between align-items-center pt-3 px-3">
                <h2 class="fs-5 fw-bold"><i class="fa-solid fa-calendar-days" style="color: #00bba7;"></i> Nouvelle réservation</h2>
                <button class="btn btn-close" @click="fermerModale()"></button>
            </div>
            <hr>
            <div class="container h-100">
                <div class="row h-100">
                    <div class="col-lg-7 col-12 bg-light p-3 rounded-5 d-flex flex-column justify-content-between h-100">
                        <div class="mb-2">
                            <label for="salle">Salle</label>
                            <input type="text" id="salle" class="input_modale" v-model="SelectedRoom" readonly>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <label for="début">Début</label>
                                <input type="text" id="début" class="input_modale" v-model="SelectedHour" readonly>
                            </div>
                            <div class="col-6">
                                <label for="fin">Fin</label>
                                <input type="text" id="fin" class="input_modale">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="titre">Titre de la réunion</label>
                            <input type="text" id="titre" class="input_modale">
                        </div>
                        <div class="mb-2">
                            <label for="titre">Catégorie</label>
                            <input type="text" id="titre" class="input_modale">
                        </div>
                        <!-- Si le counter est à 3 je voudrais que ça disparaisse -->
                        <div class="mt-3 d-flex justify-content-center align-items-center" @click="ajouter_un_equipement()" v-if="counter_equipment.length < 3">
                            <button class="btn btn-primary">Ajouter un équipement</button>
                        </div>
                        <div class="mt-3 row box_equipement" v-for="(equipment, i) in counter_equipment" :key="i">
                                <div class="col-7 d-flex justify-content-start align-items-center">
                                    <select name="equipement" id="equipement" class="input_modale text-center">
                                        <option value="equipement1">Equipement 1</option>
                                        <option value="equipement2">Equipement 2</option>
                                        <option value="equipement3">Equipement 3</option>
                                    </select>
                                </div>
                                <div class="col-5 d-flex justify-content-center align-items-center">
                                    <button class="input_modale btn_size_equipment" @click="décrémenter_un_equipement(i)"><i class="fa-solid fa-minus"></i></button>
                                    <input type="text" class="counter_equipment input_modale" readonly :value="equipment.quantity">
                                    <button class="input_modale btn_size_equipment" @click="equipment.quantity++"><i class="fa-solid fa-plus"></i></button>
                                </div>
                        </div>
                        <div class="mt-2 d-flex justify-content-center align-items-center">
                            <button class="btn btn-white" @click="fermerModale()">Annuler</button>
                            <button class="btn btn-success">Valider</button>
                        </div>
                    </div>
                    <div class="col-lg-5 col-12">
                            <div class="row">
                                <div class="col-4 d-flex flex-column justify-content-center align-items-center profile_name_size" v-for="collègue in collègues" :key="collègue.id">
                                    <div class="box_img_profile_reservation"><img :src="avatars" alt="Collègue" class="img_profile_reservation"></div>
                                    <span class="d-block">{{ collègue.firstName }}</span> <span class="d-block">{{ collègue.lastName }}</span>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>   
</template>

<script setup>
import { ref } from 'vue';
const container = document.querySelector('#vue-app');
const avatars = container.dataset.avatars;
const users = JSON.parse(container.dataset.users);
const collègues = ref(users);
const salles = ref(['Salle 1', 'Salle 2', 'Salle 3', 'Salle 4', 'Salle 5']);
const heures = ref([8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22]);

const modalVisible = ref(false);
const SelectedRoom = ref(null);
const SelectedHour = ref(null);

// Ouvrir la modale et assombrir le reste de la page
function ouvrirModale(salle, heure) {
    SelectedRoom.value = salle;
    SelectedHour.value = heure;
    modalVisible.value = true;
    document.querySelector('header').classList.add('darkened_blurred');
    document.querySelector('footer').classList.add('blurred');
}
// Fermer la modale et remettre l'apparence normale de la page
function fermerModale() {
    modalVisible.value = false;
    document.querySelector('header').classList.remove('darkened_blurred');
    document.querySelector('footer').classList.remove('blurred');
}

const counter_equipment = ref([]);

function ajouter_un_equipement() {
    if (counter_equipment.value.length < 3) {
        counter_equipment.value.push({ quantity: 1 });
    }
};
function décrémenter_un_equipement(index) {
    if (counter_equipment.value[index].quantity > 1) {
        counter_equipment.value[index].quantity--;
    }else {
        counter_equipment.value.splice(index, 1);
    }
    
}
</script>