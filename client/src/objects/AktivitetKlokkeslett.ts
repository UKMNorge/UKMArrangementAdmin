class AktivitetKlokkeslett {
    id: number;
    title: string;
    navn: string;
    fra: string;
    til: string;

    private spaInteraction = (<any>window).spaInteraction; // Definert i main.ts

    constructor(id : number, navn : string, fra : string, til : string) {
        this.id = id;
        this.navn = navn;
        this.title = navn + ' ' + fra + " - " + til;
        this.fra = fra;
        this.til = til;
    }

    public async save() {
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'aktivitet/klokkeslett/save',
            id: this.id,
            navn: this.navn,
            fra: this.fra,
            til: this.til
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        return results;
    }

    public async delete() {
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'aktivitet/klokkeslett/delete',
            id: this.id
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        return results;
    }

    public async create() {
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'aktivitet/klokkeslett/create',
            navn: this.navn,
            id: this.id,
            fra: this.fra,
            til: this.til
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);

        this.id = results.id;
        this.fra = results.fra;
        this.til = results.til;
        this.title = results.fra + " - " + results.til;

        return results;
    }

}

export default AktivitetKlokkeslett;