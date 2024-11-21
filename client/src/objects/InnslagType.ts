class InnslagType {
    id: string;
    key : string;
    title: string;
    antall: number;
    isPerson: boolean;

    constructor(id : string, key : string, title : string, antall: number, isPerson: boolean) {
        this.id = id;
        this.key = key;
        this.title = title;
        this.antall = antall;
        this.isPerson = isPerson;

    }
}

export default InnslagType;