import { Vehicle } from "./vehicle.model";

export class Discount {
    constructor(
        public id: number,
        public storageDuration: number,
        public rate: number,
        public vehicles?: Vehicle[]
    ) {}
}