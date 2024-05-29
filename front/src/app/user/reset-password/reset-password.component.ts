import { Component } from '@angular/core';
import { ResetPasswordService } from './reset-password.service';
import { ActivatedRoute } from '@angular/router';
import { CompareValidatorDirective } from '../../directive/compare-validator.directive';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-reset-password',
  standalone: true,
  imports: [CompareValidatorDirective, FormsModule, CommonModule],
  templateUrl: 'reset-password.component.html',
  styles: ``
})
export class ResetPasswordComponent {
  token: string|null;
  plainPassword: string;
  confirmPassword: string;
  isRequestPending: boolean = false;
  successMessage: string|null;
  errorMessage: string|null;

  constructor(
    private route: ActivatedRoute,
    private resetPasswordService: ResetPasswordService
  ) {}

  ngOnInit(): void {
    this.token = this.route.snapshot.paramMap.get('token');
  }

  resetPassword() {
    this.isRequestPending = true;
    this.resetPasswordService.resetPassword(this.token, this.plainPassword).subscribe({
      next: (response) => {
        console.log(response);
        this.successMessage = response.message;
        this.isRequestPending = false;
      },
      error: (error) => {
        console.log(error);
        this.errorMessage = error.error.error;
        this.isRequestPending = false;
      }
    });
  }
}
