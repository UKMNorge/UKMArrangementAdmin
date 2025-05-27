<template>
    <div class="as-container main-container">
        <div class="aktiviteter-buttons as-margin-bottom-space-4 as-display-flex">
            <v-btn
                class="v-btn-as v-btn-hvit as-margin-right-space-2"
                prepend-icon="mdi-plus"
                color="#000"
                rounded="large"
                :size="isMobile ? 'large' : 'x-large'"
                @click="nyGruppe"
                variant="outlined" >
                Legg til Gruppe
            </v-btn>
        </div>

        <div class="">
            <h4>Gruppering av hendelser</h4>
        </div>

        <v-card class="mx-auto h-g-card as-margin-top-space-4">
            <v-list class="h-g-list nop-impt">
                <div>
                    <div class="hg-item as-card-1 as-margin-top-space-2" v-for="hendelseGruppe in hendelseGrupper" :key="hendelseGruppe.id">
                        <v-list-item @click="toggleExpand(hendelseGruppe)" class="hendelse-gruppe-item as-card-1 as-padding-space-3">
                            
                            <div class="d-flex justify-space-between align-center">
                                <v-list-item-title class="text-h6">
                                    {{ hendelseGruppe.title }}
                                </v-list-item-title>
                            </div>

                            <template v-slot:append>
                                <v-btn 
                                icon 
                                variant="text" 
                                @click.stop="toggleExpand(hendelseGruppe)"
                                >
                                    <v-icon>{{ hendelseGruppe.expanded ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                                </v-btn>
                            </template>
                        </v-list-item>

                        <v-expand-transition>
                            
                            <div v-if="hendelseGruppe.expanded" class="extendable-hendelse as-padding-space-3" @click.stop>
                                <div class="hendelse-gruppe-data as-margin-bottom-space-2">
                                    <div class="col-xs-5 as-margin-right-space-2 nop-impt">
                                        <v-text-field
                                            v-model="hendelseGruppe.title"
                                            variant="outlined"
                                            class="v-text-field-arr-sys"
                                            label="Gruppe navn"
                                            required
                                        ></v-text-field>
                                    </div>
                                    <div class="col-xs-5 as-margin-right-space-2 nop-impt">
                                        <v-textarea 
                                            class="v-text-field-arr-sys" 
                                            label="Beskrivelse" 
                                            variant="outlined"
                                            v-model="hendelseGruppe.beskrivelse">
                                        </v-textarea>
                                    </div>
                                </div>

                                <div class="as-margin-bottom-space-2">
                                    <h5>Hendelser:</h5>
                                </div>

                                <div class="as-margin-top-space-2">
                                    <v-select
                                        chips
                                        label="Hendelser" 
                                        variant="outlined" 
                                        class="v-autocomplete-arr-sys" 
                                        :items="hendelser"
                                        v-model="hendelseGruppe.hendelser"
                                        multiple
                                        item-text="title"
                                        item-value="id" 
                                        >
                                    </v-select>
                                </div>

                                <div class="col-xs-12 as-margin-top-space-4 nop-impt">
                                    <v-btn 
                                        class="v-btn-as v-btn-success as-margin-right-space-1"
                                        rounded="large"
                                        size="large"
                                        @click="lagreGruppeHendelsen(hendelseGruppe)"
                                        variant="outlined">
                                        {{ hendelseGruppe.id == -1 ? 'Opprett' : 'Lagre' }}
                                    </v-btn>

                                    <v-btn v-show="hendelseGruppe.id != -1"
                                        class="v-btn-as v-btn-error as-margin-right-space-1"
                                        rounded="large"
                                        size="large"
                                        @click="deleteGruppeHendelsen(hendelseGruppe)"
                                        variant="outlined">
                                        Slett gruppen
                                    </v-btn>
                                </div>

                            </div>
                        </v-expand-transition>
                    </div>
                </div>
            </v-list>
        </v-card>

    </div>
</template>

<script lang="ts">
import VueDatePicker from '@vuepic/vue-datepicker';
import type { PropType } from 'vue';  // Use type-only import for PropType
import Arrangement from '../objects/Arrangement'; // Assuming you have an Arrangement type defined
import Hendelse from '../objects/Hendelse'; // Assuming you have a Hendelse type defined
import HendelseGruppe from '../objects/HendelseGruppe'; // Assuming you have a Hendelse type defined

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
    },
    mounted() {
        this.init();
    },
    data() {
        return {
            fetched : false as boolean,
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            hendelseGrupper: [] as any[],
            hendelser: [] as Hendelse[],        
        }
    },
    methods : {
        init() {
            console.log('Hendelser component mounted');
            this.fetch();
            this.fetchAlleHendelser();
        },
        toggleExpand(hGruppe : HendelseGruppe) {
            hGruppe.expanded = !hGruppe.expanded;
        },
        async fetchAlleHendelser() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'aktivitet/getAlleHendelser',
            };
    
            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
            
            let hendelser : Hendelse[] = [];
            for(let hendelse of results) {
                hendelser.push(new Hendelse(
                    hendelse.id, 
                    hendelse.navn, 
                ));
            }
    
            this.hendelser = hendelser;
    
            return results;
        },
        async lagreGruppeHendelsen(hGruppe : HendelseGruppe) {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'hendelser/saveHendelseGruppe',
                id : hGruppe.id,
                navn: hGruppe.title,
                beskrivelse: hGruppe.beskrivelse,
                hendelser: hGruppe.getHendelser(),
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            if(results && hGruppe.id == -1) {
                this.fetch();
            }
        },
        async deleteGruppeHendelsen(hGruppe : HendelseGruppe) {
            if(!confirm('Er du sikker på at du vil slette denne gruppen?')) {
                return;
            }

            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'hendelser/deleteHendelseGruppe',
                id : hGruppe.id,
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            // Remove the group from the list
            this.hendelseGrupper = this.hendelseGrupper.filter(h => h.id !== hGruppe.id);
        },
        closeAll() {
            for(let hGruppe of this.hendelseGrupper) {
                hGruppe.expanded = false;
            }
        },
        nyGruppe() {
            this.closeAll();
            for(let hGruppe of this.hendelseGrupper) {
                if(hGruppe.id == -1) {
                    hGruppe.expanded = true;
                    return;
                }
            }

            let hGruppe = new HendelseGruppe(
                -1, 
                'Ny gruppe',
                '',
                []
            );
            hGruppe.expanded = true;
            this.hendelseGrupper.push(hGruppe);
        },
        async fetch() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'hendelser/getAlleHendelseGrupper',
                
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            this.hendelseGrupper = [];

            for(let hGruppe of results) {
                let hendelser = [];
                
                for(let hKey in hGruppe.hendelser) {
                    let hendelse = hGruppe.hendelser[hKey];
                    let h = new Hendelse(
                        hendelse.id, 
                        hendelse.navn,
                    );
                    h.gruppeId = hGruppe.id;

                    hendelser.push(h);
                }
                
                let hG = new HendelseGruppe(
                    hGruppe.id, 
                    hGruppe.navn,
                    hGruppe.beskrivelse,
                    hendelser
                );

                this.hendelseGrupper.push(hG);

            }

            this.fetched = true;
        }
    }
}
</script>

<style scoped>
.hendelse-item {
    background-color: var(--color-primary-grey-lightest);
    box-shadow: none;
}
.hendelse-gruppe-item {
    box-shadow: none;
    padding: calc(3 * var(--initial-space-box)) !important;
}
.extendable-hendelse,
.hendelse-gruppe-data {
    display: grid;
}
.h-g-card,
.h-g-card .h-g-list {
    background: transparent !important;
    box-shadow: none !important;
}
.hg-item {
    overflow: hidden;
    box-shadow: none !important;
    background: #fff !important;
}
</style>