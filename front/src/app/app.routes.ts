import { Routes } from '@angular/router';
import { AdminComponent } from './admin/admin.component';
import { CatalogComponent } from './catalog/catalog.component';
import { ShowVehicleComponent } from './vehicle/show-vehicle/show-vehicle.component';
import { PageNotFoundComponent } from './page-not-found/page-not-found.component';
import { ConnectionComponent } from './connection/connection.component';
import { AuthGuard } from './auth/auth.guard';
import { ResendConfirmationEmailComponent } from './user/sign-in/resend-confirmation-email/resend-confirmation-email.component';

export const routes: Routes = [
    { path: 'admin', component: AdminComponent },
    { path: 'connection', component: ConnectionComponent, canActivate: [AuthGuard] },
    { path: 'resend-confirmation-email', component: ResendConfirmationEmailComponent },
    { path: 'catalog', component: CatalogComponent },
    { path: 'vehicle/:id', component: ShowVehicleComponent },
    { path: '', redirectTo: 'catalog', pathMatch: 'full' },
    { path: '404', component: PageNotFoundComponent },
    { path: '**', redirectTo: '/404' }
];
