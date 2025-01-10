class Arrangement {
    id: string;
    name: string;
    place: string;
    startDate: Date;
    endDate: Date;
    status: number;
    statusKortText : string = "";
    statusLangText : string = "";
    antallDeltakere: boolean;
    openPamelding: boolean;
    openVideresending: boolean;
    viseFrist: Date;
    jobbeFrist: Date;

    // Spesialfelt for landsfestivalen
    kvote_deltakere : string|null = null;
    kvote_ledere : string|null = null;
    avgift_ordinar : string|null = null;
    avgift_subsidiert : string|null = null;
    avgift_reise : string|null = null;

    saveOngoing: boolean = false;
    spaInteraction: any = (<any>window).spaInteraction; // Definert i main.ts

    constructor(id: string,
            name: string,
            place: string,
            startDate: number,
            endDate: number,
            status: number,
            statusKortText: string,
            statusLangText: string,
            antallDeltakere: boolean,
            openPamelding: boolean,
            openVideresending: boolean,
            viseFrist: number,
            jobbeFrist: number
        ) {

        this.id = id;
        this.name = name;
        this.place = place;
        this.startDate = new Date(startDate * 1000);
        this.endDate = new Date(endDate * 1000);
        this.status = status;
        this.antallDeltakere = antallDeltakere;
        this.openPamelding = openPamelding;
        this.openVideresending = openVideresending;
        this.viseFrist = new Date(viseFrist * 1000);
        this.jobbeFrist = new Date(jobbeFrist * 1000);
        this.statusKortText = statusKortText ?? "";
        this.statusLangText = statusLangText ?? "";
    }

    static createEmpty(): Arrangement {
        return new Arrangement(
            "",           // Empty string for id
            "",           // Empty string for name
            "",           // Empty string for place
            0,            // Epoch time for startDate
            0,            // Epoch time for endDate
            0,            // Default status
            "",           // Default statusKortText
            "",           // Default statusLangText
            false,        // Default for antallDeltakere
            false,        // Default for openPamelding
            false,         // Default for openVideresending
            0,
            0,
        );
    }
    public static async load() : Promise<Arrangement> {
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'getArrangement',
        };

        var results = await (<any>window).spaInteraction.runAjaxCall('/', 'POST', data);

        if(results != null) {
            let status = results.status == 'gjennomforer_med_info' ? 1 : (results.status == 'avlys' ? 2 : 0);

            let arrangement = new Arrangement(
                results.id,
                results.name,
                results.place,
                results.startDate,
                results.endDate,
                status,
                results.statusKortText,
                results.statusLangText,
                results.antallDeltakere,
                results.openPamelding,
                results.openVideresending,
                results.viseFrist,
                results.jobbeFrist,
            );

            // Spesialfelt for landsfestivalen
            arrangement.kvote_deltakere = results.kvote_deltakere ?? null;
            arrangement.kvote_ledere = results.kvote_ledere ?? null;
            arrangement.avgift_ordinar = results.avgift_ordinar ?? null;
            arrangement.avgift_subsidiert = results.avgift_subsidiert ?? null;
            arrangement.avgift_reise = results.avgift_reise ?? null;

            console.log(arrangement);
            return arrangement;
        }else {
            (<any>window).spaInteraction.showMessage('Feil', 'Kunne ikke hente innstillinger', 'error');
        }

        return Arrangement.createEmpty();        
    }
    public async save() {
        if(!this.canBeSaved()) {
            return;
        }
        this.saveOngoing = true;
        
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'saveArrangement',
            
            name: this.name,
            place: this.place,
            startDate: this.startDate.getTime() / 1000,
            endDate: this.endDate.getTime() / 1000,
            status: this.status,
            statusKortText: this.statusKortText,
            statusLangText: this.statusLangText,
            antallDeltakere: this.antallDeltakere,
            openPamelding: this.openPamelding,
            openVideresending: this.openVideresending,
            viseFrist: this.viseFrist.getTime() / 1000,
            jobbeFrist: this.jobbeFrist.getTime() / 1000,
            // Spesialfelt for landsfestivalen
            kvote_deltakere: this.kvote_deltakere,
            kvote_ledere: this.kvote_ledere,
            avgift_ordinar: this.avgift_ordinar,
            avgift_subsidiert: this.avgift_subsidiert,
            avgift_reise: this.avgift_reise,
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        if(results != null && results.success == true) {
            this.spaInteraction.showMessage('Lagret', 'Arrangementet er lagret', 'success');
        } else {
            this.spaInteraction.showMessage('Feil', 'Kunne ikke lagre arrangementet', 'error');
        }
        
        return results;
    }

    private canBeSaved() : boolean {
        if(this.startDate.getTime() > this.endDate.getTime()) {
            this.spaInteraction.showMessage('Feil', 'Startdato kan ikke være etter sluttdato', 'error');
            return false;
        }
        return true;
    }

    public isActive() {
        return this.status != 2;
    }

    public isVideresendingOpen() {
        return this.openVideresending;
    }

}

export default Arrangement;