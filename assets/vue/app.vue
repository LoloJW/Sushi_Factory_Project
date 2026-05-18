<template>
    <h1 :class="{'blurred':modaleVisible, 'blurred':modaleReservationVisible}" class="character_size_title">Veuillez choisir votre salle et votre créneau :</h1>
    <div>
        <table :class="{'blurred':modaleVisible, 'blurred':modaleReservationVisible}" class="table table-sm table-light table-bordered align-middle">
            <thead>
                <tr class="first_line">
                    <th class="salle_column_reservation">
                        <div class="d-none d-lg-block">Salle</div>
                        <div class="d-block d-lg-none character_size">S.</div>
                    </th>
                    <th v-for="heure in heures" :key="heure" class="d-lg-table-cell d-none"><div>{{ heure }}:00</div></th>
                    <th v-for="heure in heures" :key="heure" class=" character_size d-table-cell d-lg-none"><div>{{ heure }}.</div></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="salle in salles" :key="salle.id">
                    <th class="p-0 p-lg-2">
                        <div class="justify-content-between align-items-center h-auto d-none d-lg-flex">
                            <div>N: {{ salle.roomNumber }}</div>
                            <div>
                                <div>Taille: {{ salle.capacity }} places</div>
                                <div class="d-flex justify-content-evenly">
                                    <span class="" v-if="salle.projector"><i class="fa-solid fa-video"></i></span>
                                    <span class="" v-if="salle.whiteboard"><i class="fa-solid fa-chalkboard"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center align-items-center character_size d-lg-none">
                            <div>N:{{ salle.roomNumber }}</div>
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <div>C.{{ salle.capacity }}</div>
                                <div class="" v-if="salle.projector"><i class="fa-solid fa-video"></i></div>
                                <div class="" v-if="salle.whiteboard"><i class="fa-solid fa-chalkboard"></i></div>
                            </div>
                        </div>
                    </th>
                    <td v-for="Cell in getCells(salle.id)" 
                    :key="Cell.heure" 
                    :class="Cell.reservation ? `reserved-${Cell.reservation.type}` : ''" 
                    :colspan="Cell.colspan" 
                    @click="Cell.reservation ? ouvrirModaleReservation(Cell.reservation) : ouvrirModale(salle.roomNumber, salle.id, Cell.heure)">
                        <div v-if="Cell.reservation">
                            <div class="text-secondary">
                                <p class="m-auto">Réunion de "{{ Cell.reservation.name }}", de {{ Cell.reservation.timeStart }} a {{ Cell.reservation.timeEnd }}</p>
                                <p class="m-auto">Fait par {{ Cell.reservation.firstName }}{{ Cell.reservation.lastName }} , {{ Cell.reservation.type }}</p>
                            </div>
                        </div>
                        <div v-else>
                            <p style="color: #4dff8b84;" class="fst-italic m-auto text-center">Libre</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div v-if="modaleVisible" class="modale">
        <div class="modale-content shadow-sm border border-secondary">
            <div class="d-flex justify-content-between align-items-center pt-3 px-3">
                <h2 class="fs-5 fw-bold"><i class="fa-solid fa-calendar-days" style="color: #00bba7;"></i> Nouvelle réservation</h2>
                <button class="btn btn-close" @click="fermerModale()"></button>
            </div>
            <hr>
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 col-12 bg-light p-3 rounded-5 d-flex flex-column justify-content-between h-100 label_modale">
                        <div class="mb-lg-2">
                            <label for="salle">Salle</label>
                            <input type="text" id="salle" class="input_modale" v-model="salleSelected" readonly>
                        </div>
                        <div class="row mb-lg-2">
                            <div class="col-6">
                                <label for="début">Début</label>
                                <input type="text" id="début" class="input_modale" v-model="heureSelectedVisible" readonly>
                            </div>
                            <div class="col-6">
                                <label for="fin">Fin</label>
                                <select class="input_modale" v-model="heureFinSelected">
                                    <option v-for="heure in heuresFin" :key="heure" :value="heure">
                                        {{ heure }}:00
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-lg-2">
                            <label for="titre">Titre de la réunion</label>
                            <input type="text" id="titre" class="input_modale" v-model="nomDeRéunion">
                        </div>
                        <div class="mb-lg-2">
                            <label for="titre">Catégorie</label>
                            <select class="input_modale" v-model="typeDeRéunion">
                                <option v-for="type in types" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </option>
                            </select>
                        </div>
                        <!-- <div class="mt-3 d-flex justify-content-center align-items-center" @click="ajouter_un_equipement()" v-if="counter_equipment.length < 3">
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
                        </div> -->
                        <div class="label_modale d-flex justify-content-center align-items-center mt-2 mt-lg-0">
                            <button class="btn btn-white" @click="fermerModale()">Annuler</button>
                            <button class="btn btn-success" @click="validerReservation()">Valider</button>
                        </div>
                    </div>
                    <div class="col-lg-5 col-12 border border-light invites-list p-3 rounded-5">
                        <div class="row">
                            <div class="col-4 d-flex flex-column justify-content-center align-items-center profile_name_size mb-2" 
                            v-for="utilisateur in utilisateurs" :key="utilisateur.id"
                            @click="ajouterUtilisateur(utilisateur)">
                                <div class="w-75" :class="invités.some(i => i.id === utilisateur.id) ? 'bg-success rounded-3' : ''">
                                    <div class="w-100 d-flex justify-content-center align-items-center">
                                        <div class="box_img_profile_reservation"><img :src="avatars" alt="Profile" class="img_profile_reservation"></div>
                                    </div>
                                    <span class="d-block text-center">{{ utilisateur.firstName }}</span> <span class="d-block text-center">{{ utilisateur.lastName }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
    <div v-if="modaleReservationVisible" class="modale">
        <div>
            dazdazdazddaz
        </div>
        <div @click="fermerModaleReservation()">
            {{ Cell.reservation.name }}
            {{ Cell.reservation.timeStart }}
            {{ Cell.reservation.timeEnd }}
            {{ Cell.reservation.type }}
            {{ Cell.reservation.user }}
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const container = document.querySelector('#vue-app');

const avatars = container.dataset.avatars;
const rooms = JSON.parse(container.dataset.rooms);
const reservations = JSON.parse(container.dataset.reservationsrooms);
const types = JSON.parse(container.dataset.reservationtypes);
const users = JSON.parse(container.dataset.users);
const csrf = container.dataset.csrf;

const salles = ref(rooms);
const salleSelected = ref(null);
const salleSelectedId = ref(null);

const heures = ref([8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22]);
const heureSelected = ref(null);
const heureSelectedVisible = ref(null);
const heuresFin = computed(() => {  //Calcule l'heure de fin dans le select en fonction de l'heure de début choisi. Computed repère le changement de chaque ref et recalcule l'ensemble.
    const heuresDeFinListe = [];
    let counter = heureSelected.value + 1;
    while(counter <= 23) {
        heuresDeFinListe.push(counter);
        counter++;
    }
    return heuresDeFinListe;
});
const heureFinSelected = ref(null);

const reservationsRooms = ref(reservations);
const nomDeRéunion = ref(null);
const typeDeRéunion = ref(null);

const utilisateurs = ref(users);
const invités = ref([]);


const modaleVisible = ref(false);
const modaleReservationVisible = ref(false);

function getReservationData(salleId, heure) {
    return reservations.find(reservation => reservation.roomId === salleId && 
    parseInt(reservation.timeStart) <= heure && 
    parseInt(reservation.timeEnd)>heure);
}
function getCells(salleId) {
    const cells = [];
    let heure = 8;
    
    while (heure <= 22) {
    const reservation = getReservationData(salleId, heure);

        if (reservation) {
                const duration = parseInt(reservation.timeEnd) - parseInt(reservation.timeStart);

                cells.push({ heure, colspan: duration, reservation });
                heure += duration;
            }
        else {
                cells.push({ heure, colspan: 1, reservation: null });
                heure++;
            }
        }
    return cells;
}
function ouvrirModale(salle,salleId, heure) {
    salleSelected.value = salle
    heureSelected.value = heure
    salleSelectedId.value = salleId
    heureSelectedVisible.value = heure + ":00"
    modaleVisible.value = true
    document.querySelector('header').classList.add('darkened_blurred');
    document.querySelector('footer').classList.add('blurred');
}
function ouvrirModaleReservation(Cell) {
    modaleReservationVisible.value = true
    document.querySelector('header').classList.add('darkened_blurred');
    document.querySelector('footer').classList.add('blurred');
}
function ajouterUtilisateur(utilisateur){
    const checkUser = invités.value.find(i => i.id === utilisateur.id);
    if (!checkUser) {
        invités.value.push(utilisateur);
    }
    else {
        invités.value = invités.value.filter
        (i => i.id !== utilisateur.id);
    }
}
function fermerModale() {
    modaleVisible.value = false;
    heureFinSelected.value = null;
    invités.value = [];
    document.querySelector('header').classList.remove('darkened_blurred');
    document.querySelector('footer').classList.remove('blurred');
}
function fermerModaleReservation() {
    modaleReservationVisible.value = false;
    document.querySelector('header').classList.remove('darkened_blurred');
    document.querySelector('footer').classList.remove('blurred');
}
async function validerReservation() {
    if (!heureSelected.value || 
    !heureFinSelected.value || 
    !nomDeRéunion.value || 
    !typeDeRéunion.value ||
    !salleSelected.value ||
    !salleSelectedId.value) {
        return alert('Veuillez remplir tous les champs.');
    }
    else {
            const requete = await fetch('/reservation/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'CSRF-Token': csrf
                },
                body: JSON.stringify({
                    roomId: salleSelectedId.value,
                    timeStart: heureSelected.value + ':00',
                    timeEnd: heureFinSelected.value + ':00',
                    type: typeDeRéunion.value,
                    name: nomDeRéunion.value,
                    invitedUsers: invités.value.map(i => i.id)
                })
            });
            if(requete.ok){
                fermerModale();
                window.location.reload();
            }
            else{
                const error = await requete.json();
                alert(error.error);
            }
        }
}
</script>