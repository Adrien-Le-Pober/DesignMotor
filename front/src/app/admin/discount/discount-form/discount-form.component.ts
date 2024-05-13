import { Component } from '@angular/core';
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

  constructor(
    private discountService: DiscountService,
  ) {}
  
  onSubmit() {
    this.discountService.addDiscount(this.discount)
      .subscribe(successMessage => {
        this.successMessage = successMessage;
      });
  }
}
