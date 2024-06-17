import { Component } from '@angular/core';
import { UserService } from '../../user.service';
import { Subject, takeUntil } from 'rxjs';
import { Router, RouterModule } from '@angular/router';

@Component({
  selector: 'app-delete-account',
  standalone: true,
  imports: [RouterModule],
  templateUrl: 'delete-account.component.html',
  styles: ``
})
export class DeleteAccountComponent {

  private unsubscribe$ = new Subject<void>();
  isRequestPending: boolean = false;

  constructor(
    private userService: UserService,
    private router: Router
  ) { }

  onDelete() {
    this.isRequestPending = true;
    this.userService.deleteAccount()
      .pipe(takeUntil(this.unsubscribe$))
      .subscribe(() => {
        this.isRequestPending = false;
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
          backdrop.remove();
        }
        this.router.navigate(['/connexion']);
      })
  }

  ngOnDestroy(): void {
    this.unsubscribe$.next();
    this.unsubscribe$.complete();
  }
}
