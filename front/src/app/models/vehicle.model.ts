export class Vehicle {
    constructor(
        public id: number,
        public brand: string,
        public model: string,
        public price: number,
        public image?: string,
    ) {}
}