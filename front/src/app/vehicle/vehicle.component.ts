import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Vehicle } from '../models/vehicle.model';
import { VehicleService } from './vehicle.service';
import { RouterModule } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';

@Component({
  selector: 'app-vehicle',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
  ],
  templateUrl: 'vehicle.component.html',
  styleUrl: 'vehicle.component.scss'
})
export class VehicleComponent {
  private unsubscribe$ = new Subject<void>();
  private timeoutId: any;
  public loading: boolean = false;
  public videoUrl: string|null;

  @Input() vehicle: Vehicle;

  constructor(
    private vehicleService: VehicleService
  ) { }

  loadVideo(vehicleId: number) {

    if(!this.videoUrl) {
      this.loading = true;
    }

    this.timeoutId = setTimeout(() => {
      this.vehicleService.getVideo(vehicleId)
        .pipe(takeUntil(this.unsubscribe$))
        .subscribe((response: Blob) => {
          // Convertir la réponse en URL de données
          const reader = new FileReader();
          reader.onload = () => {
            this.loading = false;
            this.videoUrl = reader.result as string;
          };
          reader.readAsDataURL(response);
        })
    }, 1000);
  }

  resetMedia() {
    clearTimeout(this.timeoutId);
    this.loading = false;
    this.videoUrl = null;
  }

  hideLoading() {
    this.loading = false;
  }

  cancelRequest() {
    // Annuler la requête en cours si l'utilisateur retire sa souris
    clearTimeout(this.timeoutId);
  }

  ngOnDestroy() {
    clearTimeout(this.timeoutId);
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
}
}
