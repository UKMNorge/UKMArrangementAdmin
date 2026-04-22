<template>
    <div class="">
        <div class="nominasjon-row as-padding-space-2">
            <div class="d-flex">
                <v-chip
                    size="small"
                    class="as-margin-right-space-1"
                    color="primary"
                    variant="outlined">
                    Nominasjon
                </v-chip>
                <v-chip
                    size="small"
                    class="as-margin-right-space-1"
                    :color="statusColor"
                    variant="outlined">
                    {{ statusReadable }}
                </v-chip>
            </div>

            <div v-if="nominasjon.beskrivelse" class="text-body-2 as-margin-top-space-1">
                {{ nominasjon.beskrivelse }}
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

            <template v-if="isGodkjenningPossible">
                <div class="as-margin-top-space-2">
                    <v-btn
                        class="v-btn-as v-btn-success"
                        rounded="small"
                        size="small"
                        @click="godkjennNominasjon()"
                        variant="outlined">
                        Godkjenn
                    </v-btn>
                </div>
            </template>

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
    data() {
        return {
            spaInteraction: (<any>window).spaInteraction, // Definert i main.ts
        };
    },
    computed: {
        isGodkjenningPossible(): boolean {
            return this.nominasjon.getStatus() == VideresendingNominasjon.STATUS_HOS_MOTTAKER;
        },
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
    methods: {
        async godkjennNominasjon() {
            let res = await this.spaInteraction.runAjaxCall('/', 'POST', {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'nominasjoner/sendTilDeltaker',
                nominasjonId: this.nominasjon?.id,
            });
            
            if (res?.success) {
                this.nominasjon.status = VideresendingNominasjon.STATUS_HOS_DELTAKER;
            }
        },
    },
};
</script>

<style scoped>
.nominasjon-row {
    border: 1px solid none;
    border-radius: var(--radius-normal);
    background: var(--as-color-primary-primary-lightest);
}
</style>

