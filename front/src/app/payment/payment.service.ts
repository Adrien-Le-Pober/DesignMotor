import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { CartItem } from '../interfaces/cart-item.interface';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class PaymentService {
  private appURL = environment.appURL;

  constructor(private http: HttpClient) {}

  createPayment(userEmail: string|undefined, cartItems: CartItem[]): Observable<{ paymentUrl: string }> {
    return this.http.post<{ paymentUrl: string }>(`${this.appURL}/payment/create`, { userEmail, cartItems });
  }
}
