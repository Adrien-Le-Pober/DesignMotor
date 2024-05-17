import { Action } from "./action.model";
import { Condition } from "./condition.model";

export class DiscountRule {
    constructor(
        public id: number,
        public name: string,
        public description?: string,
        public conditions?: Condition[],
        public actions?: Action[]
    ) {}
}