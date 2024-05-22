import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Vehicle } from '../models/vehicle.model';
import { VehicleService } from './vehicle.service';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-vehicle',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
  ],
  templateUrl: 'vehicle.component.html',
  styles: ``
})
export class VehicleComponent {
  @Input() vehicle: Vehicle;

  private timeoutId: any;
  public loading: boolean = false;
  public videoUrl: string|null;

  constructor(
    private vehicleService: VehicleService
  ) { }

  loadVideo(vehicleId: number) {

    if(!this.videoUrl) {
      this.loading = true;
    }

    this.timeoutId = setTimeout(() => {
      this.vehicleService.getVideo(vehicleId)
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
}
}
