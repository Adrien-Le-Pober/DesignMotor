import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { environment } from '../../environments/environment.development';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private currentUserSubject = new BehaviorSubject<string | null>(localStorage.getItem('currentUser'));
  currentUser$ = this.currentUserSubject.asObservable();
  private appURL = environment.appURL;

  constructor(private http: HttpClient) { }

  get currentUserValue(): any {
    const currentUserString = localStorage.getItem('currentUser');
    return currentUserString ? JSON.parse(currentUserString) : null;
  }

  register(email: string, password: string): Observable<any> {
    return this.http.post<any>(`${this.appURL}/register`, { email, password });
  }

  setCurrentUser(user: string | null) {
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
}
