import AktivitetTidspunkt from './AktivitetTidspunkt';
import AktivitetTag from "./AktivitetTag";
import axios from 'axios';


class Aktivitet {
    loading : boolean = false;
    deleted : boolean = false;

    title : string;
    subtitle : string;

    id : number;
    navn : string;
    sted : string;
    beskrivelse : string;
    beskrivelseLeder : string;
    kursholder : string;

    plId : number;

    tags : AktivitetTag[] = [];

    image : string|null;
    uploadedImage : any;


    // Alle tidspunkter
    tidspunkter : AktivitetTidspunkt[] = [];

    public expanded : boolean = false;  
    public expandedTidspunkter : boolean = false;

    private spaInteraction = (<any>window).spaInteraction; // Definert i main.ts

    constructor(id : number, navn : string, sted : string, beskrivelse : string, beskrivelseLeder : string, kursholder : string, plId : number, tidspunkter : AktivitetTidspunkt[], tags : AktivitetTag[], image : string|null) {
        this.id = id;
        this.navn = navn;
        this.title = navn;
        this.subtitle = sted;
        this.sted = sted;
        this.setBeskrivelse(beskrivelse);
        this.setBeskrivelseLeder(beskrivelseLeder);
        this.kursholder = kursholder;
        this.plId = plId;
        this.tidspunkter = tidspunkter;
        this.tags = tags;
        this.image = image;

        this.addNewTidspubktInTheList();
    }

    public setBeskrivelse(beskrivelse : string) {
        this.beskrivelse = beskrivelse ? decodeURIComponent(beskrivelse) : '';
    }

    public setBeskrivelseLeder(beskrivelseLeder : string) {
        this.beskrivelseLeder = beskrivelseLeder ? decodeURIComponent(beskrivelseLeder) : '';
    }

    public openOrClose() {

        this.expanded = !this.expanded
    }

    public removeTidspunkt(tidspunkt : AktivitetTidspunkt) {
        var index = this.tidspunkter.indexOf(tidspunkt);
        if(index > -1) {
            this.tidspunkter.splice(index, 1);
        }
    }

    public addNewTidspubktInTheList() {
        // Only if id -1 is not in the list
        if(this.tidspunkter.find(tidspunkt => tidspunkt.id == -1) != null) {
            return;
        }

        this.tidspunkter.unshift(new AktivitetTidspunkt(
            -1,     // : number,
            '',     // : string, 
            this.getNowDate(),     // : string, 
            this.getNowDate(30),     // : string, 
            0,     // : number, 
            100,     // : number, 
            null,     // : number|null, 
            this,     // : Aktivitet, 
            [],     // : AktivitetDeltaker[],
            true,     // : boolean,
            true,     // : boolean,)
            true,     // : boolean,
            null     // : AktivitetKlokkeslett|null
        ));
    }

    private getNowDate(plusMinuttes : number = 0) : string {
        const now = new Date();

            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0'); // Months are zero-based
            const day = String(now.getDate()).padStart(2, '0');

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            const formattedDate = `${year}-${month}-${day} ${hours}:${(minutes+plusMinuttes)}:${seconds}`;

            return formattedDate;
    }

    public async create() {
        let results : any = await this.save('aktivitet/createAktivitet');

        this.id = results.id;
        this.navn = results.navn;
        this.title = results.navn;
        this.subtitle = results.sted;
        this.sted = results.sted;
        this.setBeskrivelse(results.beskrivelse);
        this.setBeskrivelseLeder(results.beskrivelseLeder);
        this.kursholder = results.kursholder;
        this.plId = results.plId;
        this.tidspunkter = []; // Assuming the last parameter

        // Add new tidspunkt placeholder
        this.addNewTidspubktInTheList();

        return results;
    }

    private async uploadImage() {
        if(this.id == -1) return;

        let isDeleted = !this.image && !this.uploadedImage;
        let isChanged = !this.image && this.uploadedImage != null;

        if(!isDeleted && !isChanged) {
            console.log('don not send');
            return;
        }

        const formData = new FormData()
        formData.append('imageFile', (!this.image && !this.uploadedImage) ? null : (this.uploadedImage ? this.uploadedImage : this.image))
        formData.append('action', 'UKMArrangementAdmin_ajax')
        formData.append('controller', 'aktivitet/uploadImageAktivitet')
        formData.append('aktivitetId', (<any>this.id))

        let response;
        try {
            response = await axios.post((<any>window).ajaxurl, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            })
            if(response.data.imageUrl == null && !response.data.isDeleted) {
                this.spaInteraction.showMessage('Feil', 'Kunne ikke laste opp bildet. Sjekk filtype eller størrelse.', 'error');
                this.image = response.data.oldUrl;
                return;
            }
            this.image = response.data.imageUrl;
        } catch (error) {
            this.spaInteraction.showMessage('Feil', (<any>error).response.data.result, 'error');
        }

        return response;
        
    }

    public async save(controllerArg : string|null = null) {
        this.loading = true;

        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: controllerArg ?? 'aktivitet/saveAktivitet',
            aktivitetId: this.id,
            navn: this.navn,
            sted: this.sted,
            beskrivelse: encodeURIComponent(this.beskrivelse),
            beskrivelseLeder: encodeURIComponent(this.beskrivelseLeder),
            kursholder : this.kursholder,
        };
        
        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        let tagsResults = null;
        if(results != null) {
            tagsResults = this.saveTags();
        }

        if(results != null && tagsResults != null) {
            this.loading = false;
            if(this.id != -1) {
                this.spaInteraction.showMessage('Lagret', 'Aktiviteten er lagret!', 'success');
            }
            this.uploadImage();
        }
        
        return results;
    }

    public async delete() {
        if(this.tidspunkter.length > 1) {
            this.spaInteraction.showMessage('Feil', 'Aktiviteten har forekomster og kan ikke slettes. Slett forekomstene først!', 'error');
            return;
        }
        this.loading = true;
        
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'aktivitet/deleteAktivitet',
            aktivitetId: this.id,
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        if(results && results.completed && results.completed == true) {
            this.loading = false;
            this.deleted = true;
        } else {
            this.spaInteraction.showMessage('Feil', 'Kunne ikke slette aktivitet: ' + (results.message ?? ''), 'error');
        }
        
        return results;
    }

    private async saveTags() {
        if(this.id == -1) {
            return;
        }
        this.loading = true;
        
        var data = {
            action: 'UKMArrangementAdmin_ajax',
            controller: 'aktivitet/updateAktivitetTags',
            aktivitetId: this.id,
            tags: this.tags.length < 1 ? [] : this.tags,
        };

        var results = await this.spaInteraction.runAjaxCall('/', 'POST', data);
        
        if(results && results.completed && results.completed == true) {
            this.loading = false;
        } else {
            this.spaInteraction.showMessage('Feil', 'Kunne ikke slette aktivitet: ' + (results.message ?? ''), 'error');
        }
        
        return results;
    }
    
}

export default Aktivitet;
