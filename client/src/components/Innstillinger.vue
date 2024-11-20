<template>
    <div class="as-container main-container">
        <!-- Navn på arrangementet -->
        <div class="col-xs-12">
            <div class="as-card-1 as-padding-space-3">
                <div class="as-margin-bottom-space-3 as-margin-bottom-space-2">
                    <h4 class="">Innstillinger</h4>
                </div>

                <div class="switch-item-select as-margin-top-space-1 as-padding-bottom-space-1">
                    <div class="text-switch">
                        <label class="">Antall deltakere synlighet</label>
                        <span class="beskrivelse-switch">Aktiver for å vise antall deltakere som har meldt seg på arrangementet på din landsside. Skru av hvis du ikke ønsker at dette skal vises.</span>
                    </div>
                    <div class="switch">
                        <v-switch
                            :loading="loadingAntallDeltakere"
                            v-model="antallDeltakere"
                            color="primary"
                            :label="antallDeltakere ? 'På' : 'Av'"
                            @change="handleSwitchChange(0, antallDeltakere)"
                        ></v-switch>
                    </div>
                </div>

                <div class="switch-item-select as-margin-top-space-1 as-padding-bottom-space-1">
                    <div class="text-switch">
                        <label class="">Åpen påmelding</label>
                        <span class="beskrivelse-switch">Når påmeldingen er åpen, kan deltakere melde seg på dette arrangementet via påmeldingssystemet.</span>
                    </div>
                    <div class="switch">
                        <v-switch
                        :loading="loadingApenPamelding"
                        v-model="openPamelding"
                        color="primary"
                        :label="openPamelding ? 'På' : 'Av'"
                        @change="handleSwitchChange(1, openPamelding)"
                        ></v-switch>
                    </div>
                </div>
                
                <div class="switch-item-select no-border-item as-margin-top-space-1 as-padding-bottom-space-1">
                    <div class="text-switch">
                        <label class="">Åpen videresending</label>
                        <span class="beskrivelse-switch">Når videresending er åpen, kan fylkene sende innslag og deltakere videre til dette arrangementet.</span>
                    </div>
                    <div class="switch">
                        <v-switch
                            :loading="loadingApenVideresending"
                            v-model="openVideresending"
                            color="primary"
                            :label="openVideresending ? 'På' : 'Av'"
                            @change="handleSwitchChange(2, openVideresending)"
                        ></v-switch>
                    </div>
                </div>

                

                <!-- <div class="as-margin-top-space-4">
                    <v-btn
                        class="v-btn-as v-btn-success"
                        rounded="large"
                        size="large"
                        @click="save()"
                        variant="outlined">
                        Lagre
                    </v-btn>
                </div> -->
            </div>
        </div>
        
    </div>
</template>

<script lang="ts">
import MainComponent from './MainComponent.vue';
// import DateTimePicker from './Utils/DateTimePicker.vue';
import StatusType from '../objects/StatusType';
import TimelineItem from '../objects/TimelineItem';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

export default {
    extends : MainComponent,
    props: {
        title: String,
    },
    components: {
        VueDatePicker : VueDatePicker
    },
    mounted() {
        this.init();

    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts

            antallDeltakere: false,
            openPamelding: false,
            openVideresending: false,

            loadingAntallDeltakere: false as any,
            loadingApenPamelding: false as any,
            loadingApenVideresending: false as any,

            savingOngoing: false,
        }
    },
    methods : {
        handleSwitchChange(type: number, value: boolean) {
            // Do not allow saving if a save is ongoing
            if(this.savingOngoing) {
                if(type == 0) this.antallDeltakere = !value;
                if(type == 1) this.openPamelding = !value;
                if(type == 2) this.openVideresending = !value;
                return;
            }
            switch(type) {
                case 0:
                    this.loadingAntallDeltakere = value == true ? 'warning' : 'success';
                    break;
                case 1:
                    this.loadingApenPamelding = value == true ? 'warning' : 'success';
                    break;
                case 2:
                    this.loadingApenVideresending = value == true ? 'warning' : 'success';
                    break;
            }

            this.save();
        },
        async init(){
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'getInnstillinger',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            if(results != null) {
                this.antallDeltakere = results.antallDeltakere;
                this.openPamelding = results.openPamelding;
                this.openVideresending = results.openVideresending;
            }else {
                this.spaInteraction.showMessage('Feil', 'Kunne ikke hente innstillinger', 'error');
            }
            
        },
        resetLoading() {
            this.loadingAntallDeltakere = false;
            this.loadingApenPamelding = false;
            this.loadingApenVideresending = false;
        },
        async save() {
            this.savingOngoing = true;
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'saveInnstillinger',
                antallDeltakere: this.antallDeltakere,
                openPamelding: this.openPamelding,
                openVideresending: this.openVideresending,
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
            
            this.savingOngoing = false;
            if(results != null && results.success == true) {
                this.spaInteraction.showMessage('Lagret', 'Arrangementet er lagret', 'success');
            } else {
                this.spaInteraction.showMessage('Feil', 'Kunne ikke lagre arrangementet', 'error');
            }

            this.resetLoading();
            
        },
    }
}
</script>

<style scoped>
.switch-item-select {
    display: flex;
    border-bottom: solid 1px #bebebe;
}
.switch-item-select.no-border-item {
    border-bottom: none;
}
.switch-item-select .switch {
    margin: auto;
    margin-right: 0;
}
.switch-item-select .text-switch {
    display: inline-grid;
}
.switch-item-select .text-switch label {
    margin: auto;
    margin-left: 0;
    font-weight: 400;
    font-size: 14px;
}
.switch-item-select .text-switch .beskrivelse-switch {
    font-size: 12px;
    font-weight: 300;
}
</style>