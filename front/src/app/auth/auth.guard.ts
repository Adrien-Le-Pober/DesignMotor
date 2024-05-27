import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from './auth.service';
import { Observable, map, take } from 'rxjs';

@Injectable({
    providedIn: 'root'
})
export class AuthGuard {

    constructor(private authService: AuthService, private router: Router) {}

    canActivate(): Observable<boolean> {
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