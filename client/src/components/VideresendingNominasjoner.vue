<template>
<div class="as-container main-container">
        <div class="col-xs-12">
            <div class="as-padding-left-space-1 as-padding-right-space-1 as-margin-bottom-space-3">
                <h4>Videresending nominasjoner</h4>
            </div>

            <VideresendingNominasjonerFilter v-model="selectedFylkeId" @update:innslagTypeValue="innslagTypeValue = $event" :fylker="fylker" :innslaggrupper="innslagGrupper" :innslagTypeValue="innslagTypeValue" />

            <div class="">
                <v-list lines="three" class="nominasjon-list nop nom">
                    <div class="">
                        <div class="as-card-1 nop-impt as-margin-bottom-space-2" v-for="gruppe in getFiltrerteNominasjoner()" :key="gruppe.innslag.id">

                            <v-list-item @click="toggleExpandGruppe(gruppe)" class="nominasjon-item nop-impt as-card-1 as-padding-space-3">
                                <div class="d-flex justify-space-between align-center">
                                    <v-list-item-title class="text-h6">
                                        {{ gruppe.innslag.navn }}
                                    </v-list-item-title>
                                    <!-- <v-chip class="chip-on-title ml-2" v-show="pameldingMinstEn(aktivitet)" color="primary" size="small">
                                        Krever påmelding
                                    </v-chip> -->
                                </div>
                                <!-- <v-list-item-subtitle>
                                    <span>{{ getAntallTidspunkter(aktivitet) }}</span>
                                </v-list-item-subtitle> -->
                                <template v-slot:prepend>
                                    <v-avatar color="grey-lighten-1">
                                    <v-icon color="white">mdi-account-arrow-right-outline</v-icon>
                                    </v-avatar>
                                </template>

                                
                                <template v-slot:append>
                                    <div class="innslag-type-chip">
                                        <div class="">
                                            <v-chip
                                                class="as-margin-left-space-1"
                                                v-if="gruppe.innslag.innslag_type"
                                                color="">
                                                {{ gruppe.innslag.innslag_type }}
                                            </v-chip>

                                            <v-chip
                                                class="as-margin-left-space-1"
                                                v-if="gruppe.innslag.fylke_fra"
                                                append-icon="mdi-map-marker-multiple"
                                                color="primary">
                                                {{ gruppe.innslag.fylke_fra }}
                                            </v-chip>
                                            
                                            <v-chip
                                                class="as-margin-left-space-1"
                                                v-if="gruppe.innslag.fylke_fra"
                                                append-icon="mdi-list-status"
                                                :color="getStatusColor(gruppe.getStatusPerGruppe())">
                                                {{ convertStatusToReadable(gruppe.getStatusPerGruppe()) }}
                                            </v-chip>
                                        </div>
                                    </div>
                                    <v-btn 
                                    icon 
                                    variant="text" 
                                    >
                                        <v-icon>{{ gruppe.expanded ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                                    </v-btn>
                                </template>
                            </v-list-item>

                            <v-expand-transition>
                                <div v-show="gruppe.expanded" class="as-margin-top-space-1 nominasjon-list-expanded">
                                    <div class="innslag-detaljer as-padding-left-space-2 as-padding-right-space-2">
                                        <div v-if="gruppe.innslag.type_key" class="as-margin-bottom-space-1">
                                            <strong>Innslagstype:</strong> {{ gruppe.innslag.type_key }}
                                        </div>
                                        <div v-if="gruppe.innslag.omrade_navn" class="as-margin-bottom-space-1">
                                            <strong>Område:</strong> {{ gruppe.innslag.omrade_navn }}
                                        </div>
                                        <div v-if="gruppe.innslag.sjanger" class="as-margin-bottom-space-1">
                                            <strong>Sjanger:</strong> {{ gruppe.innslag.sjanger }}
                                        </div>
                                        <div class="as-margin-bottom-space-1">
                                            <strong>Har titler:</strong>
                                            {{ gruppe.innslag.har_titler ? 'Ja' : 'Nei' }}
                                        </div>
                                        <div v-if="gruppe.innslag.beskrivelse" class="as-margin-bottom-space-1 innslag-beskrivelse">
                                            <strong>Beskrivelse:</strong>
                                            <span class="d-block">{{ gruppe.innslag.beskrivelse }}</span>
                                        </div>
                                    </div>

                                    <template v-if="gruppe.titler.length">
                                        <div class="as-padding-bottom-space-1 as-padding-left-space-2">
                                            <div v-for="tittel in gruppe.titler" :key="tittel.id" class="as-margin-bottom-space-2 as-margin-top-space-2 as-padding-left-space-2 as-padding-right-space-2 tittel-blokk">
                                                <Tittel :title="tittel" class="as-margin-bottom-space-1" />

                                                <Nominasjon
                                                    v-for="nominasjon in tittel.nominasjoner"
                                                    :key="nominasjon.id"
                                                    class="as-margin-bottom-space-1"
                                                    :nominasjon="nominasjon"
                                                />

                                                <!-- Spørsmål fra avsender og svar fra motaker -->
                                                <SporsmaalSvar
                                                    :nominasjoner="tittel.nominasjoner"
                                                    :spaInteraction="spaInteraction"
                                                />
                                            </div>
                                        </div>
                                    </template>

                                    <template v-if="gruppe.nominasjonerUtenTitler.length">
                                        <div class="as-padding-bottom-space-1 as-padding-space-2">
                                            <div class="as-margin-bottom-space-1 "><strong>Uten tittel</strong></div>
                                            <Nominasjon :nominasjon="nominasjon" v-for="nominasjon in gruppe.nominasjonerUtenTitler" :key="nominasjon.id" class="as-margin-bottom-space-1" />
                                            <SporsmaalSvar
                                                :nominasjoner="gruppe.nominasjonerUtenTitler"
                                                :spaInteraction="spaInteraction"
                                            />
                                        </div>
                                    </template>
                                </div>
                            </v-expand-transition>
                        </div>
                    </div>
                </v-list>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import MainComponent from './MainComponent.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import type Arrangement from './../objects/Arrangement';
import type { PropType } from 'vue';  // Use type-only import for PropType
import InnslagNominasjonGruppe from '../objects/InnslagNominasjonGruppe';
import type Innslag from '../objects/Innslag';
import Fylke from '../objects/Fylke';
import { PermanentNotification } from 'ukm-components-vue3';
import VideresendingNominasjon from '../objects/VideresendingNominasjon';
import VideresendingNominasjonerFilter from './VideresendingNominasjonerFilter.vue';
import Nominasjon from './VideresendingNominasjoner/Nominasjon.vue';
import Tittel from './VideresendingNominasjoner/Tittel.vue';
import SporsmaalSvar from './VideresendingNominasjoner/SporsmaalSvar.vue';
import type TittelType from './VideresendingNominasjoner/Tittel.vue';

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
        PermanentNotification : PermanentNotification,
        VideresendingNominasjonerFilter: VideresendingNominasjonerFilter,
        Nominasjon: Nominasjon,
        Tittel: Tittel,
        SporsmaalSvar: SporsmaalSvar
    },
    mounted() {
        this.fetchNominasjoner();
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            savingOngoing: false,
            innslagGrupper: [] as InnslagNominasjonGruppe[],
            fylker: [] as Fylke[],
            selectedFylkeId: null as number | null,
            innslagTypeValue: null as string | null,
        }
    },
    methods : {
        getFiltrerteNominasjoner(): InnslagNominasjonGruppe[] {
            let list = this.innslagGrupper;
            if (this.selectedFylkeId != null) {
                list = list.filter((gruppe) => gruppe.innslag.fylke_fra_id === this.selectedFylkeId);
            }
            if (this.innslagTypeValue != null && this.innslagTypeValue !== '') {
                list = list.filter((gruppe) => {
                    const typeId = (gruppe.innslag.type_key || gruppe.innslag.innslag_type || '').trim();
                    return typeId === this.innslagTypeValue;
                });
            }
            return list;
        },
        toggleExpandGruppe(gruppe: InnslagNominasjonGruppe) {
            gruppe.expanded = !gruppe.expanded;
        },
        innslagUndertekst(innslag: Innslag): string {
            const parts = [innslag.omrade_navn, innslag.sjanger].filter((s) => (s ?? '').trim().length > 0);
            return parts.join(' · ');
        },
        getStatusColor(status: string): string {
            switch(status) {
                case VideresendingNominasjon.STATUS_GODKJENT:
                    return 'success';
                case VideresendingNominasjon.STATUS_HOS_DELTAKER:
                    return 'warning';
                case VideresendingNominasjon.STATUS_HOS_MOTTAKER:
                    return 'red';
                case VideresendingNominasjon.STATUS_HOS_AVSENDER:
                    return 'info';
            }
            return 'warning';
        },
        convertStatusToReadable(status: string): string {
            switch(status) {
                case VideresendingNominasjon.STATUS_GODKJENT:
                    return 'Godkjent';
                case VideresendingNominasjon.STATUS_HOS_DELTAKER:
                    return 'Venter på deltaker';
                case VideresendingNominasjon.STATUS_HOS_MOTTAKER:
                    return 'Venter på deg';
                case VideresendingNominasjon.STATUS_HOS_AVSENDER:
                    return 'Venter på avsender';
            }
            return 'Ukjent';
        },
        hasSporsmalInNominasjoner(TittelMedNominasjoner: any[]): any {
            for(let nominasjon of TittelMedNominasjoner) {
                if(nominasjon.sporsmal != null && nominasjon.sporsmal.trim() != '') {
                    return nominasjon;
                }
            }

            return null
            
        },
        // handleSwitchChange(leder: Leder|any) {
        //     // Do not allow saving if a save is ongoing
        //     if(this.savingOngoing) {
        //         leder.godkjent = !leder.godkjent;
        //         return;
        //     }

        //     this.save(leder);
        // },
        async fetchNominasjoner() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'nominasjoner/getAlleNominasjoner',
            };

            const results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            this.innslagGrupper = InnslagNominasjonGruppe.parseAlleNominasjonerResponse(results);

            for (const gruppe of this.innslagGrupper) {
                this.addFylkeIfDoesntExist(new Fylke(gruppe.innslag.fylke_fra_id, gruppe.innslag.fylke_fra));
            }

        },
        addFylkeIfDoesntExist(fylke: Fylke) {
            if(this.fylker.find(f => f.id === fylke.id)) {
                return;
            }
            this.fylker.push(fylke);
        }
    }
}
</script>

<style scoped>
.videresending-nominasjon-item {
    cursor: pointer;
}
.innslag-beskrivelse .d-block {
    white-space: pre-wrap;
}
.tittel-blokk {
    border-left: 3px solid rgba(0, 0, 0, 0.12);
    padding-left: 12px;
}
.card-nominasjon-item {
    overflow: hidden;
    box-shadow: none !important;
}
.nominasjon-list {
    background: none !important;
    padding: 0 !important;
}
.innslag-type-chip {
    margin: auto;
    margin-right: 50px;
}

.nominasjon-list {
    background: transparent;
    box-shadow: none !important;
}
.nominasjon-item {
    padding: calc(3*var(--initial-space-box)) !important;
    border: none;
    box-shadow: none !important;
    min-height: 56px !important;
    cursor: pointer;
    border-radius: var(--radius-high) !important;
}
.aktiviteter-buttons {
    padding: var(--initial-space-box) !important;
}
.nominasjon-list :deep(.v-list-item__content) {
    display: flex;
}
.nominasjon-list-expanded {
    font-size: 14px;
}

@media(max-width: 767px) {

}
</style>