import Person from './Person';

export type InnslagEkstra = {
    sjanger?: string;
    beskrivelse?: string;
    omrade_navn?: string;
    /** API kan sende bool eller 0/1 */
    har_titler?: boolean | number | string;
    type_key?: string;
    fylke_fra?: string;
    fylke_fra_id?: number;
};

class Innslag {
    id: number;
    navn: string;
    innslag_type: string;
    personer: Person[];
    sjanger: string;
    beskrivelse: string;
    omrade_navn: string;
    har_titler: boolean;
    type_key: string;
    fylke_fra: string;
    fylke_fra_id: number;

    constructor(id: number, navn: string, innslag_type: string, personer: Person[] = [], ekstra: InnslagEkstra = {}) {
        this.id = id;
        this.navn = navn ?? '';
        this.innslag_type = innslag_type ?? '';
        this.personer = personer;
        this.sjanger = ekstra.sjanger ?? '';
        this.beskrivelse = ekstra.beskrivelse ?? '';
        this.omrade_navn = ekstra.omrade_navn ?? '';
        this.har_titler = parseHarTitler(ekstra.har_titler);
        this.type_key = ekstra.type_key ?? '';
        this.fylke_fra = ekstra.fylke_fra ?? '';
        this.fylke_fra_id = ekstra.fylke_fra_id ?? 0;
    }

    static fromAjax(row: any): Innslag {
        const id = toInt(row.id) ?? 0;
        const navn = (row.navn ?? row.name ?? row.tittel ?? row.title ?? '') as string;
        const innslag_type = (row.innslag_type ?? row.type ?? row.type_key ?? '') as string;
        const personer = Array.isArray(row.personer) ? row.personer.map((p: any) => Person.fromAjax(p)) : [];
        return new Innslag(id, navn, innslag_type, personer, {
            sjanger: row.sjanger,
            beskrivelse: row.beskrivelse,
            omrade_navn: row.omrade_navn,
            har_titler: row.har_titler,
            type_key: row.type_key,
            fylke_fra: row.fylke_fra,
            fylke_fra_id: row.fylke_fra_id,
        });
    }
}

function parseHarTitler(v: unknown): boolean {
    if (v === true || v === 1 || v === '1') {
        return true;
    }
    if (v === false || v === 0 || v === '0' || v === '') {
        return false;
    }
    return Boolean(v);
}

function toInt(v: unknown): number | null {
    if (v === null || v === undefined || v === '') {
        return null;
    }
    const n = typeof v === 'number' ? v : Number(v);
    return Number.isFinite(n) ? Math.trunc(n) : null;
}

export default Innslag;
