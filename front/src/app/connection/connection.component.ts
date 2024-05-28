import { Component } from '@angular/core';
import { LoginComponent } from '../auth/login/login.component';
import { SignInComponent } from '../user/sign-in/sign-in.component';
import { ActivatedRoute } from '@angular/router';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-connection',
  standalone: true,
  imports: [LoginComponent, SignInComponent, CommonModule],
  templateUrl: './connection.component.html',
  styles: ``
})
export class ConnectionComponent {
  message: string |null;

  constructor(private route: ActivatedRoute) { }

  ngOnInit(): void {
    this.route.queryParams.subscribe(params => {
      this.message = params['message'];
    });
  }
}
