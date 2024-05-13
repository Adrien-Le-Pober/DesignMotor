import { Component } from '@angular/core';
import { Vehicle } from '../models/vehicle.model';
import { CatalogService } from './catalog.service';
import { CommonModule } from '@angular/common';
import { VehicleComponent } from '../vehicle/vehicle.component';
import { Subscription } from 'rxjs';

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
  private requestSubscription: Subscription | undefined;
  public vehicleList: Vehicle[];

  constructor(
    private catalogService: CatalogService
  ) { }

  ngOnInit() {
    this.requestSubscription = this.catalogService.getVehicleList()
      .subscribe(vehicleList => {
        this.vehicleList = vehicleList;
      });
  }

  ngOnDestroy(): void {
    if (this.requestSubscription) {
      this.requestSubscription.unsubscribe();
    }
  }
}
