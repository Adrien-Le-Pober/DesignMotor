import { Routes } from '@angular/router';
import { AdminComponent } from './admin/admin.component';
import { CatalogComponent } from './catalog/catalog.component';

export const routes: Routes = [
    { path: 'admin', component: AdminComponent },
    { path: 'catalog', component: CatalogComponent },
    { path: '', redirectTo: 'catalog', pathMatch: 'full' },
];
