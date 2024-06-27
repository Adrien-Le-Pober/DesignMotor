import { Component, ElementRef, HostListener, ViewChild } from '@angular/core';
import { RouterModule } from '@angular/router';
import { UserService } from '../user/user.service';
import { CommonModule } from '@angular/common';
import { CurrentUser } from '../interfaces/current-user.interface';
import { VehicleService } from '../vehicle/vehicle.service';
import { FormsModule } from '@angular/forms';
import { Observable, map } from 'rxjs';
import { CartService } from '../cart/cart.service';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [RouterModule, CommonModule, FormsModule],
  templateUrl: 'navbar.component.html',
  styleUrl: 'navbar.component.scss'
})
export class NavbarComponent {
  currentUser: CurrentUser|null = null;
  searchResults: any[] = [];
  searchTerm: string = '';
  isCartEmpty$: Observable<boolean>;

  @ViewChild('hamburger', { static: true }) hamburgerBtn!: ElementRef;
  @ViewChild('navMenu', { static: true }) navMenu!: ElementRef;
  @ViewChild('menu', { static: true }) menu!: ElementRef;
  @ViewChild('menuLinks', { static: true }) menuLinks!: ElementRef;

  private dropdownBtns: NodeListOf<HTMLElement>;
  private dropdowns: NodeListOf<HTMLElement>;
  private links: NodeListOf<HTMLAnchorElement>;

  constructor(
    private userService: UserService,
    private vehicleService: VehicleService,
    private cartService: CartService
  ) { }

  ngOnInit() {
    this.userService.currentUser$.subscribe((user: CurrentUser | null) => {
      this.currentUser = user;
    });
    this.isCartEmpty$ = this.cartService.items$.pipe(
      map(items => items.length === 0)
    );
  }

  ngAfterViewInit() {
    this.dropdownBtns = this.navMenu.nativeElement.querySelectorAll('.dropdown-btn');
    this.dropdowns = this.navMenu.nativeElement.querySelectorAll('.dropdown');
    this.links = this.navMenu.nativeElement.querySelectorAll('.dropdown a, .menu-bar a');

    this.addEventListeners();
    this.setMenuPosition();
  }

  ngOnDestroy() {
    this.removeEventListeners();
  }

  @HostListener('window:resize', ['$event'])
  onResize(event: any) {
    if (window.innerWidth > 1200) {
      this.menu.nativeElement.classList.remove('show');
      this.menu.nativeElement.style.top = 0;
    } else {
      this.setMenuPosition();
    }
  }

  logout() {
    this.userService.clearCurrentUser();
  }

  setMenuPosition() {
    if (window.innerWidth <= 1200) {
      const navMenuHeight = this.navMenu.nativeElement.clientHeight;
      this.menu.nativeElement.style.top = `${navMenuHeight}px`;
    }
  }

  addEventListeners() {
    this.dropdownBtns.forEach((btn) => {
      btn.addEventListener('click', this.toggleDropdown.bind(this));
    });

    this.links.forEach((link) => {
      link.addEventListener('click', this.closeMenus.bind(this));
      link.addEventListener('click', this.closeMenu.bind(this));
    });

    document.documentElement.addEventListener('click', this.closeMenus.bind(this));
    document.addEventListener('keydown', this.handleEscapeKey.bind(this));
    this.hamburgerBtn.nativeElement.addEventListener('click', this.toggleHamburger.bind(this));
  }

  removeEventListeners() {
    this.dropdownBtns.forEach((btn) => {
      btn.removeEventListener('click', this.toggleDropdown.bind(this));
    });

    this.links.forEach((link) => {
      link.removeEventListener('click', this.closeMenus.bind(this));
      link.removeEventListener('click', this.closeMenu.bind(this));
    });

    document.documentElement.removeEventListener('click', this.closeMenus.bind(this));
    document.removeEventListener('keydown', this.handleEscapeKey.bind(this));
    this.hamburgerBtn.nativeElement.removeEventListener('click', this.toggleHamburger.bind(this));
  }

  setAriaExpandedFalse() {
    this.dropdownBtns.forEach((btn) => btn.setAttribute('aria-expanded', 'false'));
  }

  closeDropdownMenu() {
    this.dropdowns.forEach((drop) => drop.classList.remove('active'));
  }

  toggleDropdown(event: Event) {
    const btn = event.currentTarget as HTMLElement;
    const dropdownId = (btn.dataset as Record<string, string>)['dropdown'];
    const dropdownElement = document.getElementById(dropdownId)!;

    dropdownElement.classList.toggle('active');
    this.dropdowns.forEach((drop) => {
      if (drop.id !== dropdownId) {
        drop.classList.remove('active');
      }
    });
    event.stopPropagation();
    btn.setAttribute('aria-expanded', btn.getAttribute('aria-expanded') === 'false' ? 'true' : 'false');
  }

  closeMenus() {
    this.closeDropdownMenu();
    this.setAriaExpandedFalse();
  }

  closeMenu() {
    if (this.menu.nativeElement.classList.contains('show')) {
      this.menu.nativeElement.classList.remove('show');
    }
  }

  handleEscapeKey(event: KeyboardEvent) {
    if (event.key === 'Escape') {
      this.closeMenus();
    }
  }

  toggleHamburger() {
    this.menu.nativeElement.classList.toggle('show');
  }

  // Search

  onSearchChange(event: Event): void {
    const inputElement = event.target as HTMLInputElement;
    const searchValue = inputElement.value;

    if (searchValue && searchValue.length > 2) {
      this.vehicleService.searchVehiclesOnNavbar(searchValue)
        .subscribe(results => {
          this.searchResults = results;
        });
    } else {
      this.searchResults = [];
    }
  }

  @HostListener('document:click', ['$event'])
  onDocumentClick(event: MouseEvent): void {
    const target = event.target as HTMLElement;
    const isClickedInside = target.closest('.search') !== null;

    if (!isClickedInside) {
      this.clearSearch();
    }
  }

  clearSearch(): void {
    this.searchTerm = '';
    this.searchResults = [];
  }
}
