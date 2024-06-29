import { Vehicle } from "../models/vehicle.model";

export interface CartItem {
    product: Vehicle;
    quantity: number;
}