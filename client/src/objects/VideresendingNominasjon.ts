import Innslag from './Innslag';

export type VideresendingNominasjonStatus =
    | 'hos-avsender'
    | 'hos-mottaker'
    | 'hos-deltaker'
    | 'godkjent'
    | 'Ukjent';

class VideresendingNominasjon {
    static readonly STATUS_HOS_AVSENDER = 'hos-avsender' as const;
    static readonly STATUS_HOS_MOTTAKER = 'hos-mottaker' as const;
    static readonly STATUS_HOS_DELTAKER = 'hos-deltaker' as const;
    static readonly STATUS_GODKJENT = 'godkjent' as const;

    id: number;
    p_id: number | null;
    b_id: number | null;
    t_id: number | null;
    season: number;
    innslag_type: string;
    arrangement_fra: number;
    arrangement_til: number;
    godkjent: boolean;
    beskrivelse: string;
    status: VideresendingNominasjonStatus;
    active: boolean;
    sporsmal: string;
    svar: string;
    innslag: Innslag;
    expanded: boolean = false;

    constructor(
        id: number,
        p_id: number | null,
        b_id: number | null,
        t_id: number | null,
        season: number,
        innslag_type: string,
        arrangement_fra: number,
        arrangement_til: number,
        godkjent: boolean,
        beskrivelse: string | null,
        status: VideresendingNominasjonStatus | null,
        active: boolean,
        sporsmal: string | null,
        svar: string | null,
        innslag: Innslag
    ) {
        this.id = id;
        this.p_id = p_id;
        this.b_id = b_id;
        this.t_id = t_id;
        this.season = season;
        this.innslag_type = innslag_type ?? '';
        this.arrangement_fra = arrangement_fra;
        this.arrangement_til = arrangement_til;
        this.godkjent = godkjent;
        this.beskrivelse = beskrivelse ?? '';
        this.status = (status ?? 'Ukjent') as VideresendingNominasjonStatus;
        this.active = active;
        this.sporsmal = sporsmal ?? '';
        this.svar = svar ?? '';
        this.innslag = innslag;
    }

    static getGyldigeStatuser(): Array<
        | typeof VideresendingNominasjon.STATUS_HOS_AVSENDER
        | typeof VideresendingNominasjon.STATUS_HOS_MOTTAKER
        | typeof VideresendingNominasjon.STATUS_HOS_DELTAKER
        | typeof VideresendingNominasjon.STATUS_GODKJENT
    > {
        return [
            VideresendingNominasjon.STATUS_HOS_AVSENDER,
            VideresendingNominasjon.STATUS_HOS_MOTTAKER,
            VideresendingNominasjon.STATUS_HOS_DELTAKER,
            VideresendingNominasjon.STATUS_GODKJENT,
        ];
    }

    static isGyldigStatus(status: string | null | undefined): status is Exclude<VideresendingNominasjonStatus, 'Ukjent'> {
        if (status == null) {
            return false;
        }
        return (VideresendingNominasjon.getGyldigeStatuser() as readonly string[]).includes(status);
    }

    /**
     * @param row Nominasjon-rad fra API (getArrObj)
     * @param innslagValgfritt Brukes når nominasjon ikke er nestet under row.innslag (gruppert svar: innslag → titler → nominasjoner)
     */
    static fromAjax(row: any, innslagValgfritt?: Innslag): VideresendingNominasjon {
        const status: VideresendingNominasjonStatus =
            VideresendingNominasjon.isGyldigStatus(row.status) ? row.status : 'Ukjent';

        const innslag =
            innslagValgfritt ??
            (row.innslag != null ? Innslag.fromAjax(row.innslag) : new Innslag(toInt(row.b_id) ?? 0, '', row.innslag_type ?? ''));

        return new VideresendingNominasjon(
            toInt(row.id) ?? 0,
            toNullableInt(row.p_id),
            toNullableInt(row.b_id),
            toNullableInt(row.t_id),
            toInt(row.season) ?? 0,
            row.innslag_type ?? '',
            toInt(row.arrangement_fra) ?? 0,
            toInt(row.arrangement_til) ?? 0,
            toBool(row.godkjent),
            row.beskrivelse,
            status,
            toBool(row.active, true),
            row.sporsmal,
            row.svar,
            innslag
        );
    }

    public getStatus(): VideresendingNominasjonStatus {
        return this.status ?? 'Ukjent';
    }

    public isActive(): boolean {
        return this.active;
    }
}

function toInt(v: unknown): number | null {
    if (v === null || v === undefined || v === '') {
        return null;
    }
    const n = typeof v === 'number' ? v : Number(v);
    return Number.isFinite(n) ? Math.trunc(n) : null;
}

function toNullableInt(v: unknown): number | null {
    return toInt(v);
}

function toBool(v: unknown, defaultValue = false): boolean {
    if (v === null || v === undefined || v === '') {
        return defaultValue;
    }
    if (typeof v === 'boolean') {
        return v;
    }
    if (typeof v === 'number') {
        return v === 1;
    }
    const s = String(v).toLowerCase().trim();
    if (s === '1' || s === 'true' || s === 'yes') {
        return true;
    }
    if (s === '0' || s === 'false' || s === 'no') {
        return false;
    }
    return defaultValue;
}

export default VideresendingNominasjon;