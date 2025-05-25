<template>
    <div class="as-container main-container">
        <div v-if="fetched" v-for="hendelseGruppe in hendelserGrupper" class="as-card-1 nop-impt as-margin-bottom-space-2 as">
            <div class="as-padding-space-3">
                <h4>{{ hendelseGruppe.navn }}</h4>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import VueDatePicker from '@vuepic/vue-datepicker';
import type { PropType } from 'vue';  // Use type-only import for PropType
import type { Arrangement } from '../objects/Arrangement'; // Assuming you have an Arrangement type defined

export default {
    computed: {
        isMobile() {
            return window.innerWidth < 576; // Adjust the breakpoint as needed
        },
    },
    props: {
        arrangement: {
            type: Object as PropType<Arrangement>,
            required: true
        },
    },
    components: {
        VueDatePicker : VueDatePicker,
    },
    mounted() {
        this.init();
    },
    data() {
        return {
            fetched : false as boolean,
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            hendelserGrupper: [] as any[],
        }
    },
    methods : {
        init() {
            console.log('Hendelser component mounted');
            this.fetch();
        },
        async fetch() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'hendelser/getAlleHendelseGrupper',
                
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            this.hendelserGrupper = results || [];

            console.log('Fetched hedelserGrupper:', results);
            this.fetched = true;
        }
    }
}
</script>

<style scoped>

</style>