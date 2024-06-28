import { Component, Input, SimpleChanges } from '@angular/core';
import { AuthService } from '../auth.service';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
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

  @Input() redirectUrl: string;

  email: string;
  password: string;
  errorMessage: string;
  successMessage: string;
  isRequestPending: boolean = false;
  isResendConfirmationEmail: boolean = false;

  constructor(
    private authService: AuthService,
    private router: Router,
    private route: ActivatedRoute
  ) {}

  ngOnInit(): void {
    const storedRedirectUrl = sessionStorage.getItem('redirectUrl');
    if (storedRedirectUrl) {
      this.redirectUrl = storedRedirectUrl;
      sessionStorage.removeItem('redirectUrl');
    }

    this.route.queryParams
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe(params => {
        if (params['errorMessage']) {
          this.errorMessage = params['errorMessage'];
        }
        if (params['successMessage']) {
          this.successMessage = params['successMessage'];
        }
        if (params['token']) {
          this.authService.handleOAuthCallback(params['token'], this.redirectUrl);
        }
      });
  }

  login() {
    this.isRequestPending = true;
    this.errorMessage = '';
    this.successMessage = '';
    this.authService.login(this.email, this.password)
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe({
        next: () => {
            this.isRequestPending = false;
            const redirect = this.redirectUrl || '/';
            this.router.navigate([redirect]);
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

  loginWithGoogle() {
    if (this.redirectUrl) {
      sessionStorage.setItem('redirectUrl', this.redirectUrl);
    }
    window.location.href = `${this.authService.getOAuthUrl('google')}`;
  }

  ngOnDestroy(): void {
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
  }
}
