import { Component } from '@angular/core';
import { Vehicle } from '../models/vehicle.model';
import { VehicleService } from './vehicle.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-catalog',
  standalone: true,
  imports: [
    CommonModule
  ],
  templateUrl: 'catalog.component.html',
  styles: ``
})
export class CatalogComponent {
  public vehicleList: Vehicle[];

  constructor(
    private vehicleService: VehicleService
  ) { }

  ngOnInit() {
    this.vehicleService.getVehicleList()
      .subscribe(vehicleList => {
        this.vehicleList = vehicleList;
      });
  }
}
