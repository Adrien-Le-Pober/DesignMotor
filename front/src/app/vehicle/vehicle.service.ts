import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment.development';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class VehicleService {

  private appURL = environment.appURL;

  constructor(private http: HttpClient) { }

  getVideo(vehicleId: number): Observable<Blob> {
    return this.http.get(`${this.appURL}/vehicle/${vehicleId}/video`, { responseType: 'blob' });
  }
}
