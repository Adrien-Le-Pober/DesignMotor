import { Component, NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    FormsModule
  ],
  template: `
    <nav>
      <ul>
        <li>
          <a routerLink="/" routerLinkActive="active">Accueil</a>
        </li>
        <li>
          <a routerLink="/admin" routerLinkActive="active">Admin</a>
        </li>
      </ul>
    </nav>

    <router-outlet></router-outlet>
  `,
  styles: [],
})
export class AppComponent {
}
