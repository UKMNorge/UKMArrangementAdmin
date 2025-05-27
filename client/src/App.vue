<template>
    <div class="as-margin-top-space-2">
        <div class="container-fluid">
            <v-tabs align-tabs="center"
                class="as-card-1 nosh-impt" 
                fixed-tabs
                bg-color="#fff"
                v-model="tab">
                <v-tab text="Festivalinfo"></v-tab>
                <v-tab text="Innstillinger"></v-tab>
                <v-tab text="Påmelding"></v-tab>
                <v-tab text="Ledere/sykerom"></v-tab>
                <v-tab text="Videresending oversikt"></v-tab>
                <v-tab text="Kvoter"></v-tab>
                <v-tab text="Aktiviteter"></v-tab>
                <v-tab text="Hendelser"></v-tab>
                <!-- <v-tab text="Kontaktpersoner"></v-tab> -->
            </v-tabs>
        </div>
        <div class="as-container">
            <div class="container arrangement-admin-container">        
                <div class="as-margin-top-space-4">
                    <v-tabs-window v-model="tab">
                        <!-- Kommunestatistikk -->
                        <v-tabs-window-item v-if="arrangementLoaded">
                            <div class="as-containercontainer">
                                <FestivalInfo :arrangement="arrangement" />
                            </div>
                        </v-tabs-window-item>
                        
                        <!-- Påmeldingssystem -->
                        <v-tabs-window-item v-if="arrangementLoaded">
                            <div class="as-containercontainer">
                                <Innstillinger :arrangement="arrangement" />
                            </div>
                        </v-tabs-window-item>
                        
                        <!-- Generell statistikk -->
                        <v-tabs-window-item v-if="arrangementLoaded">
                            <div class="as-containercontainer">
                                <Pamelding :arrangement="arrangement" />
                            </div>
                        </v-tabs-window-item>

                        <!--  -->
                        <v-tabs-window-item v-if="arrangementLoaded">
                            <div class="as-containercontainer">
                                <Ledere :arrangement="arrangement" />
                            </div>
                        </v-tabs-window-item>

                        <!--  -->
                        <v-tabs-window-item v-if="arrangementLoaded">
                            <div class="as-containercontainer">
                                <VideresendteArrangementer :arrangement="arrangement" />
                            </div>
                        </v-tabs-window-item>

                        <!--  -->
                        <v-tabs-window-item v-if="arrangementLoaded">
                            <div class="as-containercontainer">
                                <ArrangementKvoter :arrangement="arrangement" />
                            </div>
                        </v-tabs-window-item>

                        <!-- Aktiviteter -->
                        <v-tabs-window-item v-if="arrangementLoaded">
                            <div class="as-containercontainer">
                                <Aktiviteter :arrangement="arrangement" />
                            </div>
                        </v-tabs-window-item>
                        
                        <!-- Hendelser -->
                        <v-tabs-window-item v-if="arrangementLoaded">
                            <div class="as-containercontainer">
                                <Hendelser :arrangement="arrangement" />
                            </div>
                        </v-tabs-window-item>

                        <!--  -->
                        <!-- <v-tabs-window-item v-if="arrangementLoaded">
                            <div class="as-containercontainer">
                                <h1>Kontaktpersoner</h1>
                            </div>
                        </v-tabs-window-item> -->

                    </v-tabs-window>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";

import Arrangement from "./objects/Arrangement";
// Components
import FestivalInfo from "./components/FestivalInfo.vue";
import Innstillinger from "./components/Innstillinger.vue";
import Pamelding from "./components/Pamelding.vue";
import Ledere from "./components/Ledere.vue";
import Aktiviteter from "./components/Aktiviteter.vue";
import Hendelser from "./components/Hendelser.vue";
import VideresendteArrangementer from "./components/VideresendteArrangementer.vue";
import ArrangementKvoter from "./components/ArrangementKvoter.vue";
import { Director } from 'ukm-spa/Director';

// import KommuneStatistikk from './components/KommuneStatistikk.vue';
// import FylkeStatistikk from './components/FylkeStatistikk.vue';
// import GenerellStatistikk from './components/GenerellStatistikk.vue';

const director = new Director();

export default {

    data() {
        return {
            tab : null as any,
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            isFylkeAdmin : false as boolean,
            isSuperadmin : false as boolean,

            arrangement : Arrangement.createEmpty() as Arrangement,
            arrangementLoaded : false as boolean,
        }
    },

    components : {
        FestivalInfo : FestivalInfo,
        Innstillinger : Innstillinger,
        Pamelding : Pamelding,
        Ledere : Ledere,
        VideresendteArrangementer : VideresendteArrangementer,
        ArrangementKvoter : ArrangementKvoter,
        Aktiviteter : Aktiviteter,
        Hendelser : Hendelser,
        // FylkeStatistikk : FylkeStatistikk,
        // GenerellStatistikk : GenerellStatistikk,
    },
    

    mounted: function () {
        let tab = director.getParam('tab');

        if(tab) {
            this.openTab(tab);
        }
        else {
            this.openTab(0);
        }

        this.initArrangement();
        
        watch(() => this.tab, (newTab) => {
            this.openTab(newTab);
        });
    },    
    methods: {
        openTab(tabId: number) {
            director.addParam('tab', tabId);
            this.tab = tabId;
        },
        async initArrangement() {
            Arrangement.load().then((arrangement: Arrangement) => {
                this.arrangement = arrangement;
                this.arrangementLoaded = true;
            });

        },
        erFylkeAdmin() : boolean {
            for(let omradeItem of (<any>window).ukm_statistikk_klient.omrade) {
                if(omradeItem.type == 'fylke') {
                    return true;
                }
            }
            return false;
        },
        erSuperAdmin() : boolean {
            if((<any>window).ukm_statistikk_klient.is_superadmin) {
                return true;
            }
            return false;
        }
    }
}
</script>
<style scoped>
.arrangement-admin-container {
    padding: 0;
    max-width: 100%;
}
</style>