<template>
    <div>
        <v-dialog
            transition="dialog-bottom-transition"
            width="auto"
        >
            <template v-slot:activator="{ props: activatorProps }">
            <v-btn
                v-bind="activatorProps"
                class="v-btn-as v-btn-hvit as-margin-right-space-2"
                color="#000"
                rounded="large"
                :size="isMobile ? 'large' : 'x-large'"
                variant="outlined" >
                Tags
            </v-btn>
            </template>

            <template v-slot:default="{ isActive }">
            <v-card>
                <v-toolbar title="Opening from the Bottom"></v-toolbar>

                <v-card-text class="text-h2 pa-12">
                    <p v-for="tag in tags">
                        {{ tag.navn }}
                    </p>
                </v-card-text>

                <v-card-actions class="justify-end">
                <v-btn
                    text="Close"
                    @click="isActive.value = false"
                ></v-btn>
                </v-card-actions>
            </v-card>
            </template>
        </v-dialog>
    </div>
</template>
<script lang="ts">
import type { PropType } from 'vue';  // Use type-only import for PropType
import AktivitetTag from '../../objects/AktivitetTag';

export default {
    computed: {
        isMobile() {
            return window.innerWidth < 576; // Adjust the breakpoint as needed
        },
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            tags : [] as AktivitetTag[],
        }
    },
    mounted() {
        this.init();
    },
    methods : {
        init(){
            this.fetchTags();
        },
        async fetchTags() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'aktivitet/getAlleTags',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
            
            for(let tag of results) {
                this.tags.push(new AktivitetTag(tag.id, tag.navn, tag.beskrivelse))
            }
        },
    }
}

</script>

