import Aktivitet from "./Aktivitet";
import AktivitetDeltaker from "./AktivitetDeltaker";
import Hendelse from "./Hendelse";

class AktivitetTidspunkt {
    loading : boolean = false;
    isReal : boolean = true;
    deleted : boolean = false;

    id : number;
    sted : string;
    start : string;
    slutt : string;
    varighetMinutter : number;
    maksAntall : number;
    hasMaksAntall : boolean;
    harPaamelding : boolean;
    erSammeStedSomAktivitet : boolean;
    kunInterne : boolean;

    hendelse : Hendelse|null = null; // Foreign key til Hendelse. Kan være null : string;
   
    aktivitet : Aktivitet; // Foreign key til Aktivite : string;
    
    deltakere : AktivitetDeltaker[] = [];

    private spaInteraction = (<any>window).spaInteraction; // Definert i main.ts


    constructor(
        id : number, 
        sted : string, 
        start : string, 
        slutt : string,
        varighetMinutter : number, 
        maksAntall : number, 
        hendelse : Hendelse|null, 
        aktivitet : Aktivitet, 
        deltakere : AktivitetDeltaker[],
        harPaamelding : boolean,
        erSammeStedSomAktivitet : boolean,
        kunInterne : boolean,
    ) {
        if(id == -1) {
            this.isReal = false;
        }

        this.id = id;
        this.sted = sted ?? "";
        this.start = start;
        this.slutt = slutt;
        this.varighetMinutter = varighetMinutter;
        this.maksAntall = maksAntall;

        this.harPaamelding = harPaamelding;
        this.erSammeStedSomAktivitet = erSammeStedSomAktivitet;
        this.kunInterne = kunInterne;

        if(maksAntall >= 999999) {
            this.hasMaksAntall = true;
        }
        else {
            this.hasMaksAntall = false;
        }

        this.hendelse = hendelse;
        this.aktivitet = aktivitet;
        this.deltakere = deltakere;
    }

    public setAlleDeltakere(deltakere : AktivitetDeltaker[]) {
        this.deltakere = deltakere;
    }
    
    public removeDeltaker(deltaker : AktivitetDeltaker) {
        if(this.removeDeltakerFromDb(deltaker)) {
            var index = this.deltakere.indexOf(deltaker);
            if(index > -1) {
                this.deltakere.splice(index, 1);
            }
        }
    }

    getTittel(): string {
        return this.isReal ? this.getStartHuman() : "+";
    }

    getStartDate(): Date {
        return new Date(this.start.replace(" ", "T"));
    }
    
    getSluttDate(): Date {
        return new Date(this.slutt.replace(" ", "T"));
    }

    private getHumanDate(arg : string): string {
        // Convert timestamp to date
        const startDate = new Date(arg.replace(" ", "T")); // Replace space with "T" for proper ISO format
        
        // Days of the week array
        const daysOfWeek = ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'];
    
        // Format the date to the desired format "Mandag hh:mm"
        return `${daysOfWeek[startDate.getDay()]} ${String(startDate.getHours()).padStart(2, '0')}:${String(startDate.getMinutes()).padStart(2, '0')}`;
    }

    getStartHuman(): string {
        return this.getHumanDate(this.start);
    }
    
    getSluttHuman() : string {
        return this.getHumanDate(this.slutt);
    }

    private removeDeltakerFromDb(deltaker : AktivitetDeltaker) : boolean {
        let ret = false;
        // Call API to remove deltaker from database

        return ret;
    }

    public async create() {
        let tidspunkt = await this.save('aktivitet/createTidspunkt');

        if(tidspunkt) {
            this.isReal = true;

            this.id = tidspunkt.id;
            this.sted = tidspunkt.sted ?? "";
            this.start = tidspunkt.start;
            this.slutt = tidspunkt.slutt;
            this.varighetMinutter = tidspunkt.varighetMinutter;
            this.maksAntall = tidspunkt.maksAntall;
    
            this.harPaamelding = tidspunkt.harPaamelding;
            this.erSammeStedSomAktivitet = tidspunkt.erSammeStedSomAktivitet;
            
            if(tidspunkt.maksAntall >= 999999) {
                this.hasMaksAntall = true;
            }
            else {
                this.hasMaksAntall = false;
            }
    
            this.hendelse = null;
            this.deltakere = [];

            // Add placeholder for new tidspunkt
            this.aktivitet.addNewTidspubktInTheList();
        }
        
        return tidspunkt;
    }

    public async save(controllerArg : string|null = null) {
        this.loading = true;
        // Save to database via API
        
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: controllerArg ?? 'aktivitet/saveAktivitetTidspunkt',
            tidspunktId: this.id,
            sted: this.sted,
            start: this.start,
            slutt: this.slutt,
            varighet: this.varighetMinutter,
            maksAntall: this.hasMaksAntall ? 999999 : this.maksAntall,
            aktivitetId: this.aktivitet.id,
            harPaamelding: this.harPaamelding,
            erSammeStedSomAktivitet: this.erSammeStedSomAktivitet,
            kunInterne : this.kunInterne,
        };
        if(this.hendelse) {
            (<any>data).hendelseId = this.hendelse;
        }

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        if(results != null) {
            this.loading = false;
        }
        
        return results;
    }

    public async delete() {
        this.loading = true;
        // Save to database via API
        
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'aktivitet/deleteTidspunkt',
            tidspunktId: this.id,
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        if(results && results.completed && results.completed == true) {
            this.loading = false;
            this.deleted = true;
        } else {
            this.spaInteraction.showMessage('Feil', 'Kunne ikke slette tidspunktet: ' + (results.message ?? ''), 'error');
        }
        
        return results;
    }
}

export default AktivitetTidspunkt;
