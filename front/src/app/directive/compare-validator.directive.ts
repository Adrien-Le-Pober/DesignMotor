import { Directive, Input } from '@angular/core';
import { NG_VALIDATORS, Validator, AbstractControl, ValidationErrors } from '@angular/forms';

@Directive({
    selector: '[compare]',
    standalone: true,
    providers: [{ provide: NG_VALIDATORS, useExisting: CompareValidatorDirective, multi: true }]
})
export class CompareValidatorDirective implements Validator {
    @Input('compare') controlToCompare: string;

    validate(control: AbstractControl): ValidationErrors | null {
        if (!control.value || !this.controlToCompare) {
            return null;
        }
    
        if (control.value !== this.controlToCompare) {
            return { compare: true };
        }
    
        return null;
    }
}