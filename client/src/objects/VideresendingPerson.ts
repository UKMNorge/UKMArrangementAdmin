export default class VideresendingPerson {
    id: number;
    fornavn: string;
    etternavn: string;
    mobil: string | number;
    alder: number;

    constructor(
        id: number,
        fornavn: string | null,
        etternavn: string | null,
        mobil: string | number | null,
        alder: number | null,
    ) {
        this.id = id;
        this.fornavn = (fornavn ?? '').toString();
        this.etternavn = (etternavn ?? '').toString();
        this.mobil = mobil == null ? '' : String(mobil);
        this.alder = alder ?? 0;
    }

    static fromAjax(raw: unknown): VideresendingPerson | null {
        if (raw == null) {
            return null;
        }
        if (typeof raw !== 'object') {
            return null;
        }
        const row = raw as Record<string, unknown>;

        const id = toInt(row.id) ?? toInt(row.p_id) ?? 0;
        const fornavn = asStringOrNull(row.fornavn ?? row.fornavn);
        const etternavn = asStringOrNull(row.etternavn ?? row.etternavn);
        const mobil = (row.mobil ?? row.phone ?? row.p_phone) as unknown;
        const alder = toInt(row.alder) ?? 0;

        return new VideresendingPerson(
            id,
            fornavn,
            etternavn,
            mobil as any,
            alder,
        );
    }

    getNavn(): string {
        const navn = `${this.fornavn} ${this.etternavn}`.trim();
        return navn.length ? navn : `Person #${this.id}`;
    }
}

function toInt(v: unknown): number | null {
    if (v === null || v === undefined || v === '') {
        return null;
    }
    const n = typeof v === 'number' ? v : Number(v);
    return Number.isFinite(n) ? Math.trunc(n) : null;
}

function asStringOrNull(v: unknown): string | null {
    if (v === null || v === undefined) {
        return null;
    }
    const s = String(v).trim();
    return s.length ? s : null;
}

