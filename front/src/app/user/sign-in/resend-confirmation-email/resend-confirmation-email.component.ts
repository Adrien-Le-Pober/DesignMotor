import { Component } from '@angular/core';
import { UserService } from '../../user.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-resend-confirmation-email',
  standalone: true,
  imports: [FormsModule, CommonModule],
  templateUrl: 'resend-confirmation-email.component.html',
  styles: ``
})
export class ResendConfirmationEmailComponent {
  email: string;
  message: string;
  isRequestPending: boolean = false;

  constructor(private authService: UserService) {}

  resendConfirmationEmail() {
    this.isRequestPending = true;
    this.authService.resendConfirmationEmail(this.email).subscribe({
      next: (response) => {
        this.message = response.message;
        this.isRequestPending = false;
      },
      error: (error) => {
        this.message = error.error.message || 'Une erreur est survenue.';
        this.isRequestPending = false;
      }
    });
  }
}
