<template>
    <div class="innslag-tittel">
        <div class="text-h6 innslag-tittel__navn">
            Navn: {{ title?.navn }}
        </div>
        <div class="text-body-2 text-medium-emphasis innslag-tittel__meta">
            Varighet: {{ title?.varighet }}
        </div>
        <div class="text-body-2 text-medium-emphasis innslag-tittel__meta">
            Selvlaget: {{ title?.selvlaget }}
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import type { PropType } from 'vue';
import type { TittelMedVideresendingNominasjoner } from '../../objects/InnslagNominasjonGruppe';

export default defineComponent({
    name: 'Tittel',
    props: {
        title: {
            type: Object as PropType<TittelMedVideresendingNominasjoner>,
            required: true,
        },
    },
    computed: {
        visningsNavn(): string {
            const navn = (this.title?.navn ?? '').trim();
            return navn.length ? navn : 'Uten navn';
        },
        metaTekst(): string {
            const id = Number(this.title?.id);
            if (!Number.isFinite(id) || id <= 0) {
                return '';
            }
            return `Tittel-ID: ${id}`;
        },
    },
});
</script>

<style scoped>
.innslag-tittel {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.innslag-tittel__navn {
    line-height: 1.2;
}

.innslag-tittel__meta {
    line-height: 1.2;
}
</style>
