import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { environment } from '../../environments/environment';
import { UserService } from '../user/user.service';

@Injectable({ providedIn: 'root' })
export class AuthService {
    private appURL = environment.appURL;

    constructor(private http: HttpClient, private userService: UserService) { }

    login(email: string, password: string): Observable<any> {
        return this.http.post<any>(`${this.appURL}/login`, { email, password })
            .pipe(map(user => {
                if (user && user.token) {
                    this.userService.setCurrentUser(user);
                }
                return user;
            }));
    }

    isLoggedIn(): Observable<boolean> {
        return this.userService.currentUser$.pipe(
            map(user => user !== null)
        );
    }
}