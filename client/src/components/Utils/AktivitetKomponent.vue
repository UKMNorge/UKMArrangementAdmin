<template>
    <div class="aktivitet-komponent">
        <div>
            <v-card>
                <v-card-text>
                    <!-- Chip Group to Replace Tabs -->
                    <v-chip-group v-model="tab" selected-class="text-white">
                        <v-chip
                            v-for="tidspunkt in aktivitet.tidspunkter"
                            :key="tidspunkt.id"
                            :value="tidspunkt.id"
                            color="primary"
                        >
                            {{ tidspunkt.getStartHuman() }}
                        </v-chip>
                    </v-chip-group>

                    <!-- Content Display for Selected Chip -->
                    <v-window v-model="tab">
                        <v-window-item v-for="tidspunkt in aktivitet.tidspunkter" :key="tidspunkt.id" :value="tidspunkt.id">
                            <v-card>
                                <v-card-title>{{ tidspunkt.getStartHuman() }}</v-card-title>
                                <v-card-text>
                                    <p>{{ tidspunkt.getStartHuman() }} - {{ tidspunkt.varighetMinutter }}</p>
                                    <p>Maks antall: {{ tidspunkt.maksAntall }}</p>
                                    <p>HendelseId: {{ tidspunkt.hendelseId }}</p>
                                    <div class="deltakere">
                                        <h5>Deltakere: </h5>
                                        <p v-for="deltaker in tidspunkt.deltakere">
                                            {{  deltaker.mobil }}
                                        </p>
                                    </div>
                                </v-card-text>
                            </v-card>
                        </v-window-item>
                    </v-window>
                </v-card-text>
            </v-card>
        </div>
    </div>
</template>

<script lang="ts">
import Aktivitet from '../../objects/Aktivitet';

export default {
    props: {
        aktivitet: {
            type: Aktivitet,
            required: true,
        },
    },
    data() {
        return {
            tab: null, // Set to null so no chip is selected by default
        };
    },
    mounted() {
        this.init();
    },
    methods: {
        init() {
            if (this.aktivitet.tidspunkter.length > 0) {
                this.tab = this.aktivitet.tidspunkter[0].id; // Select first item by default
            }
        },
    },
};
</script>

<style scoped>
.aktivitet-komponent {
    background-color: #fff;
    padding: 16px;
}
</style>
