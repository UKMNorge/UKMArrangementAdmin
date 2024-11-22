<template>
    <div class="as-container main-container">
        <!-- Left -->
        <div class="col-xs-6">
            <div v-if="arrangement != undefined" class="as-card-1 as-padding-space-3">
                <div class="as-margin-bottom-space-3 as-margin-bottom-space-2">
                    <h4 class="">Påmeldingsfrist for de som vil vise frem noe</h4>
                </div>

                <div>                    
                    <div v-for="type in availableTypesViseFrem" class="type-item-innslag-checkbox" :key="type.id">
                        <v-checkbox
                            v-model="selectedTyperVise"
                            :label="type.title"
                            :value="type.key"
                            @change="handleCheckboxChange(type, true)"
                        ></v-checkbox>
                        <v-progress-circular
                            v-if="type.loading"
                            :size="16"
                            :width="2"
                            indeterminate
                            class="loading-checkbox"
                        ></v-progress-circular>
                        <div class="chip-antall">
                            <v-chip
                                :append-icon="type.isPerson ? 'mdi-account' : 'mdi-account-supervisor'"
                                :color="selectedTyperVise.includes(type.key) ? 'primary' : '#bebebe'">
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
                    <div v-for="type in availableTypesJobbe" class="type-item-innslag-checkbox" :key="type.id">
                        <v-checkbox
                            v-model="selectedTyperJobbe"
                            :label="type.title"
                            :value="type.key"
                            @change="handleCheckboxChange(type, false)"
                        ></v-checkbox>
                        <v-progress-circular
                            v-if="type.loading"
                            :size="16"
                            :width="2"
                            indeterminate
                            class="loading-checkbox"
                        ></v-progress-circular>
                        <div class="chip-antall">
                            <v-chip
                                :append-icon="type.isPerson ? 'mdi-account' : 'mdi-account-supervisor'"
                                :color="selectedTyperJobbe.includes(type.key) ? 'primary' : '#bebebe'">
                                {{ type.antall }} {{ type.isPerson ? 'person' + (type.antall == 1 ? '' : 'er') : 'innslag'}}
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
        handleCheckboxChange(type : InnslagType, isViseFrem : boolean) {
            let key = type.key;
            if(this.savingOngoing) {
                // find the type in selectedTyperVise
                if(isViseFrem) {
                    if(this.selectedTyperVise.includes(key)) {
                        this.selectedTyperVise = this.selectedTyperVise.filter(item => item !== key);
                    } else {
                        this.selectedTyperVise.push(key);
                    }
                }
                else {
                    if(this.selectedTyperJobbe.includes(key)) {
                        this.selectedTyperJobbe = this.selectedTyperJobbe.filter(item => item !== key);
                    } else {
                        this.selectedTyperJobbe.push(key);
                    }
                }

                return;
            }

            this.save(type);

        },
        async fetchTypes() {
            // Fetch types from API
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'getInnslagTyper',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            for(let type of results) {
                if(type.erViseFrem) {
                    this.availableTypesViseFrem.push(new InnslagType(type.id, type.key, type.navn, type.antall, (type.erEnkeltPerson == true)));
                    if(type.isSelected) {
                        this.selectedTyperVise.push(type.key);
                    }
                } else {
                    this.availableTypesJobbe.push(new InnslagType(type.id, type.key, type.navn, type.antall, (type.erEnkeltPerson == true)));
                    if(type.isSelected) {
                        this.selectedTyperJobbe.push(type.key);
                    }
                }
            }
        },
        async save(type : InnslagType) {
            this.savingOngoing = true;
            type.loading = true;

            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'saveInnslagType',
                // merge selectedTyperVise and selectedTyperJobbe
                selectedTyper: this.selectedTyperVise.concat(this.selectedTyperJobbe),
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            if(results){
                this.spaInteraction.showMessage('Lagret', 'Data er lagret', 'success');
                this.savingOngoing = false;
                type.loading = false;
            }else {
                this.spaInteraction.showMessage('Feil', 'Kunne ikke lagre data', 'error');
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
.type-item-innslag-checkbox:last-child {
    border-bottom: none;
}
.type-item-innslag-checkbox.no-border-item {
    border-bottom: none;
}
.loading-checkbox {
    margin-top: 20px;
    margin-left: 10px;
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