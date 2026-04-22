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
            <div class="as-margin-top-space-1 as-margin-bottom-space-2">
                Antall innslag nominiasjoner {{ modelValue ? 'for ' + fylker.find(f => f.id === modelValue)?.navn : 'for alle fylker' }}: {{ getAntallNominasjonerForFylke() }}
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
        getAntallNominasjonerForFylke(): number {
            if(!this.modelValue || this.modelValue === null) {
                // Returner antall nominiasjoner for alle fylker
                return this.innslaggrupper.length;
            }
            return this.innslaggrupper.filter(gruppe => gruppe.innslag.fylke_fra_id === this.modelValue).length;
        },
    },
});
</script>