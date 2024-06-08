import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Router, RouterStateSnapshot } from '@angular/router';
import { AuthService } from './auth.service';
import { Observable, map, take } from 'rxjs';

@Injectable({
    providedIn: 'root'
})
export class AuthGuard {

    constructor(private authService: AuthService, private router: Router) {}

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<boolean> {
        const method = route.data['authGuardMethod'];
        if (method === 'canActivateIfLoggedIn') {
            return this.canActivateIfLoggedIn();
        } else if (method === 'canActivateIfNotLoggedIn') {
            return this.canActivateIfNotLoggedIn();
        }
        return this.authService.isLoggedIn().pipe(
            take(1),
            map(isLoggedIn => {
                if (isLoggedIn) {
                    this.router.navigate(['/']);
                    return false;
                }
                return true;
            })
        );
    }

    private canActivateIfLoggedIn(): Observable<boolean> {
        return this.authService.isLoggedIn().pipe(
            take(1),
            map(isLoggedIn => {
                if (!isLoggedIn) {
                    this.router.navigate(['/connexion']);
                    return false;
                }
                return true;
            })
        );
    }

    private canActivateIfNotLoggedIn(): Observable<boolean> {
        return this.authService.isLoggedIn().pipe(
            take(1),
            map(isLoggedIn => {
                if (isLoggedIn) {
                    this.router.navigate(['/']);
                    return false;
                }
                return true;
            })
        );
    }
}