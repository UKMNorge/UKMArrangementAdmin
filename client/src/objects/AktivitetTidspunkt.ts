import Aktivitet from "./Aktivitet";
import AktivitetDeltaker from "./AktivitetDeltaker";
import AktivitetTag from "./AktivitetTag";

class AktivitetTidspunkt {
    loading : boolean = false;
    isReal : boolean = true;

    id : number;
    sted : string;
    start : string;
    slutt : string;
    varighetMinutter : number;
    maksAntall : number;
    hasMaksAntall : boolean;
    harPaamelding : boolean;
    erSammeStedSomAktivitet : boolean;

    hendelseId : number|null; // Foreign key til Hendelse. Kan være null : string;
   
    aktivitet : Aktivitet; // Foreign key til Aktivite : string;
    
    deltakere : AktivitetDeltaker[] = [];
    tags : AktivitetTag[] = [];

    private spaInteraction = (<any>window).spaInteraction; // Definert i main.ts


    constructor(
        id : number, 
        sted : string, 
        start : string, 
        slutt : string,
        varighetMinutter : number, 
        maksAntall : number, 
        hendelseId : number|null, 
        aktivitet : Aktivitet, 
        deltakere : AktivitetDeltaker[],
        harPaamelding : boolean,
        erSammeStedSomAktivitet : boolean,
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
        
        if(maksAntall >= 999999) {
            this.hasMaksAntall = true;
        }
        else {
            this.hasMaksAntall = false;
        }

        this.hendelseId = hendelseId;
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
        let tidspunkt = this.save('aktivitet/createTidspunkt');

        this.isReal = true;

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
        };
        if(this.hendelseId) {
            (<any>data).hendelseId = this.hendelseId;
        }

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        if(results != null) {
            this.loading = false;
        }
        
        return results;
    }
}

export default AktivitetTidspunkt;
