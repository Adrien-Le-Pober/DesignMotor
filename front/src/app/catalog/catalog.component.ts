import { Component } from '@angular/core';
import { Vehicle } from '../models/vehicle.model';
import { CatalogService } from './catalog.service';
import { CommonModule } from '@angular/common';
import { VehicleComponent } from '../vehicle/vehicle.component';

@Component({
  selector: 'app-catalog',
  standalone: true,
  imports: [
    CommonModule,
    VehicleComponent
  ],
  templateUrl: 'catalog.component.html',
  styles: ``
})
export class CatalogComponent {
  public vehicleList: Vehicle[];

  constructor(
    private catalogService: CatalogService
  ) { }

  ngOnInit() {
    this.catalogService.getVehicleList()
      .subscribe(vehicleList => {
        this.vehicleList = vehicleList;
      });
  }
}
