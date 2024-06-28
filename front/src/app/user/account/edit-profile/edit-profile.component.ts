import { Component, EventEmitter, Input, Output } from '@angular/core';
import { UserService } from '../../user.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Subject, takeUntil } from 'rxjs';

@Component({
  selector: 'app-edit-profile',
  standalone: true,
  imports: [FormsModule, CommonModule],
  templateUrl: 'edit-profile.component.html',
  styleUrl: 'edit-profile.component.scss'
})
export class EditProfileComponent {
  private unsubscribe$ = new Subject<void>();
  
  @Input() allFieldsRequired = false;
  @Output() formSubmitted = new EventEmitter<void>();

  email: string = '';
  firstname: string = '';
  lastname: string = '';
  phone: string = '';

  successMessage: string = '';
  errorMessage: string = '';
  isRequestPending: boolean = false;
  isLoading: boolean = false;

  constructor(private userService: UserService) { }

  ngOnInit() {
    this.isLoading = true;
    this.isRequestPending = true;
    this.userService.getUserInfo()
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe(userData => {
        this.email = userData.email;
        this.firstname = userData.firstname;
        this.lastname = userData.lastname;
        this.phone = userData.phone;
        this.isLoading = false;
        this.isRequestPending = false;
      });
  }

  onSubmit() {
    this.isRequestPending = true;
    this.successMessage = '';
    this.errorMessage = '';
    this.userService.editProfile(this.email, this.firstname, this.lastname, this.phone)
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe({
        next: (response) => {
          this.formSubmitted.emit();
          this.successMessage = response.successMessage;
          this.isRequestPending = false;
        },
        error: (error) => {
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
