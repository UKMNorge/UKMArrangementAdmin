<template>
    <div class="aktivitet-komponent container main-container">
        <div class="col-xs-12 as-margin-top-space-2 nop-impt">
            <div class="col-xs-12 nop-impt">
                <div class="as-margin-top-space-2 as-margin-bottom-space-3">
                    <h4>Aktivitet</h4>
                </div>
                <div class="col-xs-5 as-margin-right-space-2 nop-impt">
                    <InputTextOverlay :placeholder="'Aktivitet navn'" v-model="aktivitet.navn" />
                </div>
                <div class="col-xs-5 nop-impt">
                    <InputTextOverlay :placeholder="'Sted'" v-model="aktivitet.sted" />
                </div>
            </div>
            
            <div class="col-xs-10 col-xs-10-with-margin as-margin-top-space-4 nop-impt">
                <div class="col-xs-12 nop-impt">
                    <v-textarea class="vue-as-textarea" label="Beskrivelse av aktivitet" v-model="aktivitet.beskrivelse"></v-textarea>
                </div>
            </div>
        </div>

        <div v-show="aktivitet.id > 0" class="col-sm-5 nop-impt as-margin-right-space-2">
            <v-select
                label="Tagger"
                multiple
                variant="outlined" 
                class="v-autocomplete-arr-sys" 
                :items="getTags()"
                v-model="aktivitet.tags"
                item-text="title"
                item-value="id" 
                chips
                closable-chips
                >
            </v-select>
        </div>

        <div class="col-xs-12 as-margin-top-space-2 nop-impt">
            <v-btn v-show="aktivitet.id == -1"
                class="v-btn-as v-btn-success"
                rounded="large"
                size="large"
                @click="createAktivitet(aktivitet)"
                variant="outlined">
                Opprett
            </v-btn>
        </div>
        <div class="col-xs-12 as-margin-top-space-2 nop-impt">
            <v-btn v-show="aktivitet.id != -1"
                class="v-btn-as v-btn-success as-margin-right-space-1"
                rounded="large"
                size="large"
                @click="saveAktivitet(aktivitet)"
                variant="outlined">
                Lagre aktiviteten
            </v-btn>

            <v-btn v-show="tab == null && aktivitet.id != -1"
                class="v-btn-as v-btn-error"
                rounded="large"
                size="large"
                @click="deleteAktivitet(aktivitet)"
                variant="outlined">
                Slett aktiviteten
            </v-btn>
        </div>

        <template v-if="aktivitet.id != -1">
            <div class="col-xs-12 as-margin-top-space-2 nop-impt">
                <hr>
            </div>

            <div class="col-xs-12 nop-impt">
                <div class="as-margin-top-space-2 as-margin-bottom-space-2">
                    <h4>Forekomster</h4>
                </div>
                <!-- Chip Group to Replace Tabs -->
                <v-chip-group v-model="tab" class="all-chips" selected-class="text-white">
                    <v-chip
                        v-for="tidspunkt in tidspunkter"
                        :key="tidspunkt.id"
                        :value="tidspunkt.navn"
                        color="primary"
                    >
                        <span v-if="tidspunkt.id > 0">{{ tidspunkt.getTittel() }}</span>
                        <v-icon v-else
                            class=""  
                            size="20">
                            mdi-plus
                        </v-icon>

                        <v-icon 
                            class="as-margin-left-space-1 mdi-close-circle" 
                            v-if="tidspunkt.id > 0" 
                            size="20" 
                            @click.stop="openDeleteDialog(tidspunkt)">
                            mdi-close
                        </v-icon>
                    </v-chip>
                </v-chip-group>

                <!-- Vuetify Delete Confirmation Dialog -->
                <v-dialog v-model="deleteDialog" max-width="400">
                    <v-card>
                        <v-card-title>Bekreft sletting</v-card-title>
                        <v-card-text>
                            Er du sikker på at du vil slette forekomsten: <b>{{ selectedTidspunkt?.getTittel() }}</b>?
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="grey" @click="deleteDialog = false">Avbryt</v-btn>
                            <v-btn color="red" @click="confirmDelete" :loading="deleting">Slett</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>

                <!-- Content Display for Selected Chip -->
                <v-window v-model="tab" class="aktivitet-window" :class="{'no-tab-open': tab == null}">
                    <v-window-item v-if="tidspunkter.length > 0" v-for="tidspunkt in tidspunkter" :key="tidspunkt.id" :value="tidspunkt.id">
                                <div class="col-xs-12 nop-impt">
                                    <div class="tidspunkt-tittel as-margin-top-space-3 as-margin-bottom-space-2">
                                        <h5>Velg start og slutt</h5>
                                    </div>
                                    
                                    <div class="col-sm-5 nop-impt tidspunkt-date-picker finfo-date-picker as-margin-right-space-2">
                                        <VueDatePicker 
                                            :format="(dates) => customFormat(dates)"
                                            :model-value="getStartSluttDate(tidspunkt)" 
                                            @update:model-value="(newDates : [Date, Date]) => handleDateChange(newDates, tidspunkt)" 
                                            :range="{ showLastInRange: false }"
                                            :hide-input-icon="true" 
                                            :clearable="false" />
                                    </div>
                                </div>

                                <div class="col-xs-12 as-margin-top-space-2 nop-impt">
                                    <div class="tidspunkt-tittel as-margin-top-space-3 as-margin-bottom-space-2">
                                        <h5>Velg sted</h5>
                                    </div>
                                    <div class="col-sm-3 nop-impt as-margin-right-space-2">
                                        <v-checkbox
                                            v-model="tidspunkt.erSammeStedSomAktivitet"
                                            label="Samme sted som aktivitet"
                                        ></v-checkbox>
                                    </div>
                                    <div v-show="!tidspunkt.erSammeStedSomAktivitet" class="col-sm-5 nop-impt as-margin-right-space-2">
                                            <InputTextOverlay :placeholder="'Sted'" v-model="tidspunkt.sted" />
                                    </div>
                                </div>

                                <div class="col-xs-12 as-margin-top-space-2 nop-impt">
                                    <div class="tidspunkt-tittel as-margin-top-space-3 as-margin-bottom-space-2">
                                        <h5>Legg forekomsten i en hendelse</h5>
                                    </div>
                                    
                                    <div class="col-sm-5 nop-impt as-margin-right-space-2">
                                        <v-select
                                            label="Hendelse" 
                                            variant="outlined" 
                                            class="v-autocomplete-arr-sys" 
                                            :items="hendelser" 
                                            v-model="tidspunkt.hendelse"
                                            item-text="title"
                                            item-value="id"
                                            >
                                        </v-select>
                                    </div>
                                </div>

                                <div class="col-xs-12 as-margin-top-space-2 nop-impt">
                                    <div class="tidspunkt-tittel as-margin-top-space-3 as-margin-bottom-space-2">
                                        <h5>Påmelding og deltakere</h5>
                                        <div class="col-xs-12 nop-impt">
                                            <div class="col-sm-4 nop-impt as-margin-right-space-2">
                                                <v-checkbox
                                                    v-model="tidspunkt.harPaamelding"
                                                    label="Krev påmelding"
                                                ></v-checkbox>
                                            </div>
                                            <div v-show="tidspunkt.harPaamelding" class="col-sm-4 nop-impt as-margin-right-space-2">
                                                <v-checkbox
                                                    v-model="tidspunkt.kunInterne"
                                                    label="Kun interne deltakere"
                                                ></v-checkbox>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 nop-impt">

                                            <div v-show="tidspunkt.harPaamelding" class="col-sm-4 nop-impt as-margin-right-space-2">
                                                <v-checkbox
                                                    v-model="tidspunkt.hasMaksAntall"
                                                    label="Ubegrenset antall deltakere"
                                                ></v-checkbox>
                                            </div>
                                            <div v-show="tidspunkt.harPaamelding" class="col-sm-4 nop-impt as-margin-right-space-2">
                                                <InputTextOverlay v-show="!tidspunkt.hasMaksAntall"
                                                :placeholder="'Begrenset antall deltakere'" 
                                                :model-value="tidspunkt.maksAntall?.toString()" 
                                                @update:model-value="val => tidspunkt.maksAntall = Number(val)"
                                                />
                                            </div>

                                            <div v-show="tidspunkt.harPaamelding" class="col-xs-12 nop-impt">
                                                <v-chip-group v-model="tidspunkt.deltakere" selected-class="text-white">
                                                    <v-chip
                                                        v-for="deltaker in tidspunkt.deltakere"
                                                        :key="deltaker.mobil"
                                                        :value="deltaker.mobil"
                                                        color="primary"
                                                    >
                                                        {{ deltaker.mobil }}
                                                    </v-chip>
                                                </v-chip-group>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                    <div class="col-xs-12 nop-impt as-margin-top-space-2">
                        <v-btn v-show="tidspunkt.id != -1"
                            class="v-btn-as v-btn-success"
                            rounded="large"
                            size="large"
                            @click="saveAktivitetOgTidspunkter(tidspunkt)"
                            variant="outlined">
                            Lagre
                        </v-btn>

                        <v-btn v-show="tidspunkt.id == -1"
                            class="v-btn-as v-btn-success"
                            rounded="large"
                            size="large"
                            @click="createTidspunkt(tidspunkt)"
                            variant="outlined">
                            Opprett forekomst
                        </v-btn>

                        <v-btn
                            class="as-margin-left-space-1 v-btn-as v-btn-error"
                            rounded="large"
                            size="large"
                            @click="deleteAktivitet(aktivitet)"
                            variant="outlined">
                            Slett aktiviteten
                        </v-btn>
                    </div>
                            
                    </v-window-item>
                </v-window>
                    
            </div>
        </template>
        
    </div>
