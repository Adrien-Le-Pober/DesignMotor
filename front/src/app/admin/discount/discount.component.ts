import { Component } from '@angular/core';
import { DiscountFormComponent } from './discount-form/discount-form.component';
import { DiscountService } from './discount.service';
import { Discount } from '../../models/discount.model';
import { CommonModule } from '@angular/common';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-discount',
  standalone: true,
  imports: [
    DiscountFormComponent,
    CommonModule
  ],
  templateUrl: 'discount.component.html',
  styles: ``
})
export class DiscountComponent {
  private requestSubscription: Subscription | undefined;
  public discountList: Discount[];

  constructor(
    private discountService: DiscountService
  ) { }

  ngOnInit() {
    this.requestSubscription = this.discountService.getDiscountList()
    .subscribe(discountList => {
      this.discountList = discountList;
    });
  }

  cancelDiscount(discountId: number) {
    this.discountService.cancelDiscountById(discountId)
      .subscribe();
  }
  
  ngOnDestroy(): void {
    if (this.requestSubscription) {
      this.requestSubscription.unsubscribe();
    }
  }
}
