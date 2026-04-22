<template>
    <div>
        <div class="nominasjoner-filter-div">
            <v-select
                :model-value="modelValue"
                @update:model-value="(v) => $emit('update:modelValue', v ?? null)"
                label="Velg fylke for filtrering"
                variant="outlined"
                class="v-autocomplete-arr-sys"
                clearable
                :items="fylker.map((f) => ({ title: f.navn, value: f.id }))"
            />
            <div class="as-margin-top-space-1">
                Antall nominerte innslag {{ modelValue ? 'for ' + fylker.find(f => f.id === modelValue)?.navn : 'for alle fylker' }}: {{ getAntallInnslagNominasjonerForFylke() }}
            </div>
            <div class="as-margin-top-space-1 as-margin-bottom-space-3">
                Antall nominerte deltakere {{ modelValue ? 'for ' + fylker.find(f => f.id === modelValue)?.navn : 'for alle fylker' }}: {{ getAntallNominasjonerForFylke() }}
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import type { PropType } from 'vue';
import type Fylke from '../objects/Fylke';
import InnslagNominasjonGruppe from '../objects/InnslagNominasjonGruppe';


export default defineComponent({
    name: 'VideresendingNominasjonerFilter',
    props: {
        fylker: {
            type: Array as PropType<Fylke[]>,
            required: true,
        },
        modelValue: {
            type: Number as PropType<number | null>,
            required: true,
        },
        innslaggrupper: {
            type: Array as PropType<InnslagNominasjonGruppe[]>,
            required: true,
        },
    },
    mounted() {
        console.log(this.innslaggrupper);
    },

    emits: ['update:modelValue'],
    data() {
        return {
        };
    },
    methods: {
        getAntallInnslagNominasjonerForFylke(): number {
            if(!this.modelValue || this.modelValue === null) {
                // Returner antall nominiasjoner for alle fylker
                return this.innslaggrupper.length;
            }
            return this.innslaggrupper.filter(gruppe => gruppe.innslag.fylke_fra_id === this.modelValue).length;
        },
        getAntallNominasjonerForFylke(): number {
            let antall = 0;
            if(!this.modelValue || this.modelValue === null) {
                for (const gruppe of this.innslaggrupper) {
                    antall += gruppe.getAntallNominasjoner();
                }
                return antall;
            }
            
            for (const gruppe of this.innslaggrupper) {
                if(gruppe.innslag.fylke_fra_id === this.modelValue) {
                    antall += gruppe.getAntallNominasjoner();
                }
            }
            return antall;
        },
    },
});
</script>