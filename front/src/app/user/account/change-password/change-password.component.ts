import { Component, ViewChild } from '@angular/core';
import { UserService } from '../../user.service';
import { FormsModule, NgForm } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { CompareValidatorDirective } from '../../../directive/compare-validator.directive';
import { Subject, takeUntil } from 'rxjs';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-change-password',
  standalone: true,
  imports: [FormsModule, CommonModule, CompareValidatorDirective, RouterModule],
  templateUrl: 'change-password.component.html',
  styleUrl: 'change-password.component.scss'
})
export class ChangePasswordComponent {
  private unsubscribe$ = new Subject<void>();
  currentPassword: string = '';
  newPassword: string = '';
  confirmPassword: string = '';
  isCurrentPasswordValid: boolean = false;
  errorMessage: string = '';
  successMessage: string = '';
  isRequestPending: boolean = false;

  @ViewChild('passwordForm') passwordForm: NgForm;

  constructor(private userService: UserService) {}

  verifyCurrentPassword() {
    this.isRequestPending = true;
    this.successMessage = '';
    this.errorMessage = '';
    this.userService.verifyPassword(this.currentPassword)
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe({
        next: validPassword => {
          this.isCurrentPasswordValid = validPassword;
          this.isRequestPending = false;
        },
        error: error => {
          this.isCurrentPasswordValid = error.error.validPassword;
          this.errorMessage = 'Mot de passe invalide';
          this.isRequestPending = false;
        }
      });
  }

  changePassword() {
    this.isRequestPending = true;
    this.successMessage = '';
    this.errorMessage = '';
    this.userService.changePassword(this.currentPassword, this.newPassword)
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe({
        next: response => {
          this.successMessage = response.successMessage;
          this.passwordForm.resetForm();
          this.isRequestPending = false;
        },
        error: error => {
          this.errorMessage = error.error.errorMessage;
          this.isRequestPending = false;
        }
      });
  }

  ngOnDestroy(): void {
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
  }
}
