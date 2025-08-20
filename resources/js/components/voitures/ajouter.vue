<template>
    <link>
    <div class="max-w-md mx-auto p-6 bg-white rounded-xl shadow-md">
        <h2 class="text-xl font-bold mb-4">Ajouter une Voiture</h2>

        <form @submit.prevent="submitForm">
            <div class="mb-3">
                <label class="block mb-1 font-medium">Marque</label>
                <input v-model="form.marque" type="text" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-3">
                <label class="block mb-1 font-medium">Modèle</label>
                <input v-model="form.modele" type="text" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-3">
                <label class="block mb-1 font-medium">Année</label>
                <input v-model="form.annee" type="number" min="1900" max="2099" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-3">
                <label class="block mb-1 font-medium">Kilométrage</label>
                <input v-model="form.kilometrage" type="number" step="1" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-3">
                <label class="block mb-1 font-medium">État</label>
                <input v-model="form.etat" type="text" class="w-full border rounded px-3 py-2" placeholder="Bon état">
            </div>

            <div class="mb-3">
                <label class="block mb-1 font-medium">Statut</label>
                <select v-model="form.statut" class="w-full border rounded px-3 py-2" required>
                <option value="En boutique">En boutique</option>
                <option value="En location">En location</option>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                Ajouter
            </button>
        </form>

        <div v-if="message" class="mt-4 p-2 bg-green-100 text-green-800 rounded">
        {{ message }}
        </div>
    </div>
</template>

<script>
    import axios from "axios";

    export default {
        name: "VoitureForm",
        data() {
            return {
                form: {
                    marque: "",
                    modele: "",
                    annee: "",
                    kilometrage: "",
                    etat: "",
                    statut: "En boutique",
                    user_id: 1, // TODO: change this later to the logged-in user
                },
                message: ""
            };
        },
        methods: {
            async submitForm() {
                try {
                    const res = await axios.post("/api/voitures", this.form);
                    this.message = "Voiture ajoutée avec succès ✅";
                    this.form = { marque:"", modele:"", annee:"", kilometrage:"", etat:"", statut:"En boutique", user_id: 1 };
                } catch (err) {
                    console.error(err);
                    this.message = "Erreur lors de l'ajout ❌";
                }
            }
        }
    };
</script>
