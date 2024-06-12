import { Component, ViewChild } from '@angular/core';
import { DiscountRulesFormComponent } from './discount-rules-form/discount-rules-form.component';
import { DiscountRule } from '../../models/discount-rule.model';
import { DiscountRulesService } from './discount-rules.service';
import { CommonModule } from '@angular/common';
import { Subject, takeUntil } from 'rxjs';

@Component({
  selector: 'app-discount-rules',
  standalone: true,
  imports: [
    DiscountRulesFormComponent,
    CommonModule
  ],
  templateUrl: 'discount-rules.component.html',
  styles: ``
})
export class DiscountRulesComponent {
  private unsubscribe$ = new Subject<void>();
  discountRuleList: DiscountRule[];
  selectedDiscountRule: DiscountRule | null = null;
  isEditMode: boolean = false;
  isRequestPending: boolean = false;
  isLoading: boolean = false;
  successMessage: string = '';
  errorMessage: string = '';

  @ViewChild(DiscountRulesFormComponent) formComponent!: DiscountRulesFormComponent;

  constructor(private discountRuleService: DiscountRulesService) { }

  ngOnInit(): void {
    this.loadDiscountRules();
  }

  loadDiscountRules(): void {
    this.isLoading = true;
    this.discountRuleService.getDiscountRules()
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe(data => {
        this.discountRuleList = data;
        this.isLoading = false;
    });
  }

  onCreate(): void {
    this.selectedDiscountRule = null;
    this.isEditMode = false;
  }

  onEdit(discountRule: DiscountRule): void {
    this.selectedDiscountRule = discountRule;
    this.isEditMode = true;
  }

  onCancelEdit(): void {
    this.selectedDiscountRule = null;
    this.isEditMode = false;
  }

  onSave(discountRule: DiscountRule): void {
    this.successMessage = '';
    this.errorMessage = '';
    this.isRequestPending = true;
    if (this.isEditMode) {
      this.discountRuleService.updateDiscountRule(discountRule).subscribe({
        next: response => {
          this.successMessage = response.successMessage;
          this.loadDiscountRules();
          this.selectedDiscountRule = null;
          this.isEditMode = false;
          this.isRequestPending = false;
        },
        error: error => {
          this.errorMessage = error.error.errorMessage;
          this.isRequestPending = false;
        }
      });
    } else {
      this.discountRuleService.createDiscountRule(discountRule).subscribe({
        next: response => {
          this.successMessage = response.successMessage;
          this.loadDiscountRules();
          this.isRequestPending = false;
          this.formComponent.resetForm();
        },
        error: error => {
          this.errorMessage = error.error.errorMessage;
          this.isRequestPending = false;
        }
      });
    }
  }

  onDelete(discountRule: DiscountRule) {
    this.isRequestPending = true;
    this.discountRuleService.deleteDiscountRule(discountRule.id).subscribe({
        next: response => {
          this.successMessage = response.successMessage;
          this.loadDiscountRules();
          this.isRequestPending = false;
        },
        error: () => {
          this.errorMessage = "Une erreur est survenue";
          this.isRequestPending = false;
        }
      });
  }

  onSuccessMessage(message: string): void {
    this.successMessage = message;
  }

  onErrorMessage(message: string): void {
    this.errorMessage = message;
  }

  ngOnDestroy(): void {
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
  }
}
