import { Component, EventEmitter, Output, ViewChild } from '@angular/core';
import { FormsModule, NgForm } from '@angular/forms';
import { DiscountService } from '../discount.service';
import { Discount } from '../../../models/discount.model';
import { CommonModule } from '@angular/common';
import { Subject, takeUntil } from 'rxjs';

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
  private unsubscribe$ = new Subject<void>();
  public discount: Discount = new Discount(0, 0, 0);
  public successMessage: string|undefined;
  public errorMessage: string|undefined;
  public isRequestPending = false;

  @ViewChild('discountForm') discountForm: NgForm;
  @Output() discountAdded = new EventEmitter<void>();

  constructor(
    private discountService: DiscountService,
  ) {}
  
  onSubmit() {
    this.isRequestPending = true;
    this.successMessage = '';
    this.errorMessage = '';
    this.discountService.addDiscount(this.discount)
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe({
        next: (response) => {
          this.successMessage = response.successMessage;
          this.isRequestPending = false;
          this.discountForm.resetForm();
          this.discountAdded.emit();
        },
        error: (error) => {
          this.errorMessage = error.error.error;
          this.isRequestPending = false;
        }
      });
  }

  ngOnDestroy(): void {
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
  }
}
