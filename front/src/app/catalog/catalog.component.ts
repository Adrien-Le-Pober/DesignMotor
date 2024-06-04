import { Component } from '@angular/core';
import { Vehicle } from '../models/vehicle.model';
import { CatalogService } from './catalog.service';
import { CommonModule } from '@angular/common';
import { VehicleComponent } from '../vehicle/vehicle.component';
import { Subscription } from 'rxjs';
import { Brand } from '../models/brand.model';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-catalog',
  standalone: true,
  imports: [
    CommonModule,
    VehicleComponent,
    FormsModule,
  ],
  templateUrl: 'catalog.component.html',
  styleUrl: 'catalog.component.scss'
})
export class CatalogComponent {
  private requestSubscription: Subscription | undefined;
  public vehicleList: Vehicle[];
  public brandList: Brand[];
  public filters: any = {};
  public isLoading: boolean = false;

  constructor(
    private catalogService: CatalogService
  ) { }

  ngOnInit() {
    this.isLoading = true;
    this.fetchBrands();
    this.fetchVehicles();
  }

  fetchVehicles() {
    this.isLoading = true;
    this.requestSubscription = this.catalogService.getVehicleList(this.filters)
      .subscribe(vehicleList => {
        this.vehicleList = vehicleList;
        this.isLoading = false;
      });
  }

  fetchBrands() {
    this.catalogService.getBrandList()
      .subscribe(brandList => {
        this.brandList = brandList;
      });
  }

  applyFilters() {
    this.fetchVehicles();
  }

  ngOnDestroy(): void {
    if (this.requestSubscription) {
      this.requestSubscription.unsubscribe();
    }
  }
}
