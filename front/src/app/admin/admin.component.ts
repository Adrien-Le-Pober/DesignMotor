import { Component } from '@angular/core';
import { DiscountComponent } from './discount/discount.component';
import { DiscountRulesComponent } from './discount-rules/discount-rules.component';

@Component({
  selector: 'app-admin',
  standalone: true,
  imports: [DiscountComponent, DiscountRulesComponent],
  template: `
    <h1>
      administration du site
    </h1>
    <app-discount/>
    <hr>
    <app-discount-rules/>
  `,
  styles: ``
})
export class AdminComponent {

}
