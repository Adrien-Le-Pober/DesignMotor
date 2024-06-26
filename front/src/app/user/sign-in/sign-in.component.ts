import { Component } from '@angular/core';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { UserService } from '../user.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Subject, takeUntil } from 'rxjs';

@Component({
  selector: 'app-sign-in',
  standalone: true,
  imports: [FormsModule, CommonModule, RouterModule],
  templateUrl: './sign-in.component.html',
  styleUrl: 'sign-in.component.scss'
})
export class SignInComponent {
  private unsubscribe$ = new Subject<void>();
  email: string = '';
  password: string = '';
  rgpd: boolean = false;
  errorMessage: string = '';
  successMessage: string = '';
  isRequestPending = false;

  ngOnInit(): void {
    this.route.queryParams.subscribe(params => {
      this.errorMessage = params['errorMessage'];
      this.successMessage = params['successMessage'];
    });
  }

  constructor(private userService: UserService, private router: Router, private route: ActivatedRoute) {}

  signIn() {
    this.isRequestPending = true;
    this.userService.register(this.email, this.password, this.rgpd)
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe({
        next: (response) => {
          this.successMessage = response.message;
          this.isRequestPending = false;
        },
        error: (error) => {
          console.log(error);
          this.errorMessage = error.error.errors.join(', ');
          this.isRequestPending = false;
        }
      });
  }

  ngOnDestroy(): void {
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
  }
}
