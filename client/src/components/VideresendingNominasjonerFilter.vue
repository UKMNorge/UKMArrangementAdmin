<template>
    <div>
        <div class="nominasjoner-filter-div">
            <div class="col-xs-6">
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
            <div class="col-xs-6">
                <v-select
                    :model-value="innslagTypeValue"
                    @update:model-value="(v) => $emit('update:innslagTypeValue', v ?? null)"
                    label="Velg innslagstype for filtrering"
                    variant="outlined"
                    class="v-autocomplete-arr-sys"
                    clearable
                    :items="innslagTypeItems"
                    item-title="title"
                    item-value="value"
                />
            </div>
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
import type InnslagNominasjonGruppe from '../objects/InnslagNominasjonGruppe';

export type InnslagTypeFilterItem = { title: string; value: string };

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
        innslagTypeValue: {
            type: String as PropType<string | null>,
            required: true,
        },
    },
    emits: ['update:modelValue', 'update:innslagTypeValue'],
    computed: {
        innslagTypeItems(): InnslagTypeFilterItem[] {
            const seen = new Set<string>();
            const items: InnslagTypeFilterItem[] = [];
            for (const gruppe of this.innslaggrupper) {
                const inv = gruppe.innslag;
                const value = (inv.type_key || inv.innslag_type || '').trim();
                if (!value || seen.has(value)) {
                    continue;
                }
                seen.add(value);
                items.push({
                    title: (inv.innslag_type || inv.type_key || value).trim() || value,
                    value,
                });
            }
            items.sort((a, b) => a.title.localeCompare(b.title, 'nb'));
            return items;
        },
        filtrerteInnslaggrupper(): InnslagNominasjonGruppe[] {
            let list = this.innslaggrupper;
            if (this.modelValue != null) {
                list = list.filter((gruppe) => gruppe.innslag.fylke_fra_id === this.modelValue);
            }
            if (this.innslagTypeValue != null && this.innslagTypeValue !== '') {
                list = list.filter((gruppe) => {
                    const typeId = (gruppe.innslag.type_key || gruppe.innslag.innslag_type || '').trim();
                    return typeId === this.innslagTypeValue;
                });
            }
            return list;
        },
    },
    methods: {
        getAntallInnslagNominasjonerForFylke(): number {
            return this.filtrerteInnslaggrupper.length;
        },
        getAntallNominasjonerForFylke(): number {
            let antall = 0;
            for (const gruppe of this.filtrerteInnslaggrupper) {
                antall += gruppe.getAntallNominasjoner();
            }
            return antall;
        },
    },
});
</script>