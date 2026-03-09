import axios from 'axios';

class Kontaktperson {
    id: number;
    fornavn: string;
    etternavn: string;
    mobil: string|null;
    beskrivelse: string;
    epost: string|null;
    created_date: string|null;
    modified_date: string|null;
    eier_omrade_id: number;
    eier_omrade_type: string;
    profile_image_url: string|null;
    wp_user_id: number|null;
    editing: boolean = false;
    uploadedImage: File|null = null;
    deletedProfileImage: boolean = false;
    isAddedToArrangement: boolean = false;
    foundMobil: boolean = false;

    private spaInteraction = (<any>window).spaInteraction;

    constructor(
        id: number,
        fornavn: string,
        etternavn: string,
        mobil: string|null,
        beskrivelse: string,
        epost: string|null,
        created_date: string|null = null,
        modified_date: string|null = null,
        eier_omrade_id: number = -1,
        eier_omrade_type: string = '',
        profile_image_url: string|null = null,
        wp_user_id: number|null = null,
    ) {
        this.id = id;
        this.fornavn = fornavn;
        this.etternavn = etternavn;
        this.mobil = mobil;
        this.beskrivelse = beskrivelse ?? '';
        this.epost = epost;
        this.created_date = created_date ?? null;
        this.modified_date = modified_date ?? null;
        this.eier_omrade_id = eier_omrade_id;
        this.eier_omrade_type = eier_omrade_type;
        this.profile_image_url = profile_image_url ?? null;
        this.wp_user_id = wp_user_id ?? null;
        this.isAddedToArrangement = true;
    }

    static createEmpty(eier_omrade_id: number = -1, eier_omrade_type: string = 'monstring'): Kontaktperson {
        let kp = new Kontaktperson(
            -1,
            '',
            '',
            '',
            '',
            '',
            null,
            null,
            -1,
            '',
            null,
            null,
        );
        kp.eier_omrade_type = eier_omrade_type;
        kp.isAddedToArrangement = false;

        return kp;
    }

    public getNavn(): string {
        return `${this.fornavn} ${this.etternavn}`.trim();
    }

    public async save() : Promise<any> {
        const formData = new FormData();
        formData.append('action', 'UKMArrangementAdmin_ajax');
        formData.append('controller', 'kontaktperson/saveKontaktperson');
        formData.append('omradeId', String(this.eier_omrade_id));
        formData.append('omradeType', this.eier_omrade_type);
        formData.append('okpId', String(this.id));
        formData.append('fornavn', this.fornavn);
        formData.append('etternavn', this.etternavn);
        formData.append('epost', this.epost ?? '');
        formData.append('beskrivelse', this.beskrivelse);
        formData.append('deletedProfileImage', this.deletedProfileImage ? 'true' : 'false');

        if (this.uploadedImage) {
            formData.append('profile_picture', this.uploadedImage);
        }

        try {
            const response = await axios.post((<any>window).ajaxurl, formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });

            if (this.deletedProfileImage && !this.uploadedImage) {
                this.profile_image_url = null;
            }

            this.resetImageState();
            return response.data;
        } catch (error: any) {
            this.spaInteraction.showMessage('Feil', error?.response?.data?.result ?? 'Kunne ikke lagre kontaktperson', 'error');
            throw error;
        }
    }

    public setUploadedImage(file: File) : void {
        this.uploadedImage = file;
        this.deletedProfileImage = false;
        this.profile_image_url = URL.createObjectURL(file);
    }

    public deleteImage() : void {
        this.deletedProfileImage = true;
        this.uploadedImage = null;
        this.profile_image_url = null;
    }

    private resetImageState() : void {
        this.uploadedImage = null;
        this.deletedProfileImage = false;
    }

}

export default Kontaktperson;
