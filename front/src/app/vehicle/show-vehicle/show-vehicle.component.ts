import { Component, Version } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { VehicleService } from '../vehicle.service';
import { CommonModule } from '@angular/common';
import { VehicleDescription } from '../../models/vehicle-description.model';

@Component({
  selector: 'app-show-vehicle',
  standalone: true,
  imports: [CommonModule],
  templateUrl: 'show-vehicle.component.html',
  styles: ``
})
export class ShowVehicleComponent {
  public vehicle: VehicleDescription|undefined;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private vehicleService: VehicleService
  ) { }

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    const vehicleId: number|null = id !== null ? +id : null;

    if (vehicleId !== null) {
      this.vehicleService.fetchVehicleById(vehicleId)
        .subscribe(vehicle => this.vehicle = vehicle);
    } else {
      this.router.navigate(['/404']);
    }
  }
}
