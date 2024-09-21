import { Component } from '@angular/core';
import { Vehicle } from '../models/vehicle.model';
import { CatalogService } from './catalog.service';
import { CommonModule } from '@angular/common';
import { VehicleComponent } from '../vehicle/vehicle.component';
import { Subject, takeUntil } from 'rxjs';
import { Brand } from '../models/brand.model';
import { FormsModule } from '@angular/forms';
import { LoaderComponent } from '../components/spinner/loader.component';

@Component({
  selector: 'app-catalog',
  standalone: true,
  imports: [
    CommonModule,
    VehicleComponent,
    FormsModule,
    LoaderComponent
  ],
  templateUrl: 'catalog.component.html',
  styles: ''
})
export class CatalogComponent {
  private unsubscribe$ = new Subject<void>();
  public vehicleList: Vehicle[];
  public totalVehicles: number = 0;
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
    this.catalogService.getVehicleList(this.filters)
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe(response => {
        this.vehicleList = response.vehicles;
        this.totalVehicles = response.total;
        this.isLoading = false;
      });
  }

  fetchBrands() {
    this.catalogService.getBrandList()
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe(brandList => {
        this.brandList = brandList;
      });
  }

  applyFilters() {
    this.fetchVehicles();
  }

  ngOnDestroy(): void {
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
  }
}
