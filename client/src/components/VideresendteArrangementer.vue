<template>
    <div class="as-container main-container">
        <div class="col-lg-8 col-sm-12">
            <div v-if="arrangement != undefined" class="as-card-1 as-padding-space-3 as-margin-bottom-space-2">
                <div class="">
                    <div class="as-margin-bottom-space-4">
                        <h4>Arrangementer som har sendt noe videre</h4>
                    </div>
    
                    <div v-if="videresendteArrangementer.length < 1" class="as-card-2 videresendt-arrangement nosh-impt as-padding-space-2 as-margin-top-space-2">
                        <p>Ingen arrangementer er videresendt</p>
                    </div>
    
                    <div>
                        <div v-for="vArr in videresendteArrangementer">
                            <div class="as-card-2 as-display-flex videresendt-arrangement nosh-impt as-padding-space-2 as-margin-top-space-2">
                                <div class="left-side-arrangement as-margin-right-space-1">
                                    <h5>{{ vArr.arrangementName }}</h5>
                                    <p>{{ vArr.fylkeName }}</p>
                                </div>
                                <div class="middle-side-arrangement">
                                    <v-chip
                                        :append-icon="vArr.antallVideresendteInnslag > 0 ? 'mdi-checkbox-marked-circle' : 'mdi-alert'"
                                        class="innslag-antall-chip as-margin-auto"
                                        :color="vArr.antallVideresendteInnslag > 0 ? 'success' : 'warning'"
                                        >
                                        {{ vArr.antallVideresendteInnslag }} innslag videresendt
                                    </v-chip>
                                    <v-chip
                                        :append-icon="vArr.antallVideresendteInnslag > 0 ? 'mdi-checkbox-marked-circle' : 'mdi-alert'"
                                        class="innslag-antall-chip show-on-xs as-margin-auto"
                                        :color="vArr.antallVideresendteInnslag > 0 ? 'success' : 'warning'"
                                        >
                                        <v-tooltip open-delay="200" activator="parent" location="start">
                                            {{ vArr.antallVideresendteInnslag }} innslag videresendt
                                        </v-tooltip>
                                        {{ vArr.antallVideresendteInnslag }}
                                    </v-chip>
                                </div>
                                <div class="right-side-arrangement">
                                    <v-btn
                                        class="ga-til-arrang-btn v-btn-as v-btn-bla"
                                        append-icon="mdi-chevron-right"
                                        rounded="large"
                                        @click="goTilArrangement(vArr.link)"
                                        variant="outlined">
                                        Til arrangement
                                    </v-btn>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-sm-12">
            <div class="as-card-1 as-padding-space-3">
                <div class="as-margin-bottom-space-4">
                    <h4>Fylke videresending oversikt</h4>
                </div>
                <div v-for="fylke in alleFylker" :key="fylke.fylkeId" class="as-display-flex fylke-arr as-padding-bottom-space-1 as-margin-bottom-space-1">
                    <div class="as-margin-auto as-margin-left-none">
                        <p>{{ fylke.fylkeName }}</p>
                    </div>
                    <div class="as-margin-auto as-margin-right-none">
                        <v-chip 
                            v-bind="props"
                            :color="fylkeColor(fylke.fylkeId)">
                            {{ faktiskeAntallVideresendte(fylke.fylkeId) + ' av ' + antallFylkeVideresendt(fylke.fylkeId) }}
                            <v-tooltip open-delay="200" activator="parent" location="start">
                                <template v-if="antallFylkeVideresendt(fylke.fylkeId) > 0">
                                    <template v-if="faktiskeAntallVideresendte(fylke.fylkeId) == antallFylkeVideresendt(fylke.fylkeId)">
                                        Alle arrangementer har videresendt innslag
                                    </template>
                                    <template v-else-if="faktiskeAntallVideresendte(fylke.fylkeId) == 0">
                                        Ingen av arrangementene har videresendt innslag
                                    </template>
                                    <template v-else>
                                        {{ faktiskeAntallVideresendte(fylke.fylkeId) + ' av ' + antallFylkeVideresendt(fylke.fylkeId) + ' arrangementer har videresendt innslag' }}
                                    </template>
                                </template>
                                <template v-else>
                                    Du har ikke åpnet videresending for {{ fylke.fylkeName }}
                                </template>
                            </v-tooltip>
                        </v-chip>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import '@vuepic/vue-datepicker/dist/main.css';
import type Arrangement from './../objects/Arrangement';
import type { PropType } from 'vue';  // Use type-only import for PropType
import { ref } from 'vue';

