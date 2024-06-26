import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CartService } from './cart.service';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './cart.component.html',
  styleUrl: './cart.component.scss'
})
export class CartComponent {
  items$ = this.cartService.items$;

  @Input() useValidateCart = false;
  @Output() cartValidated = new EventEmitter<void>();

  constructor(private cartService: CartService) {}

  removeFromCart(productId: number): void {
    this.cartService.removeFromCart(productId);
  }

  updateQuantity(productId: number, quantity: number): void {
    this.cartService.updateQuantity(productId, quantity);
  }

  clearCart(): void {
    this.cartService.clearCart();
  }

  validateCart(): void {
    this.cartValidated.emit();
  }
}
