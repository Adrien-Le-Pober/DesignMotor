import { Component } from '@angular/core';
import { UserService } from '../../user.service';
import { Subject, takeUntil } from 'rxjs';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-order-history',
  standalone: true,
  imports: [CommonModule],
  templateUrl: 'order-history.component.html',
  styles: ``
})
export class OrderHistoryComponent {
  private unsubscribe$ = new Subject<void>();

  orders: any[] = [];
  isLoading: boolean = false;

  constructor(private userService: UserService) {}

  ngOnInit() {
    this.isLoading = true;
    this.userService.getUserOrders()
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe(response => {
        this.orders = response.orders;
        this.isLoading = false;
      });
  }

  ngOnDestroy(): void {
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
  }
}
