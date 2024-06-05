import { Routes } from '@angular/router';
import { AdminComponent } from './admin/admin.component';
import { CatalogComponent } from './catalog/catalog.component';
import { ShowVehicleComponent } from './vehicle/show-vehicle/show-vehicle.component';
import { PageNotFoundComponent } from './page-not-found/page-not-found.component';
import { AuthGuard } from './auth/auth.guard';
import { ResendConfirmationEmailComponent } from './user/sign-in/resend-confirmation-email/resend-confirmation-email.component';
import { ResetPasswordComponent } from './user/reset-password/reset-password.component';
import { ForgotPasswordComponent } from './user/forgot-password/forgot-password.component';
import { SignInComponent } from './user/sign-in/sign-in.component';
import { LoginComponent } from './auth/login/login.component';

export const routes: Routes = [
    { path: 'admin', component: AdminComponent },
    { path: 'connection', component: LoginComponent, canActivate: [AuthGuard] },
    { path: 'inscription', component: SignInComponent, canActivate: [AuthGuard] },
    { path: 'resend-confirmation-email', component: ResendConfirmationEmailComponent },
    { path: 'reinitialiser-mot-de-passe/:token', component: ResetPasswordComponent },
    { path: 'mot-de-passe-oublie', component: ForgotPasswordComponent },
    { path: 'catalog', component: CatalogComponent },
    { path: 'vehicle/:id', component: ShowVehicleComponent },
    { path: '', redirectTo: 'catalog', pathMatch: 'full' },
    { path: '404', component: PageNotFoundComponent },
    { path: '**', redirectTo: '/404' }
];
