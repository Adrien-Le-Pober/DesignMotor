import { Injectable } from '@angular/core';
import { Vehicle } from '../models/vehicle.model';
import { BehaviorSubject, Observable } from 'rxjs';

interface CartItem {
  product: Vehicle;
  quantity: number;
}

@Injectable({
  providedIn: 'root'
})
export class CartService {
  private items: CartItem[] = [];
  private itemsSubject = new BehaviorSubject<CartItem[]>(this.items);

  items$: Observable<CartItem[]> = this.itemsSubject.asObservable();

  addToCart(product: Vehicle) {
    const existingItem = this.items.find(item => item.product.id === product.id);

    if (existingItem) {
      existingItem.quantity += 1;
    } else {
      this.items.push({ product, quantity: 1 });
    }

    this.updateCart();
  }

  removeFromCart(productId: number) {
    this.items = this.items.filter(item => item.product.id !== productId);
    this.updateCart();
  }

  clearCart() {
    this.items = [];
    this.updateCart();
  }

  getItems(): CartItem[] {
    return this.items;
  }

  updateQuantity(productId: number, quantity: number) {
    const item = this.items.find(item => item.product.id === productId);

    if (item) {
      item.quantity = quantity;
      if (item.quantity <= 0) {
        this.removeFromCart(productId);
      }
      this.updateCart();
    }
  }

  private updateCart() {
    this.itemsSubject.next([...this.items]);
  }
}
