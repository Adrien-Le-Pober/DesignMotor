import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output, ViewChild } from '@angular/core';
import { FormArray, FormBuilder, FormGroup, FormsModule, NgForm, ReactiveFormsModule, Validators } from '@angular/forms';
import { DiscountRule } from '../../../models/discount-rule.model';
import { DiscountRulesService } from '../discount-rules.service';
import { Condition } from '../../../models/condition.model';
import { Action } from '../../../models/action.model';
import { Brand } from '../../../models/brand.model';
import { DayOfWeek } from '../../../interfaces/day-of-week.interface';
import { Subject, takeUntil } from 'rxjs';

@Component({
  selector: 'app-discount-rules-form',
  standalone: true,
  imports: [
    FormsModule,
    CommonModule,
    ReactiveFormsModule
  ],
  templateUrl: 'discount-rules-form.component.html',
  styles: ``
})
export class DiscountRulesFormComponent {
  private unsubscribe$ = new Subject<void>();
  public discountRuleForm: FormGroup;
  public availableConditions = [
    { value : 'day_of_week', select: 'Jour de la semaine' },
    { value : 'brand', select: 'Marque' },
  ];
  public daysOfWeek: DayOfWeek[] = [];
  public brandList: Brand[];
  public errorMessage: string = '';

  @Input() discountRule: DiscountRule | null = null;
  @Input() isEditMode: boolean = false;
  @Input() isRequestPending: boolean = false;
  @Output() save = new EventEmitter<DiscountRule>();

  constructor(private fb: FormBuilder, private discountRuleService: DiscountRulesService) {
    this.discountRuleForm = this.fb.group({
      name: ['', Validators.required],
      description: [''],
      conditions: this.fb.array([]),
      actions: this.fb.array([])
    });
  }

  ngOnInit() {
    this.daysOfWeek = this.discountRuleService.getDaysOfWeek();
    this.fetchBrands();

    if (this.discountRule) {
      this.populateForm(this.discountRule);
    }
  }

  fetchBrands() {
    this.discountRuleService.getBrandList()
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe(brandList => {
        this.brandList = brandList;
      });
  }

  populateForm(discountRule: DiscountRule): void {
    this.discountRuleForm.patchValue({
      name: discountRule.name,
      description: discountRule.description
    });
    discountRule.conditions?.forEach(condition => this.addCondition(condition));
    discountRule.actions?.forEach(action => this.addAction(action));
  }

  get conditions(): FormArray {
    return this.discountRuleForm.get('conditions') as FormArray;
  }

  onTypeChange(index: number): void {
    const condition = this.conditions.at(index) as FormGroup;
    condition.get('value')?.setValue([]); // Réinitialise les valeurs lorsque le type change
  }

  getValuesForType(type: string) {
    switch (type) {
      case 'day_of_week':
        return this.daysOfWeek;
      case 'brand':
        return this.brandList;
      default:
        return [];
    }
  }
  
  onCheckboxChange(event: any, index: number): void {
    const condition = this.conditions.at(index) as FormGroup;
    const valueArray = condition.get('value')?.value as string[];
  
    if (event.target.checked) {
      valueArray.push(event.target.value);
    } else {
      const idx = valueArray.indexOf(event.target.value);
      if (idx > -1) {
        valueArray.splice(idx, 1);
      }
    }
  
    condition.get('value')?.setValue(valueArray);
  }

  addCondition(conditionData?: Condition): void {
    const conditionForm = this.fb.group({
      type: [conditionData ? conditionData.type : '', Validators.required],
      value: [conditionData ? conditionData.value : '', Validators.required]
    });

    this.conditions.push(conditionForm);
  }

  removeCondition(index: number): void {
    this.conditions.removeAt(index);
  }

  get actions(): FormArray {
    return this.discountRuleForm.get('actions') as FormArray;
  }

  addAction(actionData?: Action): void {
    const actionForm = this.fb.group({
      type: [actionData ? actionData.type : 'discount', Validators.required],
      value: [actionData ? actionData.value : '', Validators.required]
    });

    this.actions.push(actionForm);
  }

  removeAction(index: number): void {
    this.actions.removeAt(index);
  }

  onSubmit(): void {
    if (this.discountRuleForm.valid) {
      this.errorMessage = '';
      const hasConditions = this.conditions.controls.length > 0;
      const hasActions = this.actions.controls.length > 0;
    
      if (!hasConditions || !hasActions) {
        this.errorMessage = 'Veuillez sélectionner au moins une condition et une action.';
        return;
      }
      this.isRequestPending = true;

      const formValue = this.discountRuleForm.value;
      const discountRule = new DiscountRule(
        this.discountRule?.id || 0,
        formValue.name,
        formValue.description,
        formValue.conditions.map((condition: any) => new Condition(
          condition.id || 0,
          condition.type,
          condition.value
        )),
        formValue.actions.map((action: any) => new Action(
            action.id || 0,
            action.type,
            action.value
        ))
      );
      this.discountRuleService.createDiscountRule(discountRule).subscribe({
        next: response => {
          this.discountRuleForm.reset();
          this.conditions.clear();
          this.actions.clear();
          this.errorMessage = '';
          this.isRequestPending = false;
          this.save.emit();
        },
        error: error => {
          this.errorMessage = error.error.error;
          this.isRequestPending = false;
        }
      });
    } else {
      this.discountRuleForm.markAllAsTouched();
      this.errorMessage = '';
    }
  }

  ngOnDestroy(): void {
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
  }
}
