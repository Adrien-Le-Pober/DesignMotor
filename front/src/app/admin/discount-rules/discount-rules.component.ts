import { Component } from '@angular/core';
import { DiscountRulesFormComponent } from './discount-rules-form/discount-rules-form.component';
import { DiscountRule } from '../../models/discount-rule.model';
import { DiscountRulesService } from './discount-rules.service';
import { CommonModule } from '@angular/common';

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
  discountRuleList: DiscountRule[];
  selectedDiscountRule: DiscountRule | null = null;
  isEditMode: boolean = false;
  isRequestPending: boolean = false;

  constructor(private discountRuleService: DiscountRulesService) { }

  ngOnInit(): void {
    this.loadDiscountRules();
  }

  loadDiscountRules(): void {
    this.discountRuleService.getDiscountRules().subscribe(data => {
      this.discountRuleList = data;
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
    this.isRequestPending = true;
    if (this.isEditMode) {
      this.discountRuleService.updateDiscountRule(discountRule).subscribe(() => {
        this.loadDiscountRules();
        this.selectedDiscountRule = null;
        this.isEditMode = false;
        this.isRequestPending = false;
      });
    } else {
      this.discountRuleService.createDiscountRule(discountRule).subscribe(() => {
        this.loadDiscountRules();
        this.isRequestPending = false;
      });
    }
  }

  onDelete(discountRule: DiscountRule): void {
    this.isRequestPending = true;
    this.discountRuleService.deleteDiscountRule(discountRule.id).subscribe(() => {
      this.loadDiscountRules();
      this.isRequestPending = false;
    });
  }
}