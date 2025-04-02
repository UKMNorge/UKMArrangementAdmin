class AktivitetTag {
    id: number;
    title: string;
    navn: string;
    beskrivelse: string;

    private spaInteraction = (<any>window).spaInteraction; // Definert i main.ts

    constructor(id : number, navn : string, beskrivelse : string) {
        this.id = id;
        this.title = navn;
        this.navn = navn;
        this.beskrivelse = beskrivelse;
    }

    public async save() {
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'aktivitet/saveTag',
            id: this.id,
            navn: this.navn,
            beskrivelse: this.beskrivelse
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        return results;
    }

    public async delete() {
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'aktivitet/deleteTag',
            tagId: this.id
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        return results;
    }

    public async create() {
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'aktivitet/createTag',
            id: this.id,
            navn: this.navn,
            beskrivelse: this.beskrivelse
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        this.id = results.id;
        this.navn = results.navn;
        this.title = results.navn;
        this.beskrivelse = results.beskrivelse;

        return results;
    }

}

export default AktivitetTag;