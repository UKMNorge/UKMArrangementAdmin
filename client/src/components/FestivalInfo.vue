<template>
    <div class="as-container main-container">
        <!-- Navn på arrangementet -->
        <div v-if="arrangement != undefined" class="col-xs-8">
            <div class="as-card-1 as-padding-space-3 as-margin-bottom-space-2">
                <div class="as-margin-bottom-space-4">
                    <h4 class="">Detaljer</h4>
                </div>

                <v-text-field 
                    v-model="arrangement.name"
                    label="Navn på arrangementet" 
                    prepend-icon="mdi-tooltip-edit"
                    class="v-text-field-arr-sys"
                    variant="outlined">
                </v-text-field>

                <div class="as-margin-top-space-2">
                    <v-text-field 
                        v-model="arrangement.place"
                        label="Hvor skal arrangementet være?" 
                        prepend-icon="mdi-map-marker"
                        class="v-text-field-arr-sys"
                        variant="outlined">
                    </v-text-field>
                </div>

                <div class="as-display-flex">
                    <div class="col-xs-6 nop-impt finfo-date-picker as-margin-right-space-2">
                        <p class="v-label title-dato">Startdato</p>
                        <VueDatePicker @update:model-value="handleDateChange" :calendar-icon="'mdi-clock-end'" v-model="arrangement.startDate" />
                    </div>
                    <div class="col-xs-6 nop-impt finfo-date-picker as-margin-left-space-2">
                        <p class="v-label title-dato">Sluttdato</p>
                        <VueDatePicker @update:model-value="handleDateChange" :calendar-icon="'mdi-clock-end'" v-model="arrangement.endDate" />
                    </div>
                </div>

                <div class="as-margin-top-space-4 as-margin-bottom-space-3">
                    <h4 class="">Status</h4>
                </div>

                <v-select
                    label="Status" 
                    variant="outlined" 
                    :prepend-icon="arrangement.status == 0 ? 'mdi-check-circle' : arrangement.status == 1 ? 'mdi-alert-circle' : 'mdi-close-circle'"
                    class="v-autocomplete-arr-sys" 
                    :items="statusTyper" 
                    v-model="arrangement.status"
                    item-text="title"
                    item-value="id" 
                    >
                </v-select>

                <div v-show="arrangement.status > 0">
                    <div class="as-margin-top-space-2">
                        <v-text-field 
                            v-model="arrangement.statusKortText"
                            label="Status beskrivelse" 
                            prepend-icon="mdi-bullhorn-variant"
                            class="v-text-field-arr-sys"
                            maxlength="140"
                            counter="140"
                            variant="outlined">
                        </v-text-field>
                    </div>
                    
                    <div class="as-margin-top-space-2">
                        <v-textarea 
                            v-model="arrangement.statusLangText"
                            class="vue-as-textarea" 
                            prepend-icon="mdi-text-long"
                            label="Detaljert beskrivelse av status">
                        </v-textarea>
                    </div>
                </div>
                

                <div class="as-margin-top-space-2">
                    <v-btn
                        class="v-btn-as v-btn-success"
                        rounded="large"
                        size="large"
                        @click="save()"
                        variant="outlined">
                        Lagre
                    </v-btn>
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="as-card-1 as-padding-space-3 as-margin-bottom-space-2">

                <v-timeline v-if="arrangement.isActive()" density="compact" side="end">
                     <template v-for="tlItem in getTimelineItems()" v-bind:key="tlItem.id">
                        <v-timeline-item class="mb-4" :dot-color="tlItem.getColor()" size="small">
                            <div :class="tlItem.finished ? 'finished-item' : ''" class="d-flex justify-space-between flex-grow-1 item-timeline">
                                <div style="width: 100%">
                                    <h5>{{ tlItem.title }}</h5>
                                    <p v-html="tlItem.description"></p>

                                </div>
                            <div class="date-timeline flex-shrink-0">
                                {{ tlItem.getDateToString() }}
                            </div>
                            </div>
                        </v-timeline-item>
                     </template>
                </v-timeline>
                <div v-else>
                    <h5>Arrangementet er avlyst!</h5>
                    <div class="as-display-flex as-margin-top-space-2">
                        <div class="as-margin-auto">
                            <v-icon color="warning" icon="mdi-alert" size="x-large"></v-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import MyObject from './../objects/myObject'
