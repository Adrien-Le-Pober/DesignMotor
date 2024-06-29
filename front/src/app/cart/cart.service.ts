import { Injectable } from '@angular/core';
import { Vehicle } from '../models/vehicle.model';
import { BehaviorSubject, Observable } from 'rxjs';
import { CartItem } from '../interfaces/cart-item.interface';


@Injectable({
  providedIn: 'root'
})
export class CartService {
  private itemsKey = 'cartItems'; // Clé pour stocker les articles dans localStorage
  private items: CartItem[] = this.loadCartItemsFromStorage();
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
    this.saveCartItemsToStorage();
  }

  removeFromCart(productId: number) {
    this.items = this.items.filter(item => item.product.id !== productId);
    this.updateCart();
    this.saveCartItemsToStorage();
  }

  clearCart() {
    this.items = [];
    this.updateCart();
    this.saveCartItemsToStorage();
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

  private loadCartItemsFromStorage(): CartItem[] {
    const itemsJson = localStorage.getItem(this.itemsKey);
    return itemsJson ? JSON.parse(itemsJson) : [];
  }

  private saveCartItemsToStorage() {
    localStorage.setItem(this.itemsKey, JSON.stringify(this.items));
  }
}
