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
        this.fra = fra;
        this.til = til;

        this.title = this.getTitle();
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

    private getTitle(): string {
        if (!this.fra || !this.til) return this.navn;

        let fraDate = new Date(this.fra);
        let tilDate = new Date(this.til);

        const formatDate = (date: Date, hourOnly: boolean = false) => {
            const hours = String(date.getHours()).padStart(2, "0");
            const minutes = String(date.getMinutes()).padStart(2, "0");

            const daysOfWeek = ['søndag', 'mandag', 'tirsdag', 'onsdag', 'torsdag', 'fredag', 'lørdag'];

            if(hourOnly) {
                return `kl. ${hours}:${minutes}`;
            }
            return `${daysOfWeek[date.getDay()]}, kl. ${hours}:${minutes}`;
        };

        if(fraDate.getDay() == tilDate.getDay()) {
            let from = formatDate(fraDate).charAt(0).toLocaleLowerCase() + formatDate(fraDate).slice(1);
            return `${this.navn} (${from} til ${formatDate(tilDate, true)})`;
        }
        return `${this.navn} (${formatDate(fraDate)} til ${formatDate(tilDate)})`;
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