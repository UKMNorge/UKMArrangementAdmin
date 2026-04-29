<template>
    <div v-if="nominasjonMedSporsmal">
        <hr />

        <div>
            <PermanentNotification
                v-if="nominasjonMedSporsmal.sporsmal != null && String(nominasjonMedSporsmal.sporsmal).trim() !== ''"
                class="arrangement-kvoter-msg"
                typeNotification="warning"
                :tittel="`Spørsmål fra avsender`"
                :isHTML="true"
                :description="nominasjonMedSporsmal.sporsmal"
            />
        </div>

        <div class="text-body-3 as-margin-top-space-2">
            <v-textarea
                class="v-text-field-arr-sys"
                label="Ditt svar eller din kommentar"
                variant="outlined"
                v-model="svar"
                :disabled="saving"
            />
        </div>

        <div class="as-margin-bottom-space-1">
            <v-btn
                class="v-btn-as v-btn-bla"
                rounded="small"
                size="small"
                :disabled="saving || !canSend"
                @click="sendSvar()"
                variant="outlined"
            >
                {{ saving ? 'Sender…' : 'Send svar' }}
            </v-btn>
        </div>

        <div v-if="statusMessage" class="as-margin-top-space-1 as-margin-bottom-space-1">
            <PermanentNotification
                class="arrangement-kvoter-msg"
                :typeNotification="statusType"
                :tittel="statusTitle"
                :isHTML="false"
                :description="statusMessage"
            />
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import type { PropType } from 'vue';
import { PermanentNotification } from 'ukm-components-vue3';
import type VideresendingNominasjon from '../../objects/VideresendingNominasjon';

type StatusType = 'success' | 'warning' | 'info' | 'error';

export default defineComponent({
    name: 'SporsmaalSvar',
    components: {
        PermanentNotification,
    },
    props: {
        nominasjoner: {
            type: Array as PropType<VideresendingNominasjon[]>,
            required: true,
        },
        spaInteraction: {
            type: Object as PropType<any>,
            required: true,
        },
    },
    data() {
        return {
            svar: '' as string,
            saving: false as boolean,
            statusMessage: '' as string,
            statusType: 'info' as StatusType,
            statusTitle: '' as string,
        };
    },
    computed: {
        nominasjonMedSporsmal(): VideresendingNominasjon | null {
            let nominasjonMS = null;
            for (const nominasjon of this.nominasjoner ?? []) {
                if (nominasjon?.sporsmal != null && String(nominasjon.sporsmal).trim() !== '') {
                    return nominasjon;
                }
                nominasjonMS = nominasjon;
            }
            return nominasjonMS;
        },
        canSend(): boolean {
            if (!this.nominasjonMedSporsmal) {
                return false;
            }
            return String(this.svar ?? '').trim().length > 0;
        },
    },
    watch: {
        nominasjonMedSporsmal: {
            immediate: true,
            handler(n: VideresendingNominasjon | null) {
                this.svar = n?.svar ?? '';
                this.statusMessage = '';
            },
        },
    },
    methods: {
        async sendSvar() {
            if (!this.nominasjonMedSporsmal || this.saving) {
                return;
            }

            this.saving = true;
            this.statusMessage = '';

            try {
                const data = {
                    action: 'UKMArrangementAdmin_ajax',
                    controller: 'nominasjoner/sendSvar',
                    nominasjonId: this.nominasjonMedSporsmal.id,
                    svar: this.svar,
                };

                const res = await this.spaInteraction.runAjaxCall('/', 'POST', data);

                if (res?.success) {
                    this.nominasjonMedSporsmal.svar = this.svar;
                    this.statusType = 'success';
                    this.statusTitle = 'Svar sendt';
                    this.statusMessage = res?.message ?? 'Svar ble lagret.';
                    this.$emit('saved', { nominasjonId: this.nominasjonMedSporsmal.id, svar: this.svar });
                } else {
                    this.statusType = 'error';
                    this.statusTitle = 'Kunne ikke sende';
                    this.statusMessage = res?.message ?? 'Ukjent feil ved lagring av svar.';
                }
            } catch (e: any) {
                this.statusType = 'error';
                this.statusTitle = 'Kunne ikke sende';
                this.statusMessage = e?.message ?? 'Ukjent feil ved lagring av svar.';
            } finally {
                this.saving = false;
            }
        },
    },
    emits: ['saved'],
});
</script>