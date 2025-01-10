<template>
    <div class="as-container main-container">
        <div v-if="arrangement != undefined" class="col-sm-8 col-xs-12">
            <div class="as-card-1 as-padding-space-3 as-margin-bottom-space-2">
                <div class="as-margin-bottom-space-4">
                    <h4 class="">Økonomi og kvoter for den nasjonale UKM-festivalen</h4>
                </div>
                
                <div class="kvote-item as-margin-top-space-2">
                    <v-text-field
                        :rules="[rules.required, rules.isNumber]" 
                        v-model="arrangement.kvote_deltakere"
                        label="Hva er ordinær deltakerkvote?" 
                        prepend-icon="mdi-account-star"
                        class="v-text-field-arr-sys"
                        variant="outlined">
                    </v-text-field>
                </div>

                <div class="kvote-item as-margin-top-space-2">
                    <v-text-field
                        :rules="[rules.required, rules.isNumber]" 
                        v-model="arrangement.kvote_ledere"
                        label="Hva er ordinær lederkvote?" 
                        prepend-icon="mdi-account-tie"
                        class="v-text-field-arr-sys"
                        variant="outlined">
                    </v-text-field>
                </div>
                
                <div class="kvote-item as-margin-top-space-2">
                    <v-text-field
                        :rules="[rules.required, rules.isNumber]" 
                        prefix="kr."
                        v-model="arrangement.avgift_ordinar"
                        label="Hva er ordinær deltakeravgift?" 
                        prepend-icon="mdi-invoice-list"
                        class="v-text-field-arr-sys"
                        variant="outlined">
                    </v-text-field>
                </div>
               
                <div class="kvote-item as-margin-top-space-2">
                    <v-text-field
                        :rules="[rules.required, rules.isNumber]" 
                        prefix="kr."
                        v-model="arrangement.avgift_subsidiert"
                        label="Hva er subsidiert deltakeravgift?" 
                        prepend-icon="mdi-invoice-minus"
                        class="v-text-field-arr-sys"
                        variant="outlined">
                    </v-text-field>
                </div>
               
                <div class="kvote-item as-margin-top-space-2">
                    <v-text-field
                        :rules="[rules.required, rules.isNumber]" 
                        prefix="kr."
                        v-model="arrangement.avgift_reise"
                        label="Hva er reiseavgift?" 
                        prepend-icon="mdi-train-bus"
                        class="v-text-field-arr-sys"
                        variant="outlined">
                    </v-text-field>
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
        <div class="col-sm-4 col-xs-12">
            <div class="as-card-1 as-padding-space-3 as-margin-bottom-space-2">
                <PermanentNotification 
                    class="arrangement-kvoter-msg"
                    typeNotification="info" 
                    :tittel="`Oversikt over videresendte ledere`" 
                    :isHTML="true"
                    :description="`<p>Husk å sjekke ut siden <a class='as-btn-ahref' href='?page=UKMarrangement_videresending'>Videresending</a> hvor du blant annet finner innstillinger for ledere 
                    <br><br>Du kan finne mer informasjon om ledere på <a class='as-btn-ahref' href='?page=UKMrapporter&action=rapportVue&rapportId=ledereOversikt'>leder rapporten</a></p>`" 
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import '@vuepic/vue-datepicker/dist/main.css';
import type Arrangement from './../objects/Arrangement';
import type { PropType } from 'vue';  // Use type-only import for PropType
import { computed, ref } from 'vue';
import { PermanentNotification } from 'ukm-components-vue3';

const props = defineProps({ 
    arrangement: {
        type: Object as PropType<Arrangement>,
        required: true
    }
});

const rules = computed(() => ({
    required: (value: string) => !!value || 'Feltet er påkrevd',
    isNumber: (value: string) => !isNaN(Number(value)) || 'Verdien må være et tall',
}));

const save = () => {
    props.arrangement.save();
}



</script>

<style scoped>

</style>

<style>
.v-text-field-arr-sys .v-text-field__prefix {
    min-height: 33px !important;
    padding-top: 0;
    padding-bottom: 0;
}
.arrangement-kvoter-msg > .vue-componment-notification-message {
    margin: 0 !important;
}
@media(max-width: 782px) {
    .v-text-field-arr-sys .v-text-field__prefix {
        min-height: 40px !important;
    }
}

    
</style>