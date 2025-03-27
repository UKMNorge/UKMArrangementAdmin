import AktivitetTidspunkt from './AktivitetTidspunkt';

class Aktivitet {
    loading : boolean = false;
    
    title : string;
    subtitle : string;

    id : number;
    navn : string;
    sted : string;
    beskrivelse : string;
    plId : number;

    // Alle tidspunkter
    tidspunkter : AktivitetTidspunkt[] = [];

    public expanded : boolean = false;  

    private spaInteraction = (<any>window).spaInteraction; // Definert i main.ts

    constructor(id : number, navn : string, sted : string, beskrivelse : string, plId : number, tidspunkter : AktivitetTidspunkt[]) {
        this.id = id;
        this.navn = navn;
        this.title = navn;
        this.subtitle = sted;
        this.sted = sted;
        this.beskrivelse = beskrivelse;
        this.plId = plId;
        this.tidspunkter = tidspunkter;
    }

    public async save() {
        this.loading = true;
        // Save to database via API
        
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'saveLeder',
            tidspunktId: this.id,
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        if(results != null) {
            this.loading = false;
        }
        
        return results;
    }
}

export default Aktivitet;
