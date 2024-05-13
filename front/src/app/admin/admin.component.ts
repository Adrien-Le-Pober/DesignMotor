import { Component } from '@angular/core';
import { DiscountComponent } from './discount/discount.component';

@Component({
  selector: 'app-admin',
  standalone: true,
  imports: [DiscountComponent],
  template: `
    <h1>
      administration du site
    </h1>
    <app-discount/>
  `,
  styles: ``
})
export class AdminComponent {

}
