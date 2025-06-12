import Hendelse from "./Hendelse";

class HendelseGruppe {
    id: number;
    title: string;
    beskrivelse: string;
    expanded: boolean = false;
    hendelser: Hendelse[] = [];
    tag : string = null;

    constructor(id : number, title : string, beskrivelse : string, hendelser: Hendelse[] = [], tag: string = null) {
        this.id = id;
        this.title = title;
        this.beskrivelse = beskrivelse;
        this.hendelser = hendelser;
        this.tag = tag;
    }

    public getHendelser(): Hendelse[] | number[] {
        if (this.hendelser.length === 0) {
            return [];
        }

        // Check if the first hendelse is an object
        if (this.hendelser[0] && typeof this.hendelser[0] === 'object') {
            // Return array of IDs
            return this.hendelser.map(hendelse => hendelse.id);
        }

        return this.hendelser;
    }

    public hasHendelse(id: number): boolean {
        return this.hendelser.some(hendelse => hendelse.id === id);
    }

}



export default HendelseGruppe;