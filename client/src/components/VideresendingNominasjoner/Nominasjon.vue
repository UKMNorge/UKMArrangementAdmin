<template>
    <div class="nominasjon-row as-padding-space-1">
        <div class="d-flex align-center justify-space-between">
            <div class="text-medium-emphasis">
                Nominasjon #{{ nominasjon.id }}
            </div>
            <v-chip size="x-small" variant="outlined" :color="statusColor">
                {{ statusReadable }}
            </v-chip>
        </div>

        <div v-if="nominasjon.beskrivelse" class="text-body-2 as-margin-top-space-1">
            {{ nominasjon.beskrivelse }}
        </div>

        <div v-if="nominasjon.sporsmal || nominasjon.svar" class="text-body-2 as-margin-top-space-1">
            <div v-if="nominasjon.sporsmal" class="text-medium-emphasis">
                {{ nominasjon.sporsmal }}
            </div>
            <div v-if="nominasjon.svar">
                {{ nominasjon.svar }}
            </div>
        </div>

        <div class="nominasjon-person as-margin-top-space-2">
            <div class="text-medium-emphasis">Deltaker</div>
            <div class="as-padding-left-space-2">
                <div class="text-body-2">
                    Navn:{{ nominasjon.person?.fornavn }} {{ nominasjon.person?.etternavn }}
                </div>
                <div class="text-body-2">
                    Mobil:{{ nominasjon.person?.mobil }}
                </div>
                <div class="text-body-2">
                    Alder:{{ nominasjon.person?.alder }}
                </div>
            </div>
        </div>

    </div>
</template>

<script lang="ts">
import type { PropType } from 'vue';
import VideresendingNominasjon from '../../objects/VideresendingNominasjon';

export default {
    props: {
        nominasjon: {
            type: Object as PropType<VideresendingNominasjon>,
            required: true,
        },
    },
    computed: {
        statusReadable(): string {
            switch (this.nominasjon.getStatus()) {
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
        statusColor(): string {
            switch (this.nominasjon.getStatus()) {
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
    },
};
</script>

<style scoped>
.nominasjon-row {
    border: 1px solid #e0e0e0;
    padding: 10px;
    border-radius: 10px;
    background: var(--color-primary-bla-25);
}
</style>

