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
    public expandedTidspunkter : boolean = false;
    
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

        this.addNewTidspubktInTheList();
    }

    public openOrClose() {

        this.expanded = !this.expanded
    }

    public removeTidspunkt(tidspunkt : AktivitetTidspunkt) {
        var index = this.tidspunkter.indexOf(tidspunkt);
        if(index > -1) {
            this.tidspunkter.splice(index, 1);
        }
    }

    public addNewTidspubktInTheList() {
        // Only if id -1 is not in the list
        if(this.tidspunkter.find(tidspunkt => tidspunkt.id == -1) != null) {
            return;
        }

        this.tidspunkter.push(new AktivitetTidspunkt(
            -1,     // : number,
            '',     // : string, 
            "",     // : string, 
            "",     // : string, 
            0,     // : number, 
            100,     // : number, 
            null,     // : number|null, 
            this,     // : Aktivitet, 
            [],     // : AktivitetDeltaker[],
            true,     // : boolean,
            true,     // : boolean,)
        ));
    }

    public async create() {
        let results : any = await this.save('aktivitet/createAktivitet');

        this.id = results.id;
        this.navn = results.navn;
        this.title = results.navn;
        this.subtitle = results.sted;
        this.sted = results.sted;
        this.beskrivelse = results.beskrivelse;
        this.plId = results.plId;
        this.tidspunkter = []; // Assuming the last parameter

        console.log('this');
        console.log(this);

        return results;
    }

    public async save(controllerArg : string|null = null) {
        this.loading = true;
        // Save to database via API
        
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: controllerArg ?? 'aktivitet/saveAktivitet',
            aktivitetId: this.id,
            navn: this.navn,
            sted: this.sted,
            beskrivelse: this.beskrivelse,
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        if(results != null) {
            this.loading = false;
        }
        
        return results;
    }
    
}

export default Aktivitet;
