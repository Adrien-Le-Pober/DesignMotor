import { Component } from '@angular/core';
import { AuthService } from '../auth.service';
import { Router, RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Subject, takeUntil } from 'rxjs';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [FormsModule, CommonModule, RouterModule],
  templateUrl: './login.component.html',
  styleUrl: 'login.component.scss'
})
export class LoginComponent {
  private unsubscribe$ = new Subject<void>();
  email: string;
  password: string;
  errorMessage: string;
  isRequestPending: boolean = false;
  isResendConfirmationEmail: boolean = false;

  constructor(private authService: AuthService, private router: Router) {}

  login() {
    this.isRequestPending = true;
    this.authService.login(this.email, this.password)
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe({
        next: () => {
            this.isRequestPending = false;
            this.router.navigate(['/']);
        },
        error: (error) => {
            this.isRequestPending = false;
            if (error.status === 403) {
              this.errorMessage = error.error.message;
              this.isResendConfirmationEmail = true;
            } else {
                this.errorMessage = 'Email ou mot de passe invalide';
            }
        }
      });
  }

  ngOnDestroy(): void {
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
  }
}
