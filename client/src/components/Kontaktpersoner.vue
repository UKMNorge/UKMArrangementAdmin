<template>
    <div class="as-container main-container col-xs-12">
        <div class="kontaktpersoner-buttons as-margin-bottom-space-4 as-display-flex">
            <v-btn
                class="v-btn-as v-btn-hvit as-margin-right-space-2"
                prepend-icon="mdi-plus"
                color="#000"
                rounded="large"
                :size="isMobile ? 'large' : 'x-large'"
                @click="nyKontaktperson()"
                variant="outlined" >
                Legg til kontaktperson
            </v-btn>
        </div>

        <div class="">
            <h4>Kontaktpersoner</h4>
        </div>
        

        <v-card class="mx-auto h-g-card as-margin-top-space-4">
            <v-list class="h-g-list nop-impt as-display-flex as-display-flex-wrap">
                <div v-for="kontaktperson in kontaktpersoner" :key="kontaktperson.id" class="kontakt-gruppe-item as-margin-top-space-2">
                    <div class="hg-item as-card-1 as-padding-space-4 kontaktperson-card">
                        <v-btn
                            icon="mdi-close"
                            variant="text"
                            size="x-small"
                            class="kontaktperson-remove-btn"
                            @click="removeKontaktpersonFromArrangement(kontaktperson)"
                        ></v-btn>
                        <div class="as-display-flex as-display-flex-wrap kontaktperson-fields">
                            <template v-if="kontaktperson.id == -1">
                                <div class="kontaktperson-image as-margin-bottom-space-2">
                                    <v-icon size="100px" class="as-margin-auto" color="#386e9e">mdi-account-circle</v-icon>
                                </div>
                                <div class="kontaktperson-content">
                                    <div v-if="kontaktperson.editing">
                                        <div class="col-xs-12 nop-impt">
                                            <v-text-field
                                                v-model="kontaktperson.mobil"
                                                variant="outlined"
                                                maxlength="8"
                                                @input="checkMobil(kontaktperson)"
                                                class="v-text-field-arr-sys"
                                                label="Mobilnummer"
                                            ></v-text-field>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <div class="kontaktperson-image as-margin-bottom-space-2">
                                    <img
                                        v-if="kontaktperson.profile_image_url"
                                        :src="kontaktperson.profile_image_url"
                                        alt=""
                                        class="kontaktperson-image-clickable"
                                        @click="openImagePopup(kontaktperson.profile_image_url)"
                                    />
                                    <v-icon v-else size="100px" class="as-margin-auto" color="#386e9e">mdi-account-circle</v-icon>
                                    <div v-if="kontaktperson.editing" class="kontaktperson-image-actions as-margin-top-space-2">
                                        <input
                                            type="file"
                                            accept="image/*"
                                            :ref="'fileInput-' + kontaktperson.id"
                                            style="display: none"
                                            @change="onImageSelected($event, kontaktperson)"
                                        />
                                        <v-btn
                                            class="v-btn-as v-btn-hvit as-margin-right-space-1"
                                            color="#000"
                                            rounded="large"
                                            size="x-small"
                                            variant="outlined"
                                            prepend-icon="mdi-upload"
                                            :disabled="kontaktperson.foundMobil"
                                            @click="triggerFileInput(kontaktperson)">
                                            Last opp bilde
                                        </v-btn>
                                        <v-btn
                                            v-if="kontaktperson.profile_image_url"
                                            class="v-btn-as"
                                            color="red"
                                            rounded="large"
                                            size="x-small"
                                            variant="outlined"
                                            prepend-icon="mdi-delete"
                                            :disabled="kontaktperson.foundMobil"
                                            @click="deleteProfileImage(kontaktperson)">
                                            Slett bilde
                                        </v-btn>
                                    </div>
                                </div>
                                <div class="kontaktperson-content">
                                    <div v-if="kontaktperson.editing">
                                        <div class="col-xs-12 nop-impt">
                                            <v-text-field
                                                v-model="kontaktperson.mobil"
                                                variant="outlined"
                                                class="v-text-field-arr-sys"
                                                label="Mobil"
                                                maxlength="8"
                                                disabled
                                            ></v-text-field>
                                        </div>
                                        <div class="col-xs-12 nop-impt">
                                            <v-text-field
                                                v-model="kontaktperson.fornavn"
                                                variant="outlined"
                                                class="v-text-field-arr-sys"
                                                label="Fornavn"
                                                :disabled="kontaktperson.foundMobil"
                                            ></v-text-field>
                                        </div>
                                        <div class="col-xs-12 nop-impt">
                                            <v-text-field
                                                v-model="kontaktperson.etternavn"
                                                variant="outlined"
                                                class="v-text-field-arr-sys"
                                                label="Etternavn"
                                                :disabled="kontaktperson.foundMobil"
                                            ></v-text-field>
                                        </div>
                                        <div class="col-xs-12 nop-impt">
                                            <v-text-field
                                                v-model="kontaktperson.epost"
                                                variant="outlined"
                                                class="v-text-field-arr-sys"
                                                label="Epost"
                                                :disabled="kontaktperson.foundMobil"
                                            ></v-text-field>
                                        </div>
                                        <div class="col-xs-12 nop-impt">
                                            <v-textarea 
                                                class="v-text-field-arr-sys" 
                                                label="Beskrivelse" 
                                                variant="outlined"
                                                v-model="kontaktperson.beskrivelse"
                                                :disabled="kontaktperson.foundMobil"
                                            >
                                            </v-textarea>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <h4>{{ kontaktperson.getNavn() }}</h4>
                                        <p v-if="kontaktperson.mobil"><b>Mobil:</b> {{ kontaktperson.mobil }}</p>
                                        <p v-if="kontaktperson.epost"><b>Epost:</b> {{ kontaktperson.epost }}</p>
                                        <p v-if="kontaktperson.beskrivelse && kontaktperson.beskrivelse.length > 0">
                                            <b>Beskrivelse:</b> {{ kontaktperson.beskrivelse }}
                                        </p>
                                        <p v-else>Ingen beskrivelse</p>
                                    </div>
                                    <div class="as-margin-top-space-2">
                                        <v-btn v-if="kontaktperson.isAddedToArrangement"
                                            class="v-btn-as"
                                            :class="{ 'v-btn-as v-btn-success': kontaktperson.editing, 'v-btn-as v-btn-info': !kontaktperson.editing }"
                                            color="#000"
                                            rounded="large"
                                            size="small"
                                            variant="outlined"
                                            @click="toggleEditing(kontaktperson)">
                                            {{ kontaktperson.editing ? 'Lagre' : 'Rediger' }}
                                        </v-btn>
                                        <v-btn v-else
                                            class="v-btn-as v-btn-success"
                                            color="#000"
                                            rounded="large"
                                            size="small"
                                            variant="outlined"
                                            @click="addKontaktpersonToArrangement(kontaktperson)">
                                            Legg til</v-btn>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </v-list>
        </v-card>

        <v-dialog v-model="imagePopup" max-width="600">
            <v-card class="image-popup-card">
                <v-btn
                    icon="mdi-close"
                    variant="text"
                    class="image-popup-close"
                    @click="imagePopup = false"
                ></v-btn>
                <img :src="imagePopupUrl" alt="" class="image-popup-img" />
            </v-card>
        </v-dialog>

        <v-dialog v-model="deleteDialog" max-width="400">
            <v-card>
                <v-card-title>Bekreft fjerning</v-card-title>
                <v-card-text>
                    Er du sikker på at du vil fjerne <b>{{ deleteTarget?.getNavn() }}</b> fra arrangementet?
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey" @click="deleteDialog = false">Avbryt</v-btn>
                    <v-btn color="red" @click="confirmRemove" :loading="deleting">Fjern</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </div>
