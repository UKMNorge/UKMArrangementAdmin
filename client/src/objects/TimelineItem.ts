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
        console.warn(this.label);
        if(this.label) {
            if(this.label == 'warning') {
                return 'var(--as-color-primary-warning-default)';
            }
        }
        return this.finished ? 'blue' : 'grey'
    }
}

export default TimelineItem;