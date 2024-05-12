import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { CatalogComponent } from './catalog/catalog.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, CatalogComponent],
  template: `
    <h1>Bienvenue sur {{title}}!</h1>
    <app-catalog></app-catalog>

    <router-outlet />
  `,
  styles: [],
})
export class AppComponent {
  title = 'Design Motor';
}