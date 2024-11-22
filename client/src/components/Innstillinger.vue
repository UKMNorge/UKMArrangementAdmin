<template>
    <div class="as-container main-container">
        <!-- Navn på arrangementet -->
        <div class="col-xs-12">
            <div v-if="arrangement != undefined" class="as-card-1 as-padding-space-3">
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
                            v-model="arrangement.antallDeltakere"
                            color="primary"
                            :label="arrangement.antallDeltakere ? 'På' : 'Av'"
                            @change="handleSwitchChange(0, arrangement.antallDeltakere)"
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
                        v-model="arrangement.openPamelding"
                        color="primary"
                        :label="arrangement.openPamelding ? 'På' : 'Av'"
                        @change="handleSwitchChange(1, arrangement.openPamelding)"
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
                            v-model="arrangement.openVideresending"
                            color="primary"
                            :label="arrangement.openVideresending ? 'På' : 'Av'"
                            @change="handleSwitchChange(2, arrangement.openVideresending)"
                        ></v-switch>
                    </div>
                </div>
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
import type Arrangement from './../objects/Arrangement';
import type { PropType } from 'vue';  // Use type-only import for PropType


export default {
    extends : MainComponent,
    props: {
        arrangement: {
            type: Object as PropType<Arrangement>,
            required: true
        },
    },
    components: {
        VueDatePicker : VueDatePicker
    },
    mounted() {

    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts

            loadingAntallDeltakere: false as any,
            loadingApenPamelding: false as any,
            loadingApenVideresending: false as any,

            arrangement : this.arrangement,

            savingOngoing: false,
        }
    },
    methods : {
        handleSwitchChange(type: number, value: boolean) {
            // Do not allow saving if a save is ongoing
            if(this.savingOngoing) {
                if(type == 0) this.arrangement.antallDeltakere = !value;
                if(type == 1) this.arrangement.openPamelding = !value;
                if(type == 2) this.arrangement.openVideresending = !value;
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
        resetLoading() {
            this.loadingAntallDeltakere = false;
            this.loadingApenPamelding = false;
            this.loadingApenVideresending = false;
        },
        async save() {
            this.savingOngoing = true;
           
            let res = await this.arrangement.save();
            if(res) {                
                this.resetLoading();
                this.savingOngoing = false;
            }
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