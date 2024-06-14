import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class ResetPasswordService {
  private appURL = environment.appURL;

  constructor(private http: HttpClient) { }

  requestReset(email: string): Observable<any> {
    return this.http.post(`${this.appURL}/reset-password`, { email });
  }

  resetPassword(token: string|null, plainPassword: string): Observable<any> {
    return this.http.post(`${this.appURL}/reset-password/reset/${token}`, { plainPassword });
  }
}
