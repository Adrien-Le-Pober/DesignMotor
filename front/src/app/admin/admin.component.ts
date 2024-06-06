import { Component } from '@angular/core';
import { DiscountComponent } from './discount/discount.component';
import { DiscountRulesComponent } from './discount-rules/discount-rules.component';

@Component({
  selector: 'app-admin',
  standalone: true,
  imports: [DiscountComponent, DiscountRulesComponent],
  templateUrl: 'admin.component.html',
  styleUrl: 'admin.component.scss'
})
export class AdminComponent {

}
