import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';
import { UserService } from '../user/user.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [RouterModule, CommonModule],
  templateUrl: 'navbar.component.html',
  styles: ``
})
export class NavbarComponent {
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
