import Innslag from './Innslag';
import VideresendingNominasjon from './VideresendingNominasjon';

export type TittelMedVideresendingNominasjoner = {
    id: number;
    navn: string;
    nominasjoner: VideresendingNominasjon[];
};

/**
 * Svar fra nominasjoner/getAlleNominasjoner: innslag → titler[].nominasjoner,
 * eller uten titler: innslag → nominasjoner.
 */
export class InnslagNominasjonGruppe {
    innslag: Innslag;
    titler: TittelMedVideresendingNominasjoner[];
    nominasjonerUtenTitler: VideresendingNominasjon[];
    expanded: boolean = false;

    constructor(
        innslag: Innslag,
        titler: TittelMedVideresendingNominasjoner[],
        nominasjonerUtenTitler: VideresendingNominasjon[]
    ) {
        this.innslag = innslag;
        this.titler = titler;
        this.nominasjonerUtenTitler = nominasjonerUtenTitler;
    }

    getStatusPerGruppe(): string {
        const rank: Record<string, number> = {
            [VideresendingNominasjon.STATUS_HOS_MOTTAKER]: 0,
            [VideresendingNominasjon.STATUS_HOS_DELTAKER]: 1,
            [VideresendingNominasjon.STATUS_HOS_AVSENDER]: 2,
            [VideresendingNominasjon.STATUS_GODKJENT]: 3,
        };

        let bestStatus = 'Ukjent';
        let bestRank = Number.POSITIVE_INFINITY;

        const consider = (status: string) => {
            const r = rank[status];
            if (r === undefined) {
                return;
            }
            if (r < bestRank) {
                bestRank = r;
                bestStatus = status;
            }
        };

        for (const nominasjon of this.nominasjonerUtenTitler) {
            consider(nominasjon.getStatus());
        }

        for (const titel of this.titler) {
            for (const nominasjon of titel.nominasjoner) {
                consider(nominasjon.getStatus());
            }
        }

        return bestStatus;
    }

    static parseAlleNominasjonerResponse(raw: unknown): InnslagNominasjonGruppe[] {
        const r = raw as Record<string, unknown> | unknown[] | null | undefined;
        const list: unknown[] = Array.isArray(r) ? r : Object.values(r ?? {});

        return list.map((gruppeRaw: any) => {
            const innslag = Innslag.fromAjax(gruppeRaw.innslag ?? {});
            const titlerRå = Array.isArray(gruppeRaw.titler) ? gruppeRaw.titler : [];
            const titler: TittelMedVideresendingNominasjoner[] = titlerRå.map((t: any) => ({
                id: Number(t.id) || 0,
                navn: (t.navn ?? '') as string,
                nominasjoner: (Array.isArray(t.nominasjoner) ? t.nominasjoner : []).map((n: any) =>
                    VideresendingNominasjon.fromAjax(n, innslag)
                ),
            }));
            const nominasjonerUtenTitler = (Array.isArray(gruppeRaw.nominasjoner) ? gruppeRaw.nominasjoner : []).map(
                (n: any) => VideresendingNominasjon.fromAjax(n, innslag)
            );
            return new InnslagNominasjonGruppe(innslag, titler, nominasjonerUtenTitler);
        });
    }
}

export default InnslagNominasjonGruppe;
