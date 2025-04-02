<template>
    <div class="">
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
                Tagger
            </v-btn>
            </template>

            <template v-slot:default="{ isActive }">
            <v-card>
                <v-toolbar title="Alle tagger"></v-toolbar>

                <div class="aktivitet-tags-div">
                    <div v-for="tag in tags" class="as-margin-top-space-2">
                        <div class="col-xs-12 nop-impt">
                            <div class="col-xs-4">
                                <InputTextOverlay :placeholder="'Navn'" v-model="tag.navn" />
                            </div>
                            <div class="col-xs-5 nop-impt">
                                <InputTextOverlay :placeholder="'Beskrivelse'" v-model="tag.beskrivelse" />
                            </div>
                            <div class="col-xs-3 button-tag-item-div">
                                <v-btn
                                    class="v-btn-as v-btn-success as-margin-auto"
                                    rounded="large"
                                    @click="saveOrCreateTag(tag)"
                                    variant="outlined">
                                    {{ tag.id == -1 ? 'Opprett' : 'Lagre' }}
                                </v-btn>
                                <v-btn v-if="tag.id != -1"
                                    class="v-btn-as v-btn-error as-margin-auto"
                                    rounded="large"
                                    @click="deleteTag(tag)"
                                    variant="outlined">
                                    Slett
                                </v-btn>
                            </div>
                        </div>
                        <div class="col-xs-12 as-padding-left-space-2 as-padding-right-space-2">
                            <hr>
                        </div>
                    </div>
                </div>

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
import { InputTextOverlay } from 'ukm-components-vue3';


export default {
    computed: {
        isMobile() {
            return window.innerWidth < 576; // Adjust the breakpoint as needed
        },
    },
    components: {
        InputTextOverlay : InputTextOverlay,
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
        async saveOrCreateTag(tag : AktivitetTag) {
            let isNew = tag.id == -1;

            let results = null;
            if(isNew) {
                results = await tag.create();
            } else {
                results = await tag.save();
            }

            if(results && (<any>results).id == tag.id) {
                this.spaInteraction.showMessage('Lagret!', 'Taggen ble lagret', 'success');
                if(isNew) {
                    this.addNewPlaceholder();
                }
            }
            else {
                this.spaInteraction.showMessage('Noe gikk galt', 'Taggen er ikke lagret!', 'error');
            }

            return results;
        },
        async deleteTag(tag : AktivitetTag) {
            if(tag.id == -1) {
                this.tags = this.tags.filter(t => t.id != -1);
                return;
            }

            let results = await tag.delete();

            if(results && results.completed == true) {
                this.tags = this.tags.filter(t => t.id != tag.id);
                this.spaInteraction.showMessage('Slettet!', 'Taggen ble slettet', 'success');
            }
            else {
                this.spaInteraction.showMessage('Noe gikk galt', 'Taggen er ikke slettet!', 'error');
            }

            return results;
            
        },
        async addNewTag() {
            this.tags.push(new AktivitetTag(-1, '', ''));
        },
        async fetchTags() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'aktivitet/getAlleTags',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
            
            for(let tag of results) {
                this.tags.unshift(new AktivitetTag(tag.id, tag.navn, tag.beskrivelse))
            }

            this.addNewPlaceholder();
        },
        addNewPlaceholder() {
            // If there is no tag with -1
            if(this.tags.filter(tag => tag.id == -1).length == 0) {
                this.tags.unshift(new AktivitetTag(-1, '', ''));
            }
        }
    }
}

</script>
<style scoped>
.aktivitet-tags-div {
    min-width: 50vw;
    height: 80vw;
    max-height: 1200px;
}
.button-tag-item-div {
    height: 60px;
    display: flex;
}
</style>
