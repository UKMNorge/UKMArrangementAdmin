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

    public getHendelser(): Hendelse[] {
        return this.hendelser;
    }

    public hasHendelse(id: number): boolean {
        return this.hendelser.some(hendelse => hendelse.id === id);
    }

}



export default HendelseGruppe;