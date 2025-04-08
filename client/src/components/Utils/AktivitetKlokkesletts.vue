<template>
    <div class="">
        <v-dialog
            transition="dialog-bottom-transition"
            width="auto"
        >
            <template v-slot:activator="{ props: activatorProps }">
            <v-btn
                v-bind="activatorProps"
                class="v-btn-as v-btn-hvit as-margin-right-space-2"
                color="#000"
                rounded="large"
                :size="isMobile ? 'large' : 'x-large'"
                variant="outlined" >
                Klokkeslett
            </v-btn>
            </template>

            <template v-slot:default="{ isActive }">
            <v-card class="card-klokkeslett">
                <v-toolbar title="Alle klokkeslett"></v-toolbar>

                <div class="aktivitet-klokkeslett-div">
                    <div v-for="kSlett in localKlokkeslett" class="as-margin-top-space-2">
                        <div class="col-xs-12">
                            <div class="col-sm-4 nop-impt tidspunkt-date-picker finfo-date-picker">
                                <VueDatePicker 
                                    :format="(dates) => customFormat(dates)"
                                    :model-value="getStartSluttDate(kSlett)" 
                                    @update:model-value="(newDates : [Date, Date]) => handleDateChange(newDates, kSlett)" 
                                    :range="{ showLastInRange: false }"
                                    :hide-input-icon="true" 
                                    :clearable="false" />
                            </div>
                            <div class="col-xs-4">
                                <InputTextOverlay :placeholder="'Navn'" v-model="kSlett.navn" />
                            </div>
                            <div class="col-xs-3 button-kslett-item-div">
                                <v-btn
                                    class="v-btn-as v-btn-success as-margin-auto"
                                    rounded="large"
                                    @click="saveOrCreateKlokkeslett(kSlett)"
                                    variant="outlined">
                                    {{ kSlett.id == -1 ? 'Opprett' : 'Lagre' }}
                                </v-btn>
                                <v-btn v-if="kSlett.id != -1"
                                    class="v-btn-as v-btn-error as-margin-auto"
                                    rounded="large"
                                    @click="deleteKSlett(kSlett)"
                                    variant="outlined">
                                    Slett
                                </v-btn>
                            </div>
                        </div>
                        <div class="col-xs-12 as-padding-left-space-2 as-padding-right-space-2">
                            <hr>
                        </div>
                    </div>
                </div>

                <v-card-actions class="justify-end">
                <v-btn
                    text="Close"
                    @click="isActive.value = false"
                ></v-btn>
                </v-card-actions>
            </v-card>
            </template>
        </v-dialog>
    </div>
</template>
<script lang="ts">
import type { PropType } from 'vue';  // Use type-only import for PropType
import AktivitetKlokkeslett from '../../objects/AktivitetKlokkeslett';
import { InputTextOverlay } from 'ukm-components-vue3';
import VueDatePicker from '@vuepic/vue-datepicker';


export default {
    props: {
        klokkeslett: {
            type: Array as PropType<AktivitetKlokkeslett[]>,
            required: true
        },
    },
    computed: {
        isMobile() {
            return window.innerWidth < 576; // Adjust the breakpoint as needed
        },
    },
    mounted() {
        setTimeout(() => {
            console.log('Mounted klokkeslett');
            console.log(this.klokkeslett);

        }, 6000);
    },
    components: {
        InputTextOverlay : InputTextOverlay,
        VueDatePicker : VueDatePicker,
    },
    data() {
        return {
            localKlokkeslett: [...this.klokkeslett], // Create a copy of the array
            spaInteraction: (<any>window).spaInteraction,
        };
    },
    watch: {
        klokkeslett: {
            handler(newKslett) {
                this.localKlokkeslett = [...newKslett]; // Sync changes if parent updates `klokkeslett`
                this.addNewPlaceholder();
            },
            deep: true,
        },
    },
    methods: {
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
        async saveOrCreateKlokkeslett(kSlett: AktivitetKlokkeslett) {
            let isNew = kSlett.id === -1;
            let results = isNew ? await kSlett.create() : await kSlett.save();

            if (results && (<any>results).id === kSlett.id) {
                this.spaInteraction.showMessage('Lagret!', 'Klokkeslett ble lagret', 'success');
            } else {
                this.spaInteraction.showMessage('Noe gikk galt', 'Klokkeslett er ikke lagret!', 'error');
            }

            this.emitUpdate();
        },
        async deleteKSlett(kSlett: AktivitetKlokkeslett) {
            if (kSlett.id === -1) {
                this.localKlokkeslett = this.localKlokkeslett.filter(t => t.id !== -1);
                return;
            }

            let results = await kSlett.delete();
            if (results && results.completed === true) {
                this.localKlokkeslett = this.localKlokkeslett.filter(t => t.id !== kSlett.id);
                this.deleteKSlettFromArray(kSlett);
                this.spaInteraction.showMessage('Slettet!', 'Klokkeslett ble slettet', 'success');
            } else {
                this.spaInteraction.showMessage('Noe gikk galt', 'Klokkeslett er ikke slettet! Du må fjerne klokkeslett fra alle aktiviteter før du kan slette den.', 'error');
            }
        },
        emitUpdate() {
            // this.localKlokkeslett.push(new AktivitetKlokkeslett(-1, '', ''));
            this.$emit('update:klokkeslett', this.localKlokkeslett);
        },
        addNewPlaceholder() {
            console.log('')
            if (!this.localKlokkeslett.some(kSlett => kSlett.id === -1)) {
                this.localKlokkeslett.unshift(new AktivitetKlokkeslett(-1, '', '', ''));
            }
        },
        async deleteKSlettFromArray(kSlett: AktivitetKlokkeslett) {
            this.$emit('update:klokkeslett', this.localKlokkeslett);
        },
        getStartSluttDate(kSlett: AktivitetKlokkeslett): [Date, Date] {
            console.log(kSlett);
            if (!kSlett.fra) return [new Date(), new Date()];
            
            // Convert timestamp to date
            const startDate = new Date(kSlett.fra.replace(" ", "T")); // Replace space with "T" for proper ISO format
            const endDate = new Date(kSlett.til.replace(" ", "T"));
            
            return [startDate, endDate];
        },
        handleDateChange(data: [Date, Date], kSlett: AktivitetKlokkeslett) {
            const formatLocalDate = (date: Date) => {
                const day = String(date.getDate()).padStart(2, "0");
                const month = String(date.getMonth() + 1).padStart(2, "0"); // Months are 0-based
                const year = date.getFullYear();
                const hours = String(date.getHours()).padStart(2, "0");
                const minutes = String(date.getMinutes()).padStart(2, "0");

                return `${year}-${month}-${day} ${hours}:${minutes}`;
            };

            kSlett.fra = formatLocalDate(data[0]);
            kSlett.til = formatLocalDate(data[1]);
        },
    }

}

</script>
<style scoped>
.button-kslett-item-div {
    height: 60px;
    display: flex;
}
.card-klokkeslett {
    width: 95vw;
    max-width: 800px;
}
.tidspunkt-date-picker >>> .dp__pointer {
    margin: 0 !important;
    width: 100%;
    height: 60px;
    border: none !important;
}
.tidspunkt-date-picker >>> .dp--menu-wrapper {
    position: sticky;
}
@media(max-width: 767px) {
    .aktivitet-klokkeslett-div {
        min-width: 50vw;
        height: auto;
        max-height: 1200px;
    }
    .button-kslett-item-div {
        display: block;
    }
    .button-kslett-item-div button {
        height: 25px;
    }
    .button-kslett-item-div button:first-child {
        margin-bottom: 10px;
    }
}
</style>
