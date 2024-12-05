// Create class Leder
class Leder {
    id : number;
    navn : string;
    epost : string;
    mobil : string;
    type : string;
    fraArrangementNavn : string;
    fraFylkeNavn : string;
    beskrivelse : string;
    godkjent : boolean|null;
    loading : boolean = false;

    constructor(id : number, navn : string, epost : string, mobil : string, type : string, fraArrangementNavn : string, fraFylkeNavn : string, beskrivelse : string, godkjent : boolean|null) {
        this.id = id;
        this.navn = navn;
        this.epost = epost;
        this.mobil = mobil;
        this.type = type;
        this.fraArrangementNavn = fraArrangementNavn;
        this.fraFylkeNavn = fraFylkeNavn;
        this.beskrivelse = beskrivelse;
        this.godkjent = godkjent;
    }

    public getGodkjent() : boolean|null {
        return this.godkjent;
    }

    public setGodkjent(godkjent : boolean) {
        this.godkjent = godkjent;
    }

    public getIcon() : string {
        if(this.type == 'Sykerom - andre hotell behov') {
            return "mdi-home-circle";
        }
        else {
            return "mdi-account-circle";
        }
    }

    public async save() {
        this.loading = true;
        // Save to database via API
        
        setTimeout(() => {
            this.loading = false;
            return true;
        }, 2000);
    }
}

export default Leder;
