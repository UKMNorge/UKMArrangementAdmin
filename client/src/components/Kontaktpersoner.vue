<template>
    <div class="as-container main-container">
        <div class="aktiviteter-buttons as-margin-bottom-space-4 as-display-flex">
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
                <div v-for="kontaktperson in kontaktpersoner" :key="kontaktperson.id" class="hendelse-gruppe-item as-margin-top-space-2">
                    <div class="hg-item as-card-1 as-padding-space-4">
                        <div class="as-display-flex as-display-flex-wrap kontaktperson-fields">
                            <div class="kontaktperson-image as-margin-bottom-space-2">
                                <img
                                    v-if="kontaktperson.profile_image_url"
                                    :src="kontaktperson.profile_image_url"
                                    alt=""
                                />
                                <v-icon v-else size="100px" class="as-margin-auto" color="#386e9e">mdi-account-circle</v-icon>
                            </div>
                            <div class="kontaktperson-content">
                                <div v-if="kontaktperson.editing">
                                    <div class="col-xs-12 nop-impt">
                                        <v-text-field
                                            v-model="kontaktperson.fornavn"
                                            variant="outlined"
                                            class="v-text-field-arr-sys"
                                            label="Fornavn"
                                        ></v-text-field>
                                    </div>
                                    <div class="col-xs-12 nop-impt">
                                        <v-text-field
                                            v-model="kontaktperson.etternavn"
                                            variant="outlined"
                                            class="v-text-field-arr-sys"
                                            label="Etternavn"
                                        ></v-text-field>
                                    </div>
                                    <div class="col-xs-12 nop-impt">
                                        <v-text-field
                                            v-model="kontaktperson.epost"
                                            variant="outlined"
                                            class="v-text-field-arr-sys"
                                            label="Epost"
                                        ></v-text-field>
                                    </div>
                                    <div class="col-xs-12 nop-impt">
                                        <v-text-field
                                            v-model="kontaktperson.mobil"
                                            variant="outlined"
                                            class="v-text-field-arr-sys"
                                            label="Mobil"
                                        ></v-text-field>
                                    </div>
                                    <div class="col-xs-12 nop-impt">
                                        <v-textarea 
                                            class="v-text-field-arr-sys" 
                                            label="Beskrivelse" 
                                            variant="outlined"
                                            v-model="kontaktperson.beskrivelse">
                                        </v-textarea>
                                    </div>
                                </div>
                                <div v-else>
                                    <h4>{{ kontaktperson.getNavn() }}</h4>
                                    <p v-if="kontaktperson.epost"><b>Epost:</b> {{ kontaktperson.epost }}</p>
                                    <p v-if="kontaktperson.mobil"><b>Mobil:</b> {{ kontaktperson.mobil }}</p>
                                    <p v-if="kontaktperson.beskrivelse && kontaktperson.beskrivelse.length > 0">
                                        <b>Beskrivelse:</b> {{ kontaktperson.beskrivelse }}
                                    </p>
                                    <p v-else>Ingen beskrivelse</p>
                                </div>
                                <div class="as-margin-top-space-2">
                                    <v-btn
                                        class="v-btn-as v-btn-hvit as-margin-right-space-2"
                                        color="#000"
                                        rounded="large"
                                        size="small"
                                        variant="outlined"
                                        @click="toggleEditing(kontaktperson)">
                                        {{ kontaktperson.editing ? 'Lagre' : 'Rediger' }}
                                    </v-btn>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </v-list>
        </v-card>

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
        }
    },
    methods : {
        init() {
            this.fetch();
        },
        toggleEditing(kp : any) {
            kp.editing = !kp.editing;
        },
        nyKontaktperson() {
            for(let kp of this.kontaktpersoner) {
                if(kp.id == -1) {
                    return;
                }
            }

            let kp = Kontaktperson.createEmpty();
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
            (<any>kp).editing = false;
            return kp;
        },
        async fetch() {
            var data = {
                action: 'UKMArrangementAdmin_ajax',
                controller: 'getKontaktpersoner',
            };

            var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

            this.kontaktpersoner = [];

            for(let kpRes of results) {
                this.kontaktpersoner.push(this.mapToKontaktperson(kpRes));
            }

            this.fetched = true;
        }
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
}
.kontaktperson-image img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    display: block;
    margin: auto;
}
.kontaktperson-content {
    width: 100%;
    text-align: center;
}
.h-g-list {
    gap: calc(var(--initial-space-box) * 2);
}
.hendelse-gruppe-item {
    flex: 0 0 calc(50% - var(--initial-space-box));
    max-width: calc(50% - var(--initial-space-box));
    box-sizing: border-box;
}
@media (max-width: 576px) {
    .hendelse-gruppe-item {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
.hendelse-item {
    background-color: var(--color-primary-grey-lightest);
    box-shadow: none;
}
.hendelse-gruppe-item {
    box-shadow: none;
    padding: calc(3 * var(--initial-space-box)) !important;
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
</style>
