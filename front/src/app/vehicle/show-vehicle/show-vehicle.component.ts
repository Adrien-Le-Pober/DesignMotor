import { Component, Version } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { VehicleService } from '../vehicle.service';
import { CommonModule } from '@angular/common';
import { VehicleDescription } from '../../models/vehicle-description.model';
import { Subject, takeUntil } from 'rxjs';
import { Vehicle } from '../../models/vehicle.model';
import { CartService } from '../../cart/cart.service';

@Component({
  selector: 'app-show-vehicle',
  standalone: true,
  imports: [CommonModule],
  templateUrl: 'show-vehicle.component.html',
  styleUrl: 'show-vehicle.component.scss'
})
export class ShowVehicleComponent {
  private unsubscribe$ = new Subject<void>();
  public isLoading: boolean = false;
  public vehicle: VehicleDescription|undefined;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private vehicleService: VehicleService,
    private cartService: CartService
  ) { }

  ngOnInit(): void {
    this.route.paramMap
    .pipe(takeUntil(this.unsubscribe$))
    .subscribe(params => {
      const idString = params.get('id');
      if (idString !== null) {
        const id = +idString;
        if (!isNaN(id)) {
          this.isLoading = true;
          this.loadVehicle(id);
        } else {
          this.router.navigate(['/404']);
        }
      } else {
        this.router.navigate(['/404']);
      }
    });
  }

  loadVehicle(vehicleId: number): void {
    this.isLoading = true;
    this.vehicleService.fetchVehicleById(vehicleId)
      .pipe(
        takeUntil(this.unsubscribe$)
      )
      .subscribe(vehicle => {
        this.vehicle = vehicle;
        this.isLoading = false;
      });
  }

  addToCart(product: Vehicle): void {
    this.cartService.addToCart(product);
    this.router.navigate(['/panier']);
  }

  ngOnDestroy(): void {
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
  }
}
