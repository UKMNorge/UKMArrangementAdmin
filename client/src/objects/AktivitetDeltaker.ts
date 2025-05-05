import Aktivitet from "./Aktivitet";

class AktivitetDeltaker {
    loading : boolean = false;

    mobil : string;

    private spaInteraction = (<any>window).spaInteraction; // Definert i main.ts


    constructor(mobil : string) {
        this.mobil = mobil;
    }

    public async save() {
        this.loading = true;
        // Save to database via API
        
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'saveLeder',
            lederId: this.id,
            godkjenning: this.godkjent,
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        if(results != null) {
            this.loading = false;
        }
        
        return results;
    }
}

export default AktivitetDeltaker;
