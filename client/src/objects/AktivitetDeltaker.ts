import Aktivitet from "./Aktivitet";

class AktivitetDeltaker {
    loading : boolean = false;
    mobil : string;
    navn : string;
    beskrivelse : string;

    private spaInteraction = (<any>window).spaInteraction; // Definert i main.ts


    constructor(mobil : string, navn : string, beskrivelse : string) {
        this.mobil = mobil;
        this.navn = navn;
        this.beskrivelse = beskrivelse;
    }
}

export default AktivitetDeltaker;
