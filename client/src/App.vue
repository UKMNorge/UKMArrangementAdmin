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
                <v-tab text="Kontaktpersoner"></v-tab>
                <v-tab text="Kvoter"></v-tab>
            </v-tabs>
        </div>
        <div class="as-container">
            <div class="container">        
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
                                <h1>Komponent4</h1>
                            </div>
                        </v-tabs-window-item>

                        <!--  -->
                        <v-tabs-window-item v-if="arrangementLoaded">
                            <div class="as-containercontainer">
                                <h1>Komponent5</h1>
                            </div>
                        </v-tabs-window-item>
                        
                        <!--  -->
                        <v-tabs-window-item v-if="arrangementLoaded">
                            <div class="as-containercontainer">
                                <h1>Komponent6</h1>
                            </div>
                        </v-tabs-window-item>

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
// import KommuneStatistikk from './components/KommuneStatistikk.vue';
// import FylkeStatistikk from './components/FylkeStatistikk.vue';
// import GenerellStatistikk from './components/GenerellStatistikk.vue';

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
        // FylkeStatistikk : FylkeStatistikk,
        // GenerellStatistikk : GenerellStatistikk,
    },
    

    mounted: function () {
        this.initArrangement();
    },
    created() {
        const router = useRouter();
        const route = useRoute();
        this.tab = route.query.tab || '1';
        
        watch(() => this.tab, (newTab) => {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', newTab);
            window.history.pushState({}, '', url.toString());
        });
    
        onMounted(() => {
            const url = new URL(window.location.href);
            this.tab = url.searchParams.get('tab') || '1';
        });
    },
    
    methods: {
        async initArrangement() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'getArrangement',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            if(results != null) {
                let status = results.status == 'videresending_kanskje' ? 1 : (results.status == 'videresending_sikkert' ? 2 : 0);

                this.arrangement = new Arrangement(
                    results.id,
                    results.name,
                    results.place,
                    results.startDate,
                    results.endDate,
                    status,
                    results.antallDeltakere,
                    results.openPamelding,
                    results.openVideresending,
                );
            }else {
                this.spaInteraction.showMessage('Feil', 'Kunne ikke hente innstillinger', 'error');
            }

            console.log('this.arrangement');
            console.log(this.arrangement);
            this.arrangementLoaded = true;

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
