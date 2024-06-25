import { Vehicle } from "./vehicle.model";

export class VehicleDescription extends Vehicle {
    constructor(
        id: number,
        price: number,
        soldedPrice: number,
        public power: string,
        public space: string|null,
        brand: string,
        public brandDescription: string|null,
        model: string,
        public modelDescription: string|null,
        public motorization: string,
        public color: string,
        public description: string|null,
        image?: string,
    ) {
        super(id, brand, model, price, soldedPrice, image);
    }
}