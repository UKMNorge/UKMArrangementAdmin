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
    }

    static createEmpty(): Kontaktperson {
        return new Kontaktperson(
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
    }

    public getNavn(): string {
        return `${this.fornavn} ${this.etternavn}`.trim();
    }

    public save() : Promise<any> {
        const data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'saveKontaktperson',
            kontaktperson: this,
        };

        let res = (<any>window).spaInteraction.runAjaxCall('/', 'POST', data);
        
        return res;
    }

}

export default Kontaktperson;
