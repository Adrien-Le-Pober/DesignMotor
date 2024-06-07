import { Component, EventEmitter, Output } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { DiscountService } from '../discount.service';
import { Discount } from '../../../models/discount.model';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-discount-form',
  standalone: true,
  imports: [
    FormsModule,
    CommonModule,
  ],
  templateUrl: 'discount-form.component.html',
  styles: ``
})
export class DiscountFormComponent {
  public discount: Discount = new Discount(0, 0, 0);
  public successMessage: string|undefined;
  public errorMessage: string|undefined;
  public isRequestPending = false;

  @Output() discountAdded = new EventEmitter<void>();

  constructor(
    private discountService: DiscountService,
  ) {}
  
  onSubmit() {
    this.isRequestPending = true;
    this.discountService.addDiscount(this.discount)
      .subscribe({
        next: (successMessage) => {
          this.successMessage = successMessage;
          this.isRequestPending = false;
          this.discountAdded.emit();
        },
        error: (error) => {
          this.errorMessage = error.error.error;
          this.isRequestPending = false;
        }
      });
  }
}
