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

  register(email: string, password: string, rgpd: boolean): Observable<any> {
    return this.http.post<any>(`${this.appURL}/register`, { email, password, rgpd });
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

  editProfile(email: string, firstname: string|null, lastname: string|null, phone: string|null): Observable<any> {
    const token = this.getCurrentToken();
    const username = this.getUsernameFromToken(token);
    if (username) {
      return this.http.post<{ token: string }>(`${this.appURL}/user/${username}/edit-profile`, { email, firstname, lastname, phone }).pipe(
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

  deleteAccount(): Observable<any> {
    const token = this.getCurrentToken();
    const username = this.getUsernameFromToken(token);
    if (username) {
      return this.http.delete(`${this.appURL}/user/${username}/delete-account`).pipe(
        tap(() => this.clearCurrentUser())
      );
    } else {
      throw new Error('User is not authenticated');
    }
  }

  getUsernameFromToken(token: string): string|undefined {
    if(token) {
      const decodedToken = jwtDecode(token) as { username: string };
      return decodedToken.username;
    }
    return undefined;
  }

  private getCurrentToken(): string {
    if (this.currentUserValue && this.currentUserValue.token) {
      return this.currentUserValue.token;
    }
    throw new Error('User is not authenticated');
  }

  private getCurrentUserFromLocalStorage(): CurrentUser | null {
    const currentUserString = localStorage.getItem('currentUser');
    return currentUserString ? JSON.parse(currentUserString) as CurrentUser : null;
  }
}