const videresendteArrangementer = ref([] as any);
const alleFylker = ref([] as any);

const props = defineProps({ 
    arrangement: {
        type: Object as PropType<Arrangement>,
        required: true
    }
}); 

const antallFylkeVideresendt = (fylkeId : number) : number => {
    return videresendteArrangementer.value.filter((arrangement : any) => arrangement.fylkeId == fylkeId).length;
}

const faktiskeAntallVideresendte = (fylkeId : number) : number => {
    return videresendteArrangementer.value.filter((arrangement : any) => arrangement.fylkeId == fylkeId && arrangement.antallVideresendteInnslag > 0).length;
}

const fylkeColor = (fylkeId : number) : string => {
    let faktiskeVideresendte = faktiskeAntallVideresendte(fylkeId);
    let muligeVideresendte = antallFylkeVideresendt(fylkeId);
    
    if(muligeVideresendte == 0) {
        return 'error';
    }

    if(faktiskeVideresendte > 0 && faktiskeVideresendte < muligeVideresendte) {
        return 'warning';
    }
    return faktiskeVideresendte == muligeVideresendte ? 'success' : 'error';
}


const fetchFylker = async () => {
    var data = {
        action: 'UKMArrangementAdmin_ajax',
        controller: 'getAlleFylker',
    };

    var results = await (<any>window).spaInteraction.runAjaxCall('/', 'POST', data);
    
    if(results.length == 0) {
        return;
    }
    
    for(let fylke of results) {
        alleFylker.value.push(fylke);
    }
}

const goTilArrangement = (link : string) => {
    window.open(link + 'wp-admin', '_blank');
};

const fetchData = async () => {
    var data = {
        action: 'UKMArrangementAdmin_ajax',
        controller: 'getVideresendteArrangementer',
    };

    var results = await (<any>window).spaInteraction.runAjaxCall('/', 'POST', data);
    
    if(results.length == 0) {
        return;
    }
    

    for(let arrangement of results) {
        videresendteArrangementer.value.push(arrangement);
    }
    
}

fetchFylker();
fetchData();
   
</script>

<style scoped>
.videresendt-arrangement {
    background: var(--color-primary-grey-lightest);
}
.left-side-arrangement, 
.right-side-arrangement,
.middle-side-arrangement {
    width: 33%;
}
.middle-side-arrangement {
    padding-left: calc(var(--initial-space-box) * 2);
    border-left: solid 1px #dfdfdf;
    margin-left: calc(var(--initial-space-box) * 2);
    padding-right: calc(var(--initial-space-box) * 2);;
    margin-right: calc(var(--initial-space-box) * 2);
    border-right: solid 1px #dfdfdf;
    display: flex
}
.right-side-arrangement {
    margin: auto;
    margin-right: 0;
    display: flex;
}
.fylke-arr {
    border-bottom: solid 1px var(--color-primary-grey-light);
}
.fylke-arr:last-child {
    margin-top: 0 !important;
    padding-top: 0 !important;
}
.fylke-arr:last-child {
    border-bottom: none;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
.ga-til-arrang-btn {
    margin: auto;
    margin-right: 0;
}
.innslag-antall-chip.show-on-xs {
    display: none;
}
@media(max-width: 767px) {
    .videresendt-arrangement {
        display: block;
        position: relative;
        min-height: 140px;
    }
    .right-side-arrangement{
        position: absolute;
        width: 100%;
        padding: calc(var(--initial-space-box) * 2);
        padding-top: calc(var(--initial-space-box) * 1);
        bottom: 0;
        left: 0;
        right: 0;
    }
    .middle-side-arrangement {
        position: absolute;
        right: 0;
        top: 0;
        width: auto !important;
        padding-right: 0;
        padding-top: 8px;
        border: none !important;
    }
    .middle-side-arrangement .innslag-antall-chip {
        margin-top: var(--initial-space-box);
        margin-bottom: var(--initial-space-box);
        margin-left: 0;
        margin-right: auto;
    }
    .left-side-arrangement, 
    .right-side-arrangement,
    .middle-side-arrangement {
        width: 100%;
    }
    .left-side-arrangement {
        max-width: 80%;
    }
    .ga-til-arrang-btn {
        width: 100%;
    }
}
@media(max-width: 576px) {
    .left-side-arrangement {
        max-width: 48vw;
    }
    .innslag-antall-chip {
        display: none;
    }
    .innslag-antall-chip.show-on-xs {
        display: flex;
    }
}

</style>