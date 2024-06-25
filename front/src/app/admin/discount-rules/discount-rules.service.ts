import { Injectable } from '@angular/core';
import { environment } from '../../../environments/environment';
import { HttpClient } from '@angular/common/http';
import { DiscountRule } from '../../models/discount-rule.model';
import { Observable } from 'rxjs';
import { Brand } from '../../models/brand.model';
import { DayOfWeek } from '../../interfaces/day-of-week.interface';

@Injectable({
  providedIn: 'root'
})
export class DiscountRulesService {

  private appURL = environment.appURL;

  constructor(private http: HttpClient) { }

  getDaysOfWeek(): DayOfWeek[] {
    return [
      {day: 'Monday', localDay: 'Lundi'},
      {day: 'Tuesday', localDay: 'Mardi'},
      {day: 'Wednesday', localDay: 'Mercredi'},
      {day: 'Thursday', localDay: 'Jeudi'},
      {day: 'Friday', localDay: 'Vendredi'},
      {day: 'Saturday', localDay: 'Samedi'},
      {day: 'Sunday', localDay: 'Dimanche'},
    ];
  }

  getBrandList(): Observable<Brand[]> {
    return this.http.get<Brand[]>(`${this.appURL}/catalog/brands`);
  }

  createDiscountRule(discountRule: DiscountRule): Observable<any> {
    return this.http.post<any>(`${this.appURL}/new-discount-rule`, discountRule);
  }

  getDiscountRules(): Observable<DiscountRule[]> {
    return this.http.get<DiscountRule[]>(`${this.appURL}/discount-rules`);
  }

  updateDiscountRule(discountRule: DiscountRule): Observable<any> {
    return this.http.put<any>(`${this.appURL}/edit-discount-rule/${discountRule.id}`, discountRule);
  }

  deleteDiscountRule(id: number): Observable<any> {
    return this.http.delete<any>(`${this.appURL}/delete-discount-rule/${id}`);
  }
}
