<template>
    <div class="as-container main-container">
        <div class="col-sm-8 col-xs-12">
            <div v-if="arrangement != undefined" class="as-card-1 as-padding-space-3 as-margin-bottom-space-2">
                <div class="">
                    <div class="as-margin-bottom-space-4">
                        <h4>Videresendte Arrangemener</h4>
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
                                <div class="right-side-arrangement">
                                    <v-btn
                                        class="ga-til-arrang-btn v-btn-as v-btn-bla"
                                        append-icon="mdi-chevron-right"
                                        rounded="large"
                                        @click="goTilArrangement(vArr.link)"
                                        variant="outlined">
                                        Gå til arrangement
                                    </v-btn>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-4 col-xs-12">
            <div class="as-card-1 as-padding-space-3">
                <div v-for="fylke in alleFylker" :key="fylke.fylkeId" class="as-display-flex fylke-arr as-padding-bottom-space-1 as-margin-bottom-space-1">
                    <div class="as-margin-auto as-margin-left-none">
                        <p>{{ fylke.fylkeName }}</p>
                    </div>
                    <div class="as-margin-auto as-margin-right-none">
                        <v-chip 
                            :color="fylkeVideresendt(fylke.fylkeId) > 0 ? 'success' : 'error'">
                            {{ fylkeVideresendt(fylke.fylkeId) }}
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

const fylkeVideresendt = (fylkeId : number) : number => {
    return videresendteArrangementer.value.filter((arrangement : any) => arrangement.fylkeId == fylkeId).length;
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
.right-side-arrangement {
    margin: auto;
    margin-right: 0;
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
@media(max-width: 767px) {
    .videresendt-arrangement {
        display: block;
    }
    .right-side-arrangement{
        margin-top: var(--initial-space-box);
    }
}
@media(max-width: 576px) {
    .ga-til-arrang-btn {
        width: 100%;
    }
}
</style>