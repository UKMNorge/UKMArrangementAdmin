class Person {
    id: number;
    fornavn: string;
    etternavn: string;
    mobil: string | null;
    epost: string | null;

    constructor(
        id: number,
        fornavn: string,
        etternavn: string,
        mobil: string | null = null,
        epost: string | null = null,
    ) {
        this.id = id;
        this.fornavn = fornavn ?? '';
        this.etternavn = etternavn ?? '';
        this.mobil = mobil ?? null;
        this.epost = epost ?? null;
    }

    static fromAjax(row: any): Person {
        const id = toInt(row.id) ?? 0;
        const fornavn = (row.fornavn ?? row.firstname ?? '') as string;
        const etternavn = (row.etternavn ?? row.lastname ?? '') as string;

        return new Person(id, fornavn, etternavn, row.mobil ?? row.phone ?? null, row.epost ?? row.email ?? null);
    }

    public getNavn(): string {
        return `${this.fornavn} ${this.etternavn}`.trim();
    }
}

function toInt(v: unknown): number | null {
    if (v === null || v === undefined || v === '') {
        return null;
    }
    const n = typeof v === 'number' ? v : Number(v);
    return Number.isFinite(n) ? Math.trunc(n) : null;
}

export default Person;
