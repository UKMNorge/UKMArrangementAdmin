<template>
    <div class="as-container main-container">
        <div class="aktiviteter-buttons">
            <v-btn
                class="v-btn-as v-btn-hvit"
                prepend-icon="mdi-plus"
                color="#000"
                rounded="large"
                :size="isMobile ? 'large' : 'x-large'"
                @click="leggTilAktivitet"
                variant="outlined" >
                Legg til Aktivitet
            </v-btn>
        </div>
        <v-card class="mx-auto aktivitet-card">
            <v-list lines="three" class="aktivitet-list">
                <div class="">
                    <div class="as-card-1 as-padding-space-3 as-margin-bottom-space-2" v-for="aktivitet in aktiviteter" :key="aktivitet.id" v-show="!aktivitet.deleted">
                        <v-list-item v-if="!aktivitet.deleted"
                        :title="aktivitet.title"
                        :subtitle="getAntallTidspunkter(aktivitet)"
                        class="aktivitet-item nop-impt as-card-1 as-padding-space-3"
                        >
                        <template v-slot:prepend>
                            <v-avatar color="grey-lighten-1">
                            <v-icon color="white">mdi-calendar-star</v-icon>
                            </v-avatar>
                        </template>

                        <template v-slot:append>
                            <v-btn 
                            icon 
                            variant="text" 
                            @click="toggleExpand(aktivitet)"
                            >
                                <v-icon>{{ aktivitet.expanded ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                            </v-btn>
                        </template>
                        </v-list-item>

                        <!-- Expandable Content BELOW the item -->
                        <v-expand-transition>
                            
                            <div v-if="aktivitet.expanded" class="as-display-flex">
                                <AktivitetKomponent :aktivitet="aktivitet" />
                            </div>
                        </v-expand-transition>
                    </div>
                </div>
            </v-list>
        </v-card>

    </div>
</template>

<script lang="ts">
import MainComponent from './MainComponent.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import type Arrangement from './../objects/Arrangement';
import type { PropType } from 'vue';  // Use type-only import for PropType
import Leder from './../objects/Leder';
import { PermanentNotification } from 'ukm-components-vue3';
import Aktivitet from './../objects/Aktivitet';
import AktivitetKomponent from './Utils/AktivitetKomponent.vue';
import AktivitetTidspunkt from './../objects/AktivitetTidspunkt';
import AktivitetDeltaker from './../objects/AktivitetDeltaker';
import { InputTextOverlay } from 'ukm-components-vue3';


export default {
    computed: {
        isMobile() {
            return window.innerWidth < 576; // Adjust the breakpoint as needed
        },
    },
    props: {
        arrangement: {
            type: Object as PropType<Arrangement>,
            required: true
        },
    },
    components: {
        VueDatePicker : VueDatePicker,
        PermanentNotification : PermanentNotification,
        AktivitetKomponent : AktivitetKomponent,
        InputTextOverlay : InputTextOverlay,

    },
    mounted() {
        this.fetch();
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            savingOngoing: false,
            aktiviteter : [] as Aktivitet[],
        }
    },
    methods : {
        getAntallTidspunkter(aktivitet : Aktivitet) {
            return 'Antall forekomster: ' + (aktivitet.tidspunkter.length-1);  
        },
        toggleExpand(aktivitet : Aktivitet) {
            aktivitet.expanded = !aktivitet.expanded;
        },
        leggTilAktivitet() {
            // Check if there is already an empty aktivitet
            for(let aktivitet of this.aktiviteter) {
                if(aktivitet.id == -1) {
                    aktivitet.expanded = true;
                    return;
                }
            }
            
            let newAktivitet = new Aktivitet(
                -1, 
                '', 
                '', 
                '',
                -1,
                []
            );
            
            this.aktiviteter.unshift(newAktivitet);
            newAktivitet.expanded = true;
        },
        async fetch() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'aktivitet/getAlleAktiviteter',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
            
            for(let aktivitet of results) {
                let tidspunkter : AktivitetTidspunkt[] = [];

                let aktivitetObj = new Aktivitet(
                    aktivitet.id, 
                    aktivitet.navn, 
                    aktivitet.sted, 
                    aktivitet.beskrivelse,
                    aktivitet.plId,
                    tidspunkter
                );
                this.aktiviteter.push(
                    aktivitetObj
                );
            
                for(let tidspunkt of aktivitet.tidspunkter) {
                    let deltakere : AktivitetDeltaker[] = [];

                    for(let deltaker of tidspunkt.deltakere) {
                        deltakere.push(
                            new AktivitetDeltaker(
                                deltaker.mobil,
                            )
                        );
                    }
                    
                    tidspunkter.push(
                        new AktivitetTidspunkt(
                            tidspunkt.id,
                            tidspunkt.sted,
                            tidspunkt.start,
                            tidspunkt.slutt,
                            tidspunkt.varighet,
                            tidspunkt.maksAntall,
                            tidspunkt.hendelseId,
                            aktivitetObj,
                            deltakere,
                            tidspunkt.harPaamelding,
                            tidspunkt.erSammeStedSomAktivitet)
                    );

                    console.log(tidspunkter);
                }
            }
            


        },
        async save(leder : Leder) {
            this.savingOngoing = true;
        
            let res;
            try {
                res = await leder.save();
            } catch(e) {
                this.spaInteraction.showMessage('Feil', 'En feil oppstod under lagring av godkjenning', 'error');
            }

            if(res.success == true) {
                this.spaInteraction.showMessage('Lagret', 'Godkjenningsstatus er lagret', 'success');
            } else {
                this.spaInteraction.showMessage('Feil', 'En feil oppstod under lagring av godkjenning', 'error');
            }

     
            this.savingOngoing = false;
        },
    }
}
</script>

<style scoped>
.aktivitet-list,
.aktivitet-card {
    background: transparent;
    box-shadow: none !important;
}
.aktivitet-item {
    border: none;
    box-shadow: none !important;
    min-height: 56px !important;
}
.aktivitet-list,
.aktiviteter-buttons {
    padding: var(--initial-space-box) !important;
}
@media(max-width: 767px) {

}

</style>