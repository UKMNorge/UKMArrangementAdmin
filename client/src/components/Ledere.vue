<template>
    <div class="as-container main-container">
        <!-- Navn på arrangementet -->
        <div class="col-xs-12">
            <div v-if="arrangement != undefined" class="as-card-1 as-padding-space-3">
                <div class="as-margin-bottom-space-3 as-margin-bottom-space-2">
                    <h4 class="">Videresendte ledere</h4>
                </div>

                <v-list class="alle-ledere" v-for="(leder, index) in ledere" :key="leder.id" lines="three">
                    <v-list-item class="leder-item" :class="{ 'last-leader-item': index === ledere.length - 1 }">
                        <template v-slot:prepend>
                            <v-icon size="40px" color="#386e9e">{{ leder.getIcon() }}</v-icon>
                        </template>
                        <!-- Leder info -->
                        <div class="as-display-flex">
                            <div>
                                <div>
                                    <h4>{{ leder.navn ? leder.navn : 'Navn er ikke definert' }}</h4>
                                    <p><b>Epost:</b> {{ leder.epost }}</p>
                                    <p><b>Mobil:</b> {{ leder.mobil }}</p>
                                    <p><b>Type:</b> {{ leder.type }}</p>
                                </div>
                                <div>
                                    <p><b>Arrangement:</b> {{ leder.fraArrangementNavn }}</p>
                                    <p><b>Fylke:</b> {{ leder.fraFylkeNavn }}</p>
                                </div>
                                <hr>
                                <div>
                                    <p v-if="leder.beskrivelse == null || leder.beskrivelse.length < 1">Ingen beskrivelse</p>
                                    <p v-else><b>Beskrivelse:</b> {{ leder.beskrivelse }}</p>
                                </div>
                            </div>

                            <!-- Godkjenning -->
                            <div class="godkjenning-div">
                                <v-switch 
                                    color="primary"
                                    v-model="leder.godkjent" 
                                    class="godkjenning-switch" 
                                    :label="leder.godkjent == null ? 'Ikke besvart' : (leder.godkjent == true ? 'Godkjent' : 'Ikke godkjent')" 
                                    :indeterminate="leder.godkjent == null"
                                    :loading="leder.loading ? 'warning' : false"
                                    @change="handleSwitchChange(leder)"
                                    >
                                </v-switch>
                            </div>                            
                        </div>
                    </v-list-item>
                </v-list>
                <div v-if="ledere.length < 1">
                    <p>Ingen ledere er videresendt</p>
                </div>

                <div class="ledere-beskjed-rapporter" >
                    <PermanentNotification 
                        typeNotification="info" 
                        :tittel="`Oversikt over videresendte ledere`" 
                        :isHTML="true"
                        :description="`<p>Full oversikt over ledere finnes på <a href='?page=UKMrapporter&action=rapportVue&rapportId=ledereOversikt'>rapporter</a></p>`" 
                    />
                </div>

            </div>

        </div>
        
    </div>
</template>

<script lang="ts">
import MainComponent from './MainComponent.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import type Arrangement from './../objects/Arrangement';
import type { PropType } from 'vue';  // Use type-only import for PropType
import Leder from './../objects/Leder';
import { PermanentNotification } from 'ukm-components-vue3';


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
        PermanentNotification : PermanentNotification
    },
    mounted() {
        this.fetchLedere();
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            savingOngoing: false,
            ledere : [] as Leder[],
        }
    },
    methods : {
        handleSwitchChange(leder: Leder|any) {
            // Do not allow saving if a save is ongoing
            if(this.savingOngoing) {
                leder.godkjent = !leder.godkjent;
                return;
            }

            this.save(leder);
        },
        async fetchLedere() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'getLedere',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
            
            for(let leder of results) {
                this.ledere.push(
                    new Leder(
                        leder.id, 
                        leder.navn, 
                        leder.epost, 
                        leder.mobil, 
                        leder.type, 
                        leder.fraArrangementNavn, 
                        leder.fraFylkeNavn, 
                        leder.beskrivelse, 
                        leder.godkjent == null ? null : (leder.godkjent == 1 ? true : false)
                    )
                );
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
</style>
<style>
.ledere-beskjed-rapporter > div > div {
    margin-bottom: 0 !important;
}
</style>