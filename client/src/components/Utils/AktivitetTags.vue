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
                    <div v-for="tag in localTags" class="as-margin-top-space-2">
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
    props: {
        tags: {
            type: Array as PropType<AktivitetTag[]>,
            required: true
        },
    },
    computed: {
        isMobile() {
            return window.innerWidth < 576; // Adjust the breakpoint as needed
        },
    },
    mounted() {
        
    },
    components: {
        InputTextOverlay : InputTextOverlay,
    },
    data() {
        return {
            localTags: [...this.tags], // Create a copy of the array
            spaInteraction: (<any>window).spaInteraction,
        };
    },
    watch: {
        tags: {
            handler(newTags) {
                this.localTags = [...newTags]; // Sync changes if parent updates `tags`
                this.addNewPlaceholder();
            },
            deep: true,
        },
    },
    methods: {
        async saveOrCreateTag(tag: AktivitetTag) {
            let isNew = tag.id === -1;
            let results = isNew ? await tag.create() : await tag.save();

            if (results && (<any>results).id === tag.id) {
                this.spaInteraction.showMessage('Lagret!', 'Taggen ble lagret', 'success');
            } else {
                this.spaInteraction.showMessage('Noe gikk galt', 'Taggen er ikke lagret!', 'error');
            }

            this.emitUpdate();
        },
        async deleteTag(tag: AktivitetTag) {
            if (tag.id === -1) {
                this.localTags = this.localTags.filter(t => t.id !== -1);
                return;
            }

            let results = await tag.delete();
            if (results && results.completed === true) {
                this.localTags = this.localTags.filter(t => t.id !== tag.id);
                this.deleteTagFromArray(tag);
                this.spaInteraction.showMessage('Slettet!', 'Taggen ble slettet', 'success');
            } else {
                this.spaInteraction.showMessage('Noe gikk galt', 'Taggen er ikke slettet!', 'error');
            }
        },
        emitUpdate() {
            // this.localTags.push(new AktivitetTag(-1, '', ''));
            this.$emit('update:tags', this.localTags);
        },
        addNewPlaceholder() {
            console.log('')
            // if (!this.localTags.some(tag => tag.id === -1)) {
                this.localTags.unshift(new AktivitetTag(-1, '', ''));
            // }
        },
        async deleteTagFromArray(tag: AktivitetTag) {
            this.$emit('update:tags', this.localTags);
        },
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
