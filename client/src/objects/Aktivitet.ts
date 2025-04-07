import AktivitetTidspunkt from './AktivitetTidspunkt';
import AktivitetTag from "./AktivitetTag";

class Aktivitet {
    loading : boolean = false;
    deleted : boolean = false;

    title : string;
    subtitle : string;

    id : number;
    navn : string;
    sted : string;
    beskrivelse : string;
    plId : number;

    tags : AktivitetTag[] = [];

    // Alle tidspunkter
    tidspunkter : AktivitetTidspunkt[] = [];

    public expanded : boolean = false;  
    public expandedTidspunkter : boolean = false;

    private spaInteraction = (<any>window).spaInteraction; // Definert i main.ts

    constructor(id : number, navn : string, sted : string, beskrivelse : string, plId : number, tidspunkter : AktivitetTidspunkt[], tags : AktivitetTag[]) {
        this.id = id;
        this.navn = navn;
        this.title = navn;
        this.subtitle = sted;
        this.sted = sted;
        this.beskrivelse = beskrivelse;
        this.plId = plId;
        this.tidspunkter = tidspunkter;
        this.tags = tags;

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
        console.log(this.tidspunkter);
        // Only if id -1 is not in the list
        if(this.tidspunkter.find(tidspunkt => tidspunkt.id == -1) != null) {
            return;
        }

        console.log('point ALFA');

        this.tidspunkter.unshift(new AktivitetTidspunkt(
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
            true,     // : boolean,
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

        // Add new tidspunkt placeholder
        this.addNewTidspubktInTheList();

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
        let tagsResults = null;
        if(results != null) {
            tagsResults = this.saveTags();
        }

        if(results != null && tagsResults != null) {
            this.loading = false;
            if(this.id != -1) {
                this.spaInteraction.showMessage('Lagret', 'Aktiviteten er lagret!', 'success');
            }

        }
        
        return results;
    }

    public async delete() {
        if(this.tidspunkter.length > 1) {
            this.spaInteraction.showMessage('Feil', 'Aktiviteten har forekomster og kan ikke slettes. Slett forekomstene først!', 'error');
            return;
        }
        this.loading = true;
        
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'aktivitet/deleteAktivitet',
            aktivitetId: this.id,
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        if(results && results.completed && results.completed == true) {
            this.loading = false;
            this.deleted = true;
        } else {
            this.spaInteraction.showMessage('Feil', 'Kunne ikke slette aktivitet: ' + (results.message ?? ''), 'error');
        }
        
        return results;
    }

    private async saveTags() {
        if(this.id == -1) {
            return;
        }
        this.loading = true;
        
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'aktivitet/updateAktivitetTags',
            aktivitetId: this.id,
            tags: this.tags.length < 1 ? [] : this.tags,
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        if(results && results.completed && results.completed == true) {
            this.loading = false;
        } else {
            this.spaInteraction.showMessage('Feil', 'Kunne ikke slette aktivitet: ' + (results.message ?? ''), 'error');
        }
        
        return results;
    }
    
}

export default Aktivitet;
