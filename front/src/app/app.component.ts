import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthService } from './auth/auth.service';
import { UserService } from './user/user.service';
import { NavbarComponent } from './navbar/navbar.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    NavbarComponent
  ],
  templateUrl: './app.component.html',
  styles: [],
})
export class AppComponent {
  currentUser: string|null = null;

  constructor(private userService: UserService) { }

  ngOnInit() {
    this.userService.currentUser$.subscribe((user: string | null) => {
      this.currentUser = user;
    });
  }

  logout() {
    this.userService.clearCurrentUser();
  }
}
