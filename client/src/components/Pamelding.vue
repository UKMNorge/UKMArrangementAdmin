<template>
    <div class="as-container main-container">
        <!-- Left -->
        <div class="col-xs-6">
            <div v-if="arrangement != undefined" class="as-card-1 as-padding-space-3">
                <div class="as-margin-bottom-space-3 as-margin-bottom-space-2">
                    <h4 class="">Påmeldingsfrist for de som vil vise frem noe</h4>
                </div>

                <div>
                    <p>{{ selectedTyperVise }}</p>
                    
                    <div v-for="type in availableTypesViseFrem" class="type-item-innslag-checkbox" :key="type.id">
                        <v-checkbox
                            v-model="selectedTyperVise"
                            :label="type.title"
                            :value="type.key"
                        ></v-checkbox>
                        <div class="chip-antall">
                            <v-chip
                                :append-icon="type.isPerson ? 'mdi-account' : 'mdi-account-supervisor'"
                                color="primary">
                                {{ type.antall}} {{ type.isPerson ? 'personer' : 'innslag'}}
                            </v-chip>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right -->
        <div class="col-xs-6">
            <div v-if="arrangement != undefined" class="as-card-1 as-padding-space-3">
                <div class="as-margin-bottom-space-3 as-margin-bottom-space-2">
                    <h4 class="">Påmeldingsfrist for de som vil vise frem noe</h4>
                </div>

                <div>
                    <p>{{ selectedTyperJobbe }}</p>
                    
                    <div v-for="type in availableTypesJobbe" class="type-item-innslag-checkbox" :key="type.id">
                        <v-checkbox
                            v-model="selectedTyperJobbe"
                            :label="type.title"
                            :value="type.key"
                        ></v-checkbox>
                        <div class="chip-antall">
                            <v-chip
                                :append-icon="type.isPerson ? 'mdi-account' : 'mdi-account-supervisor'"
                                color="primary">
                                {{ type.antall}} {{ type.isPerson ? 'personer' : 'innslag'}}
                            </v-chip>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</template>

<script lang="ts">
import MainComponent from './MainComponent.vue';
import '@vuepic/vue-datepicker/dist/main.css';
import type Arrangement from './../objects/Arrangement';
import type { PropType } from 'vue';  // Use type-only import for PropType
import InnslagType from './../objects/InnslagType';

export default {
    extends : MainComponent,
    props: {
        arrangement: {
            type: Object as PropType<Arrangement>,
            required: true
        },
    },
    components: {

    },
    mounted() {
        this.fetchTypes();
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            arrangement : this.arrangement,
            selectedTyperVise: [] as string[],
            selectedTyperJobbe: [] as string[],

            availableTypesViseFrem: [] as InnslagType[],
            availableTypesJobbe: [] as InnslagType[],

            savingOngoing: false,
        }
    },
    methods : {
        async fetchTypes() {
            // Fetch types from API
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'getInnslagTyper',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            for(let type of results) {
                if(type.erViseFrem) {
                    this.availableTypesViseFrem.push(new InnslagType(type.id, type.key, type.navn, 99, (type.erEnkeltPerson == true)));
                    if(type.isSelected) {
                        this.selectedTyperVise.push(type.key);
                    }
                } else {
                    this.availableTypesJobbe.push(new InnslagType(type.id, type.key, type.navn, 99, (type.erEnkeltPerson == true)));
                    if(type.isSelected) {
                        this.selectedTyperJobbe.push(type.key);
                    }
                }
            }

        },
        handleSwitchChange(type: number, value: boolean) {
            // Do not allow saving if a save is ongoing
            // if(this.savingOngoing) {
            //     if(type == 0) this.arrangement.antallDeltakere = !value;
            //     if(type == 1) this.arrangement.openPamelding = !value;
            //     if(type == 2) this.arrangement.openVideresending = !value;
            //     return;
            // }
            // switch(type) {
            //     case 0:
            //         this.loadingAntallDeltakere = value == true ? 'warning' : 'success';
            //         break;
            //     case 1:
            //         this.loadingApenPamelding = value == true ? 'warning' : 'success';
            //         break;
            //     case 2:
            //         this.loadingApenVideresending = value == true ? 'warning' : 'success';
            //         break;
            // }

            // this.save();
        },
        resetLoading() {
            // this.loadingAntallDeltakere = false;
            // this.loadingApenPamelding = false;
            // this.loadingApenVideresending = false;
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
.chip-antall {
    margin: 12px 0 auto auto!important
}
.type-item-innslag-checkbox {
    display: flex;
    justify-content: space-between;
    border-bottom: solid 1px var(--color-primary-grey-light);
}
.type-item-innslag-checkbox.no-border-item {
    border-bottom: none;
}
</style>

<style>
.type-item-innslag-checkbox .v-input__details {
    display: none;
}
.v-label.v-label--clickable {
    margin: 0 !important;
}
</style>