<template>
    <div class="as-container main-container">

        <v-card class="mx-auto">
            <v-list lines="three">
                <template v-for="aktivitet in aktiviteter" :key="aktivitet.id">
                    <v-list-item
                    :subtitle="aktivitet.subtitle"
                    :title="aktivitet.title"
                    >
                    <template v-slot:prepend>
                        <v-avatar color="grey-lighten-1">
                        <v-icon color="white">mdi-folder</v-icon>
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
                        <div v-if="aktivitet.expanded" class="">
                            <AktivitetKomponent :aktivitet="aktivitet" />
                        </div>
                    </v-expand-transition>
                </template>
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

export default {
    extends : MainComponent,
    props: {
        arrangement: {
            type: Object as PropType<Arrangement>,
            required: true
        },
    },
    components: {
        VueDatePicker : VueDatePicker,
        PermanentNotification : PermanentNotification,
        AktivitetKomponent : AktivitetKomponent
    },
    mounted() {
        this.fetch();
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            savingOngoing: false,
            ledere : [] as Leder[],
            aktiviteter : [] as Aktivitet[],
        }
    },
    methods : {
        toggleExpand(aktivitet : Aktivitet) {
            aktivitet.expanded = !aktivitet.expanded;
        },
        handleSwitchChange(leder: Leder|any) {
            // Do not allow saving if a save is ongoing
            if(this.savingOngoing) {
                leder.godkjent = !leder.godkjent;
                return;
            }

            this.save(leder);
        },
        async fetch() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'getAlleAktiviteter',
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
                    
                    console.log('-----');
                    console.log(tidspunkt);
                    console.log(deltakere);
                    console.log('-----');
                    tidspunkter.push(
                        new AktivitetTidspunkt(
                            tidspunkt.id,
                            tidspunkt.sted,
                            tidspunkt.start,
                            tidspunkt.varighet,
                            tidspunkt.maksAntall,
                            tidspunkt.hendelseId,
                            aktivitetObj,
                            deltakere)
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
.godkjenning-div {
    margin: auto;
    margin-right: 0;
    padding-left: calc(var(--initial-space-box) * 4);
    display: flex;
    min-width: 170px;
}
.godkjenning-div .godkjenning-switch {
    margin: auto;
    margin-right: 0;
}
.leder-item {
    border-bottom: solid 1px var(--color-primary-grey-light);
    padding-bottom: calc(var(--initial-space-box) * 4) !important;
}
.leder-item.last-leader-item {
    border-bottom: none;
    padding-bottom: calc(var(--initial-space-box) * 2) !important;
}
@media(max-width: 767px) {
    .leder-item {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
}

</style>
<style>
.ledere-beskjed-rapporter > div > div {
    margin-bottom: 0 !important;
}
@media(max-width: 767px) {
    .leder-item .v-list-item__prepend {
        display: none !important;
    }
}
</style>