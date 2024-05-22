import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-page-not-found',
  standalone: true,
  imports: [
    RouterModule
  ],
  template: `
    <a routerLink="/" routerLinkActive="active-link">Retour à l'accueil</a>
  `,
  styles: ``
})
export class PageNotFoundComponent {

}
