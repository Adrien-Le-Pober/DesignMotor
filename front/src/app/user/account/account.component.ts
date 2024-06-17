import { Component } from '@angular/core';
import { EditProfileComponent } from './edit-profile/edit-profile.component';
import { ChangePasswordComponent } from './change-password/change-password.component';
import { DeleteAccountComponent } from './delete-account/delete-account.component';

@Component({
  selector: 'app-account',
  standalone: true,
  imports: [EditProfileComponent, ChangePasswordComponent, DeleteAccountComponent],
  templateUrl: 'account.component.html',
  styleUrl: 'account.component.scss'
})
export class AccountComponent {

}