</template>

<script lang="ts">
import type { PropType } from 'vue';  // Use type-only import for PropType
import Arrangement from '../objects/Arrangement'; // Assuming you have an Arrangement type defined
import Kontaktperson from '../objects/Kontaktperson'; // Assuming you have a Kontaktperson type defined

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
    mounted() {
        this.init();
    },
    data() {
        return {
            fetched : false as boolean,
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            kontaktpersoner: [] as any[],
            imagePopup: false as boolean,
            imagePopupUrl: '' as string,
            deleteDialog: false as boolean,
            deleteTarget: null as Kontaktperson|null,
            deleting: false as boolean,
        }
    },
    methods : {
        async checkMobil(kp : Kontaktperson) {
            if(kp.mobil == null || kp.mobil.length != 8) {
                return;
            }

            var data = {
                mobil: kp.mobil,
                action: 'UKMArrangementAdmin_ajax',
                controller: 'kontaktperson/checkKontaktperson',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            if(results && results.userFound) {
                kp.mobil = '';
                kp.id = results.okp.mobil;
                kp.mobil = results.okp.mobil;
                kp.fornavn = results.okp.fornavn;
                kp.etternavn = results.okp.etternavn;
                kp.epost = results.okp.epost;
                kp.beskrivelse = results.okp.beskrivelse;
                kp.profile_image_url = results.okp.profile_image_url;
                kp.wp_user_id = results.okp.wp_user_id;
                kp.editing = true;
                kp.isAddedToArrangement = false;
                kp.foundMobil = true;
            }
            else {
                kp.id = parseInt(kp.mobil) ?? -1;
            }
        },
        init() {
            this.fetch();
        },
        toggleEditing(kp : Kontaktperson) {
            if(kp.editing) {
                this.saveKontaktperson(kp);
            }
            kp.editing = !kp.editing;
        },
        async saveKontaktperson(kp : Kontaktperson) {
            console.log(kp.isAddedToArrangement);
            if(kp.isAddedToArrangement == false) {
                await this.addKontaktpersonToArrangement(kp);
                await this.fetch();

                return;
            }
            try {
                await kp.save();
                await this.fetch();
            } catch (e) {
                // Error already shown via spaInteraction
            }
        },
        triggerFileInput(kp : Kontaktperson) {
            const refKey = 'fileInput-' + kp.id;
            const input = (this.$refs[refKey] as HTMLInputElement[] | HTMLInputElement);
            const el = Array.isArray(input) ? input[0] : input;
            if (el) el.click();
        },
        onImageSelected(event: Event, kp : Kontaktperson) {
            const input = event.target as HTMLInputElement;
            if (input.files && input.files.length > 0) {
                kp.setUploadedImage(input.files[0]);
            }
        },
        deleteProfileImage(kp : Kontaktperson) {
            kp.deleteImage();
        },
        openImagePopup(url: string) {
            this.imagePopupUrl = url;
            this.imagePopup = true;
        },
        nyKontaktperson() {
            for(let kp of this.kontaktpersoner) {
                if(kp.id == -1) {
                    return;
                }
            }

            let kp = Kontaktperson.createEmpty(this.arrangement.id, 'monstring');
            (<any>kp).editing = true;
            this.kontaktpersoner.push(kp);
        },
        mapToKontaktperson(kpRes : any) : Kontaktperson {
            const navnDeler = (kpRes.navn ?? '').split(' ');
            const fornavn = kpRes.fornavn ?? navnDeler.shift() ?? '';
            const etternavn = kpRes.etternavn ?? navnDeler.join(' ') ?? '';

            const kp = new Kontaktperson(
                kpRes.id ?? -1,
                fornavn,
                etternavn,
                kpRes.mobil ?? kpRes.telefon ?? null,
                kpRes.beskrivelse ?? '',
                kpRes.epost ?? null,
                kpRes.created_date ?? null,
                kpRes.modified_date ?? null,
                kpRes.eier_omrade_id ?? -1,
                kpRes.eier_omrade_type ?? '',
                kpRes.profile_image_url ?? kpRes.bilde ?? null,
                kpRes.wp_user_id ?? null,
            );

            (<any>kp).isAddedToArrangement = true;
            (<any>kp).editing = false;
            return kp;
        },
        async fetch() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'kontaktperson/getKontaktpersoner',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            this.kontaktpersoner = [];

            for(let kpRes of results) {
                this.kontaktpersoner.push(this.mapToKontaktperson(kpRes));
            }

            this.fetched = true;
        },
        async addKontaktpersonToArrangement(kp : Kontaktperson) {
            var data = {
                kpId: kp.id,
                mobil: kp.mobil,
                fornavn: kp.fornavn,
                etternavn: kp.etternavn,
                epost: kp.epost,
                beskrivelse: kp.beskrivelse,
                foundMobil: kp.foundMobil ? kp.mobil : -1,
                omradeId: this.arrangement.id,
                omradeType: 'monstring',
                action: 'UKMArrangementAdmin_ajax',
                controller: 'kontaktperson/addKontaktpersonToArrangement',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
            await this.fetch();
        },
        removeKontaktpersonFromArrangement(kp : Kontaktperson) {
            if(kp.id == -1) {
                this.kontaktpersoner = this.kontaktpersoner.filter(k => k !== kp);
                return;
            }

            this.deleteTarget = kp;
            this.deleteDialog = true;
        },
        async confirmRemove() {
            if(!this.deleteTarget) return;

            this.deleting = true;
            try {
                var data = {
                    id: this.deleteTarget.id,
                    omradeId: this.arrangement.id,
                    omradeType: 'monstring',
                    action: 'UKMArrangementAdmin_ajax',
                    controller: 'kontaktperson/removeKontaktpersonFromArrangement',
                };

                await this.spaInteraction.runAjaxCall('/', 'POST', data);
                this.deleteDialog = false;
                await this.fetch();
            } catch (e) {
                this.spaInteraction.showMessage('Feil', 'Kunne ikke fjerne kontaktperson', 'error');
            } finally {
                this.deleting = false;
                this.deleteTarget = null;
            }
        },
    }
}
</script>

