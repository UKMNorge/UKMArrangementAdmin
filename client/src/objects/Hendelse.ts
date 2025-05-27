class Hendelse {
    id: number;
    title: string;
    gruppeId: number|null = null;

    constructor(id : number, title : string) {
        this.id = id;
        this.title = title;
    }

}



export default Hendelse;