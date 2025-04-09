<template>
    <div class="as-container main-container">
        <div class="aktiviteter-buttons as-margin-bottom-space-4 as-display-flex">
            <v-btn
                class="v-btn-as v-btn-hvit as-margin-right-space-2"
                prepend-icon="mdi-plus"
                color="#000"
                rounded="large"
                :size="isMobile ? 'large' : 'x-large'"
                @click="leggTilAktivitet"
                variant="outlined" >
                Legg til Aktivitet
            </v-btn>
            <AktivitetTags :dialogVisible="tagsDialogVisible" :tags="tags" @update:tags="updateTags"/>
            <AktivitetKlokkesletts :dialogVisible="klokkeslettDialogVisible" :klokkeslett="klokkeslett" @update:klokkeslett="updateKlokkeslett"/>
        </div>
        <div class="as-padding-left-space-1 as-padding-right-space-1 as-margin-bottom-space-1">
            <h4>Aktiviteter</h4>
        </div>
        <v-card class="mx-auto aktivitet-card">
            <v-list lines="three" class="aktivitet-list">
                <div class="">
                    <div class="as-card-1 as-padding-space-3 as-margin-bottom-space-2" v-for="aktivitet in aktiviteter" :key="aktivitet.id" v-show="!aktivitet.deleted" @click="toggleExpand(aktivitet)">
                        <v-list-item v-if="!aktivitet.deleted"
                        class="aktivitet-item nop-impt as-card-1 as-padding-space-3"
                        >
                        <v-list-item-title class="text-h6">
                            {{ aktivitet.title }}
                        </v-list-item-title>
                        <v-list-item-subtitle>
                            <span>{{ getAntallTidspunkter(aktivitet) }}</span>
                        </v-list-item-subtitle>
                        <template v-slot:prepend>
                            <v-avatar color="grey-lighten-1">
                            <v-icon color="white">mdi-calendar-star</v-icon>
                            </v-avatar>
                        </template>

                        <template v-slot:append>
                            <v-btn 
                            icon 
                            variant="text" 
                            @click.stop="toggleExpand(aktivitet)"
                            >
                                <v-icon>{{ aktivitet.expanded ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                            </v-btn>
                        </template>
                        </v-list-item>

                        <!-- Expandable Content BELOW the item -->
                        <v-expand-transition>
                            
                            <div v-if="aktivitet.expanded" class="as-display-flex" @click.stop>
                                <AktivitetKomponent :aktivitet="aktivitet" :hendelser="hendelser" :tags="tags" :klokkeslett="klokkeslett" :key="tagsKey"/>
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
import type Arrangement from './../objects/Arrangement';
import type { PropType } from 'vue';  // Use type-only import for PropType
import Leder from './../objects/Leder';
import { PermanentNotification } from 'ukm-components-vue3';
import Aktivitet from './../objects/Aktivitet';
import AktivitetKomponent from './Utils/AktivitetKomponent.vue';
import AktivitetTidspunkt from './../objects/AktivitetTidspunkt';
import AktivitetDeltaker from './../objects/AktivitetDeltaker';
import { InputTextOverlay } from 'ukm-components-vue3';
import AktivitetTag from '../objects/AktivitetTag';
import Hendelse from './../objects/Hendelse';
import AktivitetTags from './Utils/AktivitetTags.vue';
import AktivitetKlokkeslett from '../objects/AktivitetKlokkeslett';
import AktivitetKlokkesletts from './Utils/AktivitetKlokkesletts.vue';

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
        PermanentNotification : PermanentNotification,
        AktivitetKomponent : AktivitetKomponent,
        InputTextOverlay : InputTextOverlay,
        AktivitetTags : AktivitetTags,
        AktivitetKlokkesletts : AktivitetKlokkesletts,
    },
    mounted() {
        this.init();
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            savingOngoing: false,
            aktiviteter : [] as Aktivitet[],
            tagsDialogVisible : false as boolean,
            klokkeslettDialogVisible : false as boolean,
            tags : [] as AktivitetTag[],
            klokkeslett : [] as AktivitetKlokkeslett[],
            tagsKey: 0, // Bruker for reaktiving av AktivitetKomponent
            hendelser: [] as Hendelse[],
        }
    },
    methods : {
        getAntallTidspunkter(aktivitet : Aktivitet) {
            return 'Antall forekomster: ' + (aktivitet.tidspunkter.length-1);  
        },
        toggleExpand(aktivitet : Aktivitet) {
            aktivitet.expanded = !aktivitet.expanded;
        },
        leggTilAktivitet() {
            // Check if there is already an empty aktivitet
            for(let aktivitet of this.aktiviteter) {
                if(aktivitet.id == -1) {
                    aktivitet.expanded = true;
                    return;
                }
            }
            
            let newAktivitet = new Aktivitet(
                -1, 
                '', 
                '', 
                '',
                '',
                -1,
                [],
                [],
                null,
            );
            
            this.aktiviteter.unshift(newAktivitet);
            newAktivitet.expanded = true;
        },
        updateTags(newTags: AktivitetTag[]) {
            this.init();

            // this.tags = newTags;
            // this.tagsKey++; // Trigger re-render of AktivitetKomponent
        },
        updateKlokkeslett(newTags: AktivitetKlokkeslett[]) {
            this.init();

            // this.tags = newTags;
            // this.tagsKey++; // Trigger re-render of AktivitetKomponent
        },
        async fetch() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'aktivitet/getAlleAktiviteter',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
            
            if(results) {
                this.aktiviteter = [];
            }
            
            for(let aktivitet of results) {
                let tidspunkter : AktivitetTidspunkt[] = [];
                let aktivitetTags : AktivitetTag[] = [];

                let aktivitetObj = new Aktivitet(
                    aktivitet.id, 
                    aktivitet.navn, 
                    aktivitet.sted, 
                    aktivitet.beskrivelse,
                    aktivitet.beskrivelseLeder,
                    aktivitet.plId,
                    tidspunkter,
                    aktivitetTags,
                    aktivitet.image,
                );
                this.aktiviteter.push(
                    aktivitetObj
                );

                for(let tag of this.tags) {
                    for(let aktivitetTag of aktivitet.tags) {
                        if(tag.id == aktivitetTag.id) {
                            aktivitetTags.push(tag);
                        }
                    }
                }
            
                for(let tidspunkt of aktivitet.tidspunkter) {
                    let deltakere : AktivitetDeltaker[] = [];

                    for(let deltaker of tidspunkt.deltakere) {
                        deltakere.push(
                            new AktivitetDeltaker(
                                deltaker.mobil,
                            )
                        );
                    }
                    
                    let hendelse = this.hendelser.find((h) => h.id == tidspunkt.hendelseId);
                    console.log('aaa');
                    console.log(hendelse);
                    tidspunkter.push(
                        new AktivitetTidspunkt(
                            tidspunkt.id,
                            tidspunkt.sted,
                            tidspunkt.start,
                            tidspunkt.slutt,
                            tidspunkt.varighet,
                            tidspunkt.maksAntall,
                            hendelse ?? null,
                            aktivitetObj,
                            deltakere,
                            tidspunkt.harPaamelding,
                            tidspunkt.erSammeStedSomAktivitet,
                            tidspunkt.erKunInterne)
                    );
                }
            };
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

            console.log(hendelser);
    
            this.hendelser = hendelser;
    
            return results;
        },
        async fetchAlleTags() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'aktivitet/getAlleTags',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
            
            let tags : AktivitetTag[] = [];
            for(let tag of results) {
                tags.push(new AktivitetTag(
                    tag.id, 
                    tag.navn, 
                    tag.beskrivelse,
                ));
            }

            this.tags = tags;

            return results;
        },
        async fetchAlleKlokkeslett() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'aktivitet/klokkeslett/getAlle',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
            
            let kSletts : AktivitetKlokkeslett[] = [];
            for(let kSlett of results) {
                kSletts.push(new AktivitetKlokkeslett(
                    kSlett.id, 
                    kSlett.navn,
                    kSlett.start, 
                    kSlett.stop,
                ));
            }

            this.klokkeslett = kSletts;

            return results;
        },
        async save(leder : Leder) {
            this.savingOngoing = true;
        
            let res;
            try {
                res = await leder.save();
            } catch(e) {
                this.spaInteraction.showMessage('Feil', 'En feil oppstod under lagring av godkjenning', 'error');
            }

            if(res.success == true) {
                this.spaInteraction.showMessage('Lagret', 'Godkjenningsstatus er lagret', 'success');
            } else {
                this.spaInteraction.showMessage('Feil', 'En feil oppstod under lagring av godkjenning', 'error');
            }

     
            this.savingOngoing = false;
        },
        async init() {
            let tags = await this.fetchAlleTags();
            let hendelserRes = await this.fetchAlleHendelser();
            let klokkeslettRes = await this.fetchAlleKlokkeslett();

            // Først må taggene hentes
            if(tags != null && hendelserRes != null && klokkeslettRes != null) {
                this.fetch();
            }
        }
    }
}
</script>

<style scoped>
.aktivitet-list,
.aktivitet-card {
    background: transparent;
    box-shadow: none !important;
}
.aktivitet-item {
    border: none;
    box-shadow: none !important;
    min-height: 56px !important;
    cursor: pointer;
}
.aktivitet-list,
.aktiviteter-buttons {
    padding: var(--initial-space-box) !important;
}
@media(max-width: 767px) {

}

</style>
<style>
.ql-toolbar.ql-snow {
    border-radius: var(--radius-normal) !important;
    border-bottom-left-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
    background: var(--color-primary-grey-lightest) !important;
    border: none !important;
    border-bottom: solid 1px var(--color-primary-grey-light) !important;
}
.ql-container.ql-snow {
    border-radius: var(--radius-normal) !important;
    background: #fff !important;
    border-top-left-radius: 0 !important;
    border-top-right-radius: 0 !important;
    border: solid 1px var(--color-primary-grey-lightest) !important;
    
}
</style>