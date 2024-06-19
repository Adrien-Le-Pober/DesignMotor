import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { environment } from '../../environments/environment';
import { UserService } from '../user/user.service';
import { Router } from '@angular/router';

@Injectable({ providedIn: 'root' })
export class AuthService {
    private appURL = environment.appURL;

    constructor(private http: HttpClient, private userService: UserService, private router: Router) { }

    login(email: string, password: string): Observable<any> {
        return this.http.post<any>(`${this.appURL}/login`, { email, password })
            .pipe(map(user => {
                if (user && user.token) {
                    this.userService.setCurrentUser(user);
                }
                return user;
            }));
    }

    getOAuthUrl(provider: string): string {
        return `${this.appURL}/oauth/connect/${provider}`;
    }

    handleOAuthCallback(token: string): void {
        if (token) {
            this.userService.setCurrentUser({ token });
            this.router.navigate(['/']);
        } else {
            console.error('Token not received');
        }
    }

    isLoggedIn(): Observable<boolean> {
        return this.userService.currentUser$.pipe(
            map(user => user !== null)
        );
    }
}