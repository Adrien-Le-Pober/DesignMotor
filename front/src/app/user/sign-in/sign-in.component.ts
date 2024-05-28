import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { UserService } from '../user.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-sign-in',
  standalone: true,
  imports: [FormsModule, CommonModule],
  templateUrl: './sign-in.component.html',
  styles: ``
})
export class SignInComponent {
  email: string = '';
  password: string = '';
  errorMessage: string = '';
  successMessage: string = '';
  isRequestPending = false;

  constructor(private userService: UserService, private router: Router) {}

  signIn() {
    this.isRequestPending = true;
    this.userService.register(this.email, this.password).subscribe({
      next: (response) => {
        this.successMessage = response.message;
        this.isRequestPending = false;
      },
      error: (error) => {
        this.errorMessage = error.error.errors.join(', ');
        this.isRequestPending = false;
      }
    });
  }
}
