import { Component, ElementRef, HostListener, QueryList, ViewChild, ViewChildren } from '@angular/core';
import { RouterModule } from '@angular/router';
import { UserService } from '../user/user.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [RouterModule, CommonModule],
  templateUrl: 'navbar.component.html',
  styleUrl: 'navbar.component.scss'
})
export class NavbarComponent {
  currentUser: string|null = null;

  @ViewChild('hamburger', { static: true }) hamburgerBtn!: ElementRef;
  @ViewChild('navMenu', { static: true }) navMenu!: ElementRef;
  @ViewChild('menu', { static: true }) menu!: ElementRef;

  private dropdownBtns: NodeListOf<HTMLElement> = document.querySelectorAll(".dropdown-btn");
  private dropdowns: NodeListOf<HTMLElement> = document.querySelectorAll(".dropdown");
  private links: NodeListOf<HTMLAnchorElement> = document.querySelectorAll(".dropdown a");

  constructor(private userService: UserService) { }

  ngOnInit() {
    this.userService.currentUser$.subscribe((user: string | null) => {
      this.currentUser = user;
    });
  }

  ngAfterViewInit() {
    this.dropdownBtns = document.querySelectorAll('.dropdown-btn');
    this.dropdowns = document.querySelectorAll('.dropdown');
    this.links = document.querySelectorAll('.dropdown a');

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

  handleEscapeKey(event: KeyboardEvent) {
    if (event.key === 'Escape') {
      this.closeMenus();
    }
  }

  toggleHamburger() {
    this.menu.nativeElement.classList.toggle('show');
  }
}
