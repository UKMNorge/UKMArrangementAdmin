class Fylke {
    id: number;
    navn: string;

    constructor(id: number, navn: string) {
        this.id = id;
        this.navn = navn ?? '';
    }

}

export default Fylke;
