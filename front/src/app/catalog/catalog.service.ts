import { Injectable } from '@angular/core';
import { Vehicle } from '../models/vehicle.model';
import { HttpClient } from '@angular/common/http';
import { Observable, catchError, of, tap } from 'rxjs';
import { environment } from '../../environments/environment.development';

@Injectable({
  providedIn: 'root'
})
export class CatalogService {

  private appURL = environment.appURL;

  constructor(private http: HttpClient) { }

  getVehicleList(): Observable<Vehicle[]> {
    return this.http.get<Vehicle[]>(`${this.appURL}/catalog`).pipe(
      tap(response => console.log(response)),
      catchError(error => {
        console.log(error);
        return of([]);
      })
    );
  }
}
