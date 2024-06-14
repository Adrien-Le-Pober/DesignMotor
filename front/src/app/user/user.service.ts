import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable, tap } from 'rxjs';
import { environment } from '../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { jwtDecode } from "jwt-decode";
import { CurrentUser } from '../interfaces/current-user.interface';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private currentUserSubject = new BehaviorSubject<CurrentUser | null>(this.getCurrentUserFromLocalStorage());
  currentUser$ = this.currentUserSubject.asObservable();
  private appURL = environment.appURL;

  constructor(private http: HttpClient) { }

  get currentUserValue(): CurrentUser |null {
    return this.currentUserSubject.value;
  }

  register(email: string, password: string): Observable<any> {
    return this.http.post<any>(`${this.appURL}/register`, { email, password });
  }

  resendConfirmationEmail(email: string): Observable<any> {
    return this.http.post(`${this.appURL}/resend-confirmation-email`, { email });
  }

  setCurrentUser(user: CurrentUser | null) {
    if (user) {
      localStorage.setItem('currentUser', JSON.stringify(user));
    } else {
      localStorage.removeItem('currentUser');
    }
    this.currentUserSubject.next(user);
  }

  clearCurrentUser() {
    localStorage.removeItem('currentUser');
    this.currentUserSubject.next(null);
  }

  decodeToken(token: string): any {
    return jwtDecode(token);
  }

  getUserInfo(): Observable<any> {
    const token = this.getCurrentToken();
    const username = this.getUsernameFromToken(token);
    return this.http.get(`${this.appURL}/user/get-infos/${username}`);
  }

  verifyPassword(currentPassword: string): Observable<any> {
    const token = this.getCurrentToken();
    const username = this.getUsernameFromToken(token);
    return this.http.post<any>(`${this.appURL}/user/${username}/verify-password`, { currentPassword });
  }

  changePassword(currentPassword: string, newPassword: string): Observable<any> {
    const token = this.getCurrentToken();
    const username = this.getUsernameFromToken(token);
    return this.http.post(`${this.appURL}/user/${username}/change-password`, { currentPassword, newPassword });

  }

  editProfile(email: string): Observable<any> {
    const token = this.getCurrentToken();
    const username = this.getUsernameFromToken(token);
    if (username) {
      return this.http.post<{ token: string }>(`${this.appURL}/user/${username}/edit-profile`, { email }).pipe(
        tap(response => {
          if (response.token) {
            this.setCurrentUser({ ...this.currentUserValue, token: response.token });
          }
        })
      );
    } else {
      throw new Error('User is not authenticated');
    }
  }

  private getCurrentToken(): string {
    if (this.currentUserValue && this.currentUserValue.token) {
      return this.currentUserValue.token;
    }
    throw new Error('User is not authenticated');
  }

  private getUsernameFromToken(token: string): string|undefined {
    if(token) {
      const decodedToken = jwtDecode(token) as { username: string };
      return decodedToken.username;
    }
    return undefined;
  }

  private getCurrentUserFromLocalStorage(): CurrentUser | null {
    const currentUserString = localStorage.getItem('currentUser');
    return currentUserString ? JSON.parse(currentUserString) as CurrentUser : null;
  }
}
