export class Vehicle {
    constructor(
        public id: number,
        public brand: string,
        public model: string,
        public color: string[],
        public power: string,
        public space?: string,
        public image?: string
    ) {}
}