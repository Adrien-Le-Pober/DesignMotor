import { Component } from '@angular/core';
import { DiscountFormComponent } from './discount-form/discount-form.component';
import { DiscountService } from './discount.service';
import { Discount } from '../../models/discount.model';
import { CommonModule } from '@angular/common';
import { Subject, takeUntil } from 'rxjs';

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
  private unsubscribe$ = new Subject<void>();
  public discountList: Discount[];
  public isLoading: boolean = false;
  public isRequestPending: boolean = false;

  constructor(
    private discountService: DiscountService
  ) { }

  ngOnInit() {
    this.loadDiscounts();
  }

  loadDiscounts() {
    this.isLoading = true;
    this.discountService.getDiscountList()
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe(discountList => {
        this.discountList = discountList;
        this.isLoading = false;
      });
  }

  cancelDiscount(discountId: number) {
    this.isRequestPending = true;
    this.discountService.cancelDiscountById(discountId)
      .subscribe(() => {
        this.loadDiscounts();
        this.isRequestPending = false;
      });
  }

  onDiscountAdded() {
    this.loadDiscounts();
  }
  
  ngOnDestroy(): void {
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
  }
}
