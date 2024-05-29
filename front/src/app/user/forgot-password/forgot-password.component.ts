import { Component } from '@angular/core';
import { ResetPasswordService } from '../reset-password/reset-password.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-forgot-password',
  standalone: true,
  imports: [FormsModule, CommonModule],
  templateUrl: 'forgot-password.component.html',
  styles: ``
})
export class ForgotPasswordComponent {
  email: string;
  isRequestPending: boolean = false;
  errorMessage: string|null;
  successMessage: string|null;

  constructor(private resetPasswordService: ResetPasswordService) {}

  requestReset() {
    this.isRequestPending = true;
    this.resetPasswordService.requestReset(this.email).subscribe({
      next: (response) => {
        this.successMessage = response.message;
        this.errorMessage = null;
        this.isRequestPending = false;
      },
      error: (error) => {
        this.errorMessage = error.error.error;
        this.successMessage = null;
        this.isRequestPending = false;
      }
    });
  }
}
