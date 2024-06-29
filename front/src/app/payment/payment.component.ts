import { Component } from '@angular/core';
import { PaymentService } from './payment.service';
import { CartService } from '../cart/cart.service';
import { UserService } from '../user/user.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { StripePaymentComponent } from './stripe-payment/stripe-payment.component';

@Component({
  selector: 'app-payment',
  standalone: true,
  imports: [CommonModule, FormsModule, StripePaymentComponent],
  templateUrl: 'payment.component.html',
  styles: ``
})
export class PaymentComponent {
  paymentMethod: string = 'stripe';
  cartItems: any[] = [];
  
  constructor(
    private paymentService: PaymentService,
    private cartService: CartService,
    private userService: UserService,
  ) {}

  ngOnInit(): void {
    this.cartItems = this.cartService.getItems().map(item => ({
      productId: item.product.id,
      quantity: item.quantity
    }));
  }

  onPaymentInitiated() {
    const currentUser = this.userService.currentUserValue;
    if (currentUser) {
      const userEmail = this.userService.getUsernameFromToken(currentUser.token);
      this.paymentService.createPayment(userEmail, this.cartItems).subscribe(response => {
        window.location.href = response.paymentUrl;
      });
    } else {
      console.error('User not authenticated');
    }
  }
}
