class AktivitetTag {
    id: number;
    title: string;
    navn: string;
    beskrivelse: string;

    constructor(id : number, navn : string, beskrivelse : string) {
        this.id = id;
        this.title = navn;
        this.navn = navn;
        this.beskrivelse = beskrivelse;
    }

}



export default AktivitetTag;