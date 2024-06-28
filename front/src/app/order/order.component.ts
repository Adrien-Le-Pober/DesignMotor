import { Component } from '@angular/core';
import { UserService } from '../user/user.service';
import { LoginComponent } from '../auth/login/login.component';
import { EditProfileComponent } from '../user/account/edit-profile/edit-profile.component';
import { CartComponent } from '../cart/cart.component';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-order',
  standalone: true,
  imports: [
    CommonModule,
    LoginComponent,
    EditProfileComponent,
    CartComponent
  ],
  templateUrl: './order.component.html',
  styleUrl: './order.component.scss'
})
export class OrderComponent {
  isLoggedIn = false;
  currentStep: number = 0;

  constructor(private userService: UserService) { }

  ngOnInit(): void {
    this.userService.currentUser$.subscribe(user => {
      this.isLoggedIn = !!user;
      if (this.isLoggedIn) {
        this.currentStep = 1;
      }
    });
  }

  onEditProfileComplete(): void {
    this.currentStep = 2;
  }

  onCartComplete(): void {
    this.currentStep = 3;
  }

  onPaymentComplete(): void {
    this.currentStep = 4;
  }
}
