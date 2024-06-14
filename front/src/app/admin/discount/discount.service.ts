import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Discount } from '../../models/discount.model';
import { Observable, catchError, of, tap } from 'rxjs';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class DiscountService {

  private appURL = environment.appURL;

  constructor(private http: HttpClient) { }

  getDiscountList(): Observable<Discount[]> {
    return this.http.get<Discount[]>(`${this.appURL}/discounts`).pipe(
      tap(response => console.log(response)),
      catchError(error => {
        console.log(error);
        return of([]);
      })
    );
  }

  addDiscount(discount: Discount): Observable<any> {
    const httpOptions = {
      headers: new HttpHeaders({ 'Content-Type': 'application/json' })
    };
    return this.http.post<string>(`${this.appURL}/new-discount`, discount, httpOptions);
  }

  cancelDiscountById(discountId: number): Observable<string|undefined> {
    return this.http.delete<string>(`${this.appURL}/cancel-discount/${discountId}`).pipe(
      tap(response => console.table(response)),
      catchError(error => {
        return of(undefined);
      })
    );
  }
}
