// Create class with properties and methods
class Arrangement {
    id: string;
    name: string;
    place: string;
    startDate: Date;
    endDate: Date;
    status: number;
    antallDeltakere: boolean;
    openPamelding: boolean;
    openVideresending: boolean;

    saveOngoing: boolean = false;
    spaInteraction: any = (<any>window).spaInteraction; // Definert i main.ts


    constructor(id: string, name: string, place: string, startDate: number, endDate: number, status: number, antallDeltakere: boolean, openPamelding: boolean, openVideresending: boolean) {
        this.id = id;
        this.name = name;
        this.place = place;
        this.startDate = new Date(startDate * 1000);
        this.endDate = new Date(endDate * 1000);
        this.status = status;
        this.antallDeltakere = antallDeltakere;
        this.openPamelding = openPamelding;
        this.openVideresending = openVideresending;
    }

    static createEmpty(): Arrangement {
        return new Arrangement(
            "",           // Empty string for id
            "",           // Empty string for name
            "",           // Empty string for place
            0,            // Epoch time for startDate
            0,            // Epoch time for endDate
            0,            // Default status
            false,        // Default for antallDeltakere
            false,        // Default for openPamelding
            false         // Default for openVideresending
        );
    }

    public async save() {
        this.saveOngoing = true;
        
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'saveArrangement',
            
            name: this.name,
            place: this.place,
            startDate: this.startDate.getTime() / 1000,
            endDate: this.endDate.getTime() / 1000,
            status: this.status,
            antallDeltakere: this.antallDeltakere,
            openPamelding: this.openPamelding,
            openVideresending: this.openVideresending
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        if(results != null && results.success == true) {
            this.spaInteraction.showMessage('Lagret', 'Arrangementet er lagret', 'success');
        } else {
            this.spaInteraction.showMessage('Feil', 'Kunne ikke lagre arrangementet', 'error');
        }
        
        return results;
    }

}

export default Arrangement;