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
        </div>
        
        <div class="col-xs-12 as-margin-top-space-2 nop-impt">
            <hr>
        </div>

        <div class="col-xs-12 nop-impt">
            <div class="as-margin-top-space-2 as-margin-bottom-space-2">
                <h4>Tidspunkter</h4>
            </div>
            <!-- Chip Group to Replace Tabs -->
            <v-chip-group v-model="tab" selected-class="text-white">
                <v-chip
                    v-for="tidspunkt in tidspunkter "
                    :key="tidspunkt.id"
                    :value="tidspunkt.navn"
                    color="primary"
                >
                    {{ tidspunkt.getTittel() }}
                </v-chip>
            </v-chip-group>

            <!-- Content Display for Selected Chip -->
            <v-window v-model="tab">
                <v-window-item v-for="tidspunkt in tidspunkter" :key="tidspunkt.id" :value="tidspunkt.id">
            
                            <div class="col-xs-12 nop-impt">
                                <div class="tidspunkt-tittel as-margin-top-space-3 as-margin-bottom-space-2">
                                    <h5>Velg start og slutt</h5>
                                </div>
                                
                                <div class="col-sm-5 nop-impt tidspunkt-date-picker finfo-date-picker as-margin-right-space-2">
                                    <VueDatePicker 
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
                                <div class="col-sm-2 nop-impt as-margin-right-space-2">
                                    <v-checkbox
                                        v-model="tidspunkt.erSammeStedSomAktivitet"
                                        label="Samme sted"
                                    ></v-checkbox>
                                </div>
                                <div v-show="!tidspunkt.erSammeStedSomAktivitet" class="col-sm-5 nop-impt as-margin-right-space-2">
                                        <InputTextOverlay :placeholder="'Sted'" v-model="tidspunkt.sted" />
                                </div>
                            </div>

                            <div class="col-xs-12 as-margin-top-space-2 nop-impt">
                                <div class="tidspunkt-tittel as-margin-top-space-3 as-margin-bottom-space-2">
                                    <h5>Velg hendelse og tag</h5>
                                </div>
                                
                                <div class="col-sm-5 nop-impt as-margin-right-space-2">
                                    <v-select
                                        label="Hendelse" 
                                        variant="outlined" 
                                        class="v-autocomplete-arr-sys" 
                                        :items="hendelser" 
                                        v-model="tidspunkt.hendelseId"
                                        item-text="title"
                                        item-value="id" 
                                        >
                                    </v-select>
                                </div>

                                <div class="col-sm-5 nop-impt as-margin-right-space-2">
                                    <v-select
                                        label="Tags"
                                        multiple
                                        variant="outlined" 
                                        class="v-autocomplete-arr-sys" 
                                        :items="hendelser" 
                                        v-model="tidspunkt.tags"
                                        item-text="title"
                                        item-value="id" 
                                        chips
                                        closable-chips
                                        >
                                    </v-select>
                                </div>
                            </div>

                            <div class="col-xs-12 as-margin-top-space-2 nop-impt">
                                <div class="tidspunkt-tittel as-margin-top-space-3 as-margin-bottom-space-2">
                                    <h5>Påmelding og deltakere</h5>
                                    <div class="col-sm-3 nop-impt as-margin-right-space-2">
                                        <v-checkbox
                                            v-model="tidspunkt.harPaamelding"
                                            label="Har påmelding"
                                        ></v-checkbox>
                                    </div>
                                    <div v-show="tidspunkt.harPaamelding" class="col-sm-2 nop-impt as-margin-right-space-2">
                                        <v-checkbox
                                            v-model="tidspunkt.hasMaksAntall"
                                            label="Ubegrenset antall deltakere"
                                        ></v-checkbox>
                                    </div>
                                    <div v-show="tidspunkt.harPaamelding" class="col-sm-2 nop-impt as-margin-right-space-2">
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


                <div class="col-xs-12 nop-impt as-margin-top-space-2">
                    <v-btn
                        class="v-btn-as v-btn-success"
                        rounded="large"
                        size="large"
                        @click="tidspunkt.save()"
                        variant="outlined">
                        Lagre
                    </v-btn>
                </div>
                        
                </v-window-item>
            </v-window>
                
        </div>
    </div>
</template>

<script lang="ts">
import Aktivitet from '../../objects/Aktivitet';
import AktivitetTidspunkt from '../../objects/AktivitetTidspunkt';
import { InputTextOverlay } from 'ukm-components-vue3';
import VueDatePicker from '@vuepic/vue-datepicker';
import Hendelse from './../../objects/Hendelse';
import AktivitetTag from './../../objects/AktivitetTag';

export default {
    props: {
        aktivitet: {
            type: Aktivitet,
            required: true,
        },
    },
    components: {
        InputTextOverlay : InputTextOverlay,
        VueDatePicker : VueDatePicker
    },
    data() {
        return {
            tab : null, // Set to null so no chip is selected by default
            hendelser : [] as Hendelse[],
            tags : [] as AktivitetTag[],
        };
    },
    computed: {
        tidspunkter() {
            let retTidspunkter = this.aktivitet.tidspunkter || [];
            
            // Create a new array with all existing items
            retTidspunkter = [...retTidspunkter];
            
            // Add a "new item" element at the end
            retTidspunkter.push(new AktivitetTidspunkt(-1, '', '', 0, 0, null, this.aktivitet, []));
            
            return retTidspunkter;
        },
    },
    mounted() {
        // Test data
        this.hendelser = [
            new Hendelse(1, 'Hendelse 1'),
            new Hendelse(2, 'Hendelse 2'),
            new Hendelse(3, 'Hendelse 3'),
        ];
        this.tags = [
            new AktivitetTag(1, 'Tag 1'),
            new AktivitetTag(2, 'Tag 2'),
            new AktivitetTag(3, 'Tag 3'),
        ];
        this.init();
    },
    methods: {
        getStartSluttDate(tidspunkt: AktivitetTidspunkt): [Date, Date] {
            if (!tidspunkt.start) return [new Date(), new Date()];
            
            // Convert timestamp to date
            const startDate = new Date(tidspunkt.start.replace(" ", "T")); // Replace space with "T" for proper ISO format
            const endDate = new Date(startDate.getTime() + tidspunkt.varighetMinutter * 60000);
            
            return [startDate, endDate];
        },
        hasMaxAntallChanged(tidspunkt : AktivitetTidspunkt) {
            tidspunkt.hasMaksAntall = !tidspunkt.hasMaksAntall;
            console.log(tidspunkt);

        },
        customFormat(date : Date) {
            // Format date as "DD-MM-YYYY HH:mm"
            const day = String(date.getDate()).padStart(2, "0");
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const year = date.getFullYear();

            const hours = String(date.getHours()).padStart(2, "0");
            const minutes = String(date.getMinutes()).padStart(2, "0");
            const seconds = String(date.getSeconds()).padStart(2, "0");

            return `${day}.${month}.${year}, kl. ${hours}:${minutes}`;
        },
        handleDateChange(data: [Date, Date], tidspunkt: AktivitetTidspunkt) {
            tidspunkt.start = data[0].toISOString().replace("T", " ").replace("Z", "");
            tidspunkt.slutt = data[1].toISOString().replace("T", " ").replace("Z", "");
            // this.getTimelineItems();
        },
        init() {
            if (this.aktivitet.tidspunkter.length > 0) {
                this.tab = this.aktivitet.tidspunkter[0].id; // Select first item by default
            }
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
</style>
