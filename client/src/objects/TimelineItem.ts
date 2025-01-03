class TimelineItem {
    public id: string;
    public title: string;
    public start: string;
    public end: string;
    public description: string;
    public label: string;
    public finished: boolean;
    public callback: Function|undefined;

    constructor(id: string, title: string, start: string, end: string, description: string, label: string, finished: boolean, callback?: Function) {
        this.id = id;
        this.title = title;
        this.start = start;
        this.end = end;
        this.description = description;
        this.label = label;
        this.finished = finished;
        this.callback = callback? callback : undefined;
    }

    public getDateToString() {
        let separator = this.start.length > 0 && this.end.length > 0 ? " - " : "";
        return `${this.start}${separator}${this.end}`;
    }

    public getColor() {
        if(this.label) {
            if(this.label == 'warning') {
                return 'var(--as-color-primary-warning-default)';
            }
            else if(this.label == 'current') {
                return 'var(--as-color-primary-success)';
            }
        }
        return this.finished ? 'var(--color-primary-bla-200)' : 'var(--color-primary-grey-medium)';
    }

    public getIcon() : string {
        if(this.label == 'current') {
            return 'mdi-clock-check'
        }
        else if(this.label == 'warning') {
            return 'mdi-alert'
        }
        else if(this.label == 'error') {
            return 'mdi-alert-decagram'
        }
        return this.finished ? 'mdi-check-bold' : '';
    }
}

export default TimelineItem;