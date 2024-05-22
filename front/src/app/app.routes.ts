import { Routes } from '@angular/router';
import { AdminComponent } from './admin/admin.component';
import { CatalogComponent } from './catalog/catalog.component';
import { ShowVehicleComponent } from './vehicle/show-vehicle/show-vehicle.component';
import { PageNotFoundComponent } from './page-not-found/page-not-found.component';

export const routes: Routes = [
    { path: 'admin', component: AdminComponent },
    { path: 'catalog', component: CatalogComponent },
    { path: 'vehicle/:id', component: ShowVehicleComponent },
    { path: '', redirectTo: 'catalog', pathMatch: 'full' },
    { path: '**', component: PageNotFoundComponent }
];
