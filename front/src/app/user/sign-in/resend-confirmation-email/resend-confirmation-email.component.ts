import { Component } from '@angular/core';
import { UserService } from '../../user.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Subject, takeUntil } from 'rxjs';

@Component({
  selector: 'app-resend-confirmation-email',
  standalone: true,
  imports: [FormsModule, CommonModule],
  templateUrl: 'resend-confirmation-email.component.html',
  styleUrl: 'resend-confirmation-email.component.scss'
})
export class ResendConfirmationEmailComponent {
  private unsubscribe$ = new Subject<void>();
  email: string;
  errorMessage: string;
  successMessage: string;
  isRequestPending: boolean = false;

  constructor(private authService: UserService) {}

  resendConfirmationEmail() {
    this.isRequestPending = true;
    this.errorMessage = '';
    this.successMessage = '';
    this.authService.resendConfirmationEmail(this.email)
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe({
        next: (response) => {
          this.successMessage = response.message;
          this.isRequestPending = false;
        },
        error: (error) => {
          this.errorMessage = error.error.message || 'Une erreur est survenue.';
          this.isRequestPending = false;
        }
      });
  }

  ngOnDestroy(): void {
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
  }
}
