import { Injectable } from '@angular/core';
import { Vehicle } from '../models/vehicle.model';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable, catchError, of, tap } from 'rxjs';
import { environment } from '../../environments/environment';
import { Brand } from '../models/brand.model';

@Injectable({
  providedIn: 'root'
})
export class CatalogService {

  private appURL = environment.appURL;

  constructor(private http: HttpClient) { }

  getVehicleList(filters: any): Observable<{ vehicles: Vehicle[], total: number }> {
    let params = new HttpParams();
    for (const key in filters) {
      if (filters[key]) {
        params = params.set(key, filters[key]);
      }
    }
    return this.http.get<{ vehicles: Vehicle[], total: number }>(`${this.appURL}/catalog`, { params }).pipe(
      tap(response => console.log(response)),
      catchError(error => {
        console.log(error);
        return of({ vehicles: [], total: 0 });
      })
    );
  }

  getBrandList(): Observable<Brand[]> {
    return this.http.get<Brand[]>(`${this.appURL}/catalog/brands`);
  }
}
