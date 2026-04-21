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
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import type { PropType } from 'vue';
import type Fylke from '../objects/Fylke';

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
    },
    emits: ['update:modelValue'],
});
</script>