<style scoped>
.kontaktperson-fields {
    display: flex;
}
.kontaktperson-image {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.kontaktperson-image img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    display: block;
    margin: auto;
}
.kontaktperson-image-clickable {
    cursor: pointer;
    transition: opacity 0.2s;
}
.kontaktperson-image-clickable:hover {
    opacity: 0.8;
}
.kontaktperson-image-actions {
    display: flex;
    justify-content: center;
    gap: 4px;
    flex-wrap: wrap;
}
.image-popup-card {
    position: relative;
    padding: 0;
    overflow: hidden;
}
.image-popup-close {
    position: absolute;
    top: 4px;
    right: 4px;
    z-index: 1;
}
.image-popup-img {
    width: 100%;
    display: block;
}
.kontaktperson-content {
    width: 100%;
    text-align: center;
}
.h-g-list {
    gap: calc(var(--initial-space-box) * 2);
}
.kontakt-gruppe-item {
    flex: 0 0 calc(24% - var(--initial-space-box));
    max-width: calc(24% - var(--initial-space-box));
    box-sizing: border-box;
}
.hendelse-item {
    background-color: var(--color-primary-grey-lightest);
    box-shadow: none;
}
.kontakt-gruppe-item {
    box-shadow: none;
}
.extendable-hendelse,
.hendelse-gruppe-data {
    display: grid;
}
.h-g-card,
.h-g-card .h-g-list {
    background: transparent !important;
    box-shadow: none !important;
}
.hg-item {
    overflow: hidden;
    box-shadow: none !important;
    background: #fff !important;
}
.kontaktperson-card {
    position: relative;
}
.kontaktperson-remove-btn {
    position: absolute;
    top: 4px;
    right: 4px;
    z-index: 1;
    opacity: 0;
    transition: opacity 0.2s;
}
.kontaktperson-card:hover .kontaktperson-remove-btn {
    opacity: 1;
}
@media (max-width: 1200px) {
    .kontakt-gruppe-item {
        flex: 0 0 calc(50% - var(--initial-space-box));
        max-width: calc(50% - var(--initial-space-box));
        box-sizing: border-box;
    }
}
@media (max-width: 576px) {
    .kontakt-gruppe-item {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>