</template>

<script lang="ts">
import Aktivitet from '../../objects/Aktivitet';
import AktivitetTidspunkt from '../../objects/AktivitetTidspunkt';
import { InputTextOverlay } from 'ukm-components-vue3';
import VueDatePicker from '@vuepic/vue-datepicker';
import Hendelse from './../../objects/Hendelse';
import AktivitetTag from './../../objects/AktivitetTag';
import { nextTick } from 'vue';
import type { PropType } from 'vue';  // Use type-only import for PropType


export default {
    props: {
        aktivitet: {
            type: Aktivitet,
            required: true,
        },
        tags: {
            type: Array as PropType<AktivitetTag[]>,
            required: true
        },
        hendelser: {
            type: Array as PropType<Hendelse[]>,
            required: true
        },
    },
    components: {
        InputTextOverlay : InputTextOverlay,
        VueDatePicker : VueDatePicker
    },
    computed: {
        tidspunkter() {
            let retTidspunkter = this.aktivitet.tidspunkter || [];
            return retTidspunkter;
        },
    },
    data() {
        return {
            tab : null, // Set to null so no chip is selected by default
            spaInteraction: (<any>window).spaInteraction, // Definert i main.ts
            
            // Dialog
            deleteDialog: false,
            selectedTidspunkt: null as AktivitetTidspunkt|null,
            deleting: false,
        };
    },
    mounted() {
        nextTick(() => {
            this.init();
        });        
    },
    methods: {
        getTags() : AktivitetTag[] {
            // Return all but -1 (placeholder for new tag)
            return this.tags.filter((tag : AktivitetTag) => tag.id != -1);
        },
        getStartSluttDate(tidspunkt: AktivitetTidspunkt): [Date, Date] {
            if (!tidspunkt.start) return [new Date(), new Date()];
            
            // Convert timestamp to date
            const startDate = new Date(tidspunkt.start.replace(" ", "T")); // Replace space with "T" for proper ISO format
            const endDate = new Date(tidspunkt.slutt.replace(" ", "T"));
            
            return [startDate, endDate];
        },
        hasMaxAntallChanged(tidspunkt : AktivitetTidspunkt) {
            tidspunkt.hasMaksAntall = !tidspunkt.hasMaksAntall;
        },
        customFormat(dates: [Date, Date] | null): string {
            if (!dates || dates.length !== 2) return "";

            const formatDate = (date: Date, hourOnly: boolean = false) => {
                const hours = String(date.getHours()).padStart(2, "0");
                const minutes = String(date.getMinutes()).padStart(2, "0");

                const daysOfWeek = ['søndag', 'mandag', 'tirsdag', 'onsdag', 'torsdag', 'fredag', 'lørdag'];

                if(hourOnly) {
                    return `kl. ${hours}:${minutes}`;
                }
                return `${daysOfWeek[date.getDay()]}, kl. ${hours}:${minutes}`;
            };

            if(dates[0].getDay() == dates[1].getDay()) {
                let from = formatDate(dates[0]).charAt(0).toUpperCase() + formatDate(dates[0]).slice(1);
                return `${from} til ${formatDate(dates[1], true)}`;
            }
            return `Fra ${formatDate(dates[0])} til ${formatDate(dates[1])}`;
        },
        handleDateChange(data: [Date, Date], tidspunkt: AktivitetTidspunkt) {
            const formatLocalDate = (date: Date) => {
                const day = String(date.getDate()).padStart(2, "0");
                const month = String(date.getMonth() + 1).padStart(2, "0"); // Months are 0-based
                const year = date.getFullYear();
                const hours = String(date.getHours()).padStart(2, "0");
                const minutes = String(date.getMinutes()).padStart(2, "0");

                return `${year}-${month}-${day} ${hours}:${minutes}`;
            };

            tidspunkt.start = formatLocalDate(data[0]);
            tidspunkt.slutt = formatLocalDate(data[1]);
        },
        async saveAktivitetOgTidspunkter(tidspunkt : AktivitetTidspunkt) {

            let resAktivitet = await tidspunkt.aktivitet.save();
            let resTidspunkt = await tidspunkt.save();

            if(resAktivitet && resTidspunkt) {
                this.spaInteraction.showMessage('Data er lagret!', 'Aktivitet og tidspunkt er lagret', 'success');
            } else {
                this.spaInteraction.showMessage('Noe gikk galt', resAktivitet + resTidspunkt, 'error');
            }
            

        },
        async saveAktivitet(aktivitet : Aktivitet) {
            let resAktivitet = await aktivitet.save();
            
            return resAktivitet
        },
        async createAktivitet(aktivitet : Aktivitet) {
            let results = await aktivitet.create();
        },
        async createTidspunkt(tidspunkt : AktivitetTidspunkt) {
            let results = await tidspunkt.create();
        },
        openDeleteDialog(tidspunkt : AktivitetTidspunkt) {
            this.selectedTidspunkt = tidspunkt;
            this.deleteDialog = true;
        },
        async confirmDelete() {
            if(this.selectedTidspunkt == null){ return };

            
            this.deleting = true; // Show loading state
            try {
                
                await this.deleteTidspunkt(this.selectedTidspunkt);
                this.tidspunkter = this.tidspunkter.filter((t : AktivitetTidspunkt) => t.id !== (<AktivitetTidspunkt>this.selectedTidspunkt).id);
                this.deleteDialog = false; // Close dialog
            } catch (error) {
                this.spaInteraction.showMessage('Noe gikk galt', 'Kunne ikke slette forekomsten. Prøv igjen', 'error');
            } finally {
                this.deleting = false;
            }
        },
        async deleteTidspunkt(tidspunkt : AktivitetTidspunkt|null) {
            if (tidspunkt == null) { return; }

            let results = await tidspunkt.delete();
            if(results && results.completed) {
                tidspunkt.aktivitet.removeTidspunkt(tidspunkt);
                this.tab = null; // Reset tab
                
                this.spaInteraction.showMessage('Forekomst slettet', 'Forekomst er slettet', 'success');
            } else {
                this.spaInteraction.showMessage('Noe gikk galt', results, 'error');
            }
            

        },
        deleteAktivitet(aktivitet : Aktivitet) {
            aktivitet.delete();
        },
        init() {
            this.tab = null;
        },
    },
};
</script>

<style scoped>
.aktivitet-komponent {
    background-color: #fff;
}

.tidspunkt-date-picker >>> .dp__pointer {
    margin: 0 !important;
    width: 100%;
}
.tidspunkt-date-picker >>> .dp--menu-wrapper {
    position: sticky;
}
.col-xs-10-with-margin {
    width: calc(83.3% + calc(var(--initial-space-box) * 2)) !important;
}
.all-chips {
    background: var(--color-primary-grey-extra-lightes);
    padding: calc(var(--initial-space-box)* 2) !important;
    border-radius: var(--radius-high) !important;
    border-bottom-left-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}
.all-chips >>> .v-slide-group__container {
    border-bottom: solid 1px #dbdbdb;
    padding-bottom: 16px;
}
.aktivitet-window {
    background: var(--color-primary-grey-extra-lightes);
    padding: calc(var(--initial-space-box)* 2) !important;
    border-radius: var(--radius-high) !important;
    border-top-left-radius: 0 !important;
    border-top-right-radius: 0 !important;
}
</style>
