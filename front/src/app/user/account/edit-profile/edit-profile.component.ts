import { Component } from '@angular/core';
import { UserService } from '../../user.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-edit-profile',
  standalone: true,
  imports: [FormsModule, CommonModule],
  templateUrl: 'edit-profile.component.html',
  styleUrl: 'edit-profile.component.scss'
})
export class EditProfileComponent {
  email: string = '';
  successMessage: string = '';
  errorMessage: string = '';
  isRequestPending: boolean = false;
  isLoading: boolean = false;

  constructor(private userService: UserService) { }

  ngOnInit() {
    this.isLoading = true;
    this.isRequestPending = true;
    this.userService.getUserInfo().subscribe(data => {
      this.email = data.email;
      this.isLoading = false;
      this.isRequestPending = false;
    });
  }

  onSubmit() {
    this.isRequestPending = true;
    this.successMessage = '';
    this.errorMessage = '';
    this.userService.editProfile(this.email).subscribe({
      next: (response) => {
        this.successMessage = response.successMessage;
        this.isRequestPending = false;
      },
      error: (error) => {
        this.errorMessage = error.error.errorMessage;
        this.isRequestPending = false;
      }
    });
  }
}