import MainComponent from './MainComponent.vue';
import type Arrangement from './../objects/Arrangement';
// import DateTimePicker from './Utils/DateTimePicker.vue';
import StatusType from './../objects/StatusType';
import TimelineItem from './../objects/TimelineItem';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
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
        this.fetchVideresendingData();
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            statusTyper: [
                new StatusType(0, 'Gjennomføres som planlagt'),
                new StatusType(1, 'Gjennomføres men har viktig melding'),
                new StatusType(2, 'Avlyses'),
            ] as StatusType[],
            selectedStatus: 0, // Initialize as null first
            myObject : new MyObject(),
            timelineItems : [] as TimelineItem[],
            arrangement : this.arrangement,
            pameldteData : {} as any,
        }
    },
    computed: {
        antallVideresendtInnslag(): number {
            return this.pameldteData.videresending.length;
        },
        antallVideresendtPersoner(): number {
            let antallPersoner = 0;
            for(let innslag of this.pameldteData.videresending) {
                antallPersoner += innslag.personer ? innslag.personer.length : 0;
            }
            return antallPersoner;
        },
        videresendingText(): string {
            return this.antallVideresendtInnslag + ' innslag ('+ this.antallVideresendtPersoner +' person'+ (this.antallVideresendtPersoner != 1 ? "er" : "") +')';
        },
        antallPameldtePersoner(): number {
            let antallPersoner = 0;
            for(let innslag of this.pameldteData.pameldte) {
                antallPersoner += innslag.personer ? innslag.personer.length : 0;
            }
            return antallPersoner;
        },
        pameldteText(): string {
            return this.antallPameldtePersoner +' person'+ (this.antallPameldtePersoner != 1 ? "er" : "");
        }
    },
    methods : {
        handleDateChange() {
            this.getTimelineItems();
        },
        save() {
                this.arrangement.save();
        },
        getTimelineItems() {
            this.timelineItems = [];

            this.timelineItems = [
                new TimelineItem('0', 'Videresending er ' + (this.arrangement.isVideresendingOpen() ? 'åpen' : 'stengt'), '', '', this.videresendingText + ' ble videresendt', !this.arrangement.isVideresendingOpen() ? 'warning' : '', true),
                new TimelineItem('10', 'Festivalen starter', '', this.getDateFormat(this.arrangement.startDate), '', '', true),
            ];
            
            var dayCount = 1;
            for(let day of this.generateDaysBetweenDates()) {
                this.timelineItems.push(
                    new TimelineItem('day'+dayCount, 'Dag '+dayCount, this.getDateFormat(day, true), '', '', '', false)
                );
                dayCount++;
            }

            this.timelineItems.push(
                new TimelineItem('40', 'Festivalen er ferdig', this.getDateFormat(this.arrangement.endDate), '', this.pameldteText + ' deltok', '', false),
            );

            return this.timelineItems;
        },
        getDateFormat(date : Date, withoutHours : boolean = false) : string {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
            const day = String(date.getDate()).padStart(2, '0');
            const formattedDate = `${day}-${month}`;

            // Extract the hour (HH format)
            const hour = String(date.getHours()).padStart(2, '0');
            const minute = String(date.getMinutes()).padStart(2, '0');

            return `${formattedDate}` + (withoutHours == false ? ` ${hour}:${minute}` : '');
        },
        generateDaysBetweenDates() {
            if(this.arrangement.startDate == null || this.arrangement.endDate == null) {
                return [];
            }

            const start = new Date(this.arrangement.startDate);
            const end = new Date(this.arrangement.endDate);
            const days = [];
            for (let date = start; date <= end; date.setDate(date.getDate() + 1)) {
                days.push(new Date(date));
            }
            return days;
        },
        async fetchVideresendingData() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'getPameldingData',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
            this.pameldteData = results;
        }
    }
}
</script>
<style scoped>
.title-item-timeline {
    margin-top: 6px;
}
.item-timeline {
    width: 100%;
    opacity: .35;
}
.item-timeline.finished-item {
    opacity: 1;
}
.date-timeline {
    max-width: 100px;
    text-align: right !important;
    font-weight: 300;
    font-size: 12px;
}
.main-container {
    height: 100vh;
}
.title-dato {
    margin-left: 36px;
    font-weight: bold;
    margin-bottom: 8px;
}
</style>

<style>
.v-timeline-item__body  {
    width: 100%;
}
.v-timeline--vertical.v-timeline.v-timeline--side-end .v-timeline-item .v-timeline-item__body {
    /* padding-top: 20px !important; */
    padding-inline-start: 18px !important;
}
.finfo-date-picker input {
    border-radius: var(--radius-normal) !important;
    padding: 14px 16px !important;
    font-size: 14px;
    border-color: #aaaaaa !important;
    margin-left: 36px !important;
    background: #f4f4f4 !important;
}
.finfo-date-picker .dp__input_wrap {
    max-width: 290px;
    
}
.finfo-date-picker .dp--clear-btn {
    right: -35px !important
}
.finfo-date-picker .dp__input_icon.dp__input_icons {
    margin-left: -8px !important;
}
    
</style>