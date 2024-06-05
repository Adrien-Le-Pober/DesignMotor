import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-page-not-found',
  standalone: true,
  imports: [
    RouterModule
  ],
  template: `
    <div>
      <img
        srcset="../../assets/images/page-not-found/page-not-found-mobile.jpg 500w,
                ../../assets/images/page-not-found/page-not-found-widescreen.jpg 1064w"
        sizes="(max-width: 576px) 500px, 1064px"
        src="../../assets/images/page-not-found/page-not-found-widescreen.jpg"
        alt="Page introuvable"
      />
    </div>
  `,
  styles: `
    div {
      display: flex;
      justify-content: center;
      background-color: #000;
      min-height: 100vh;
    }

    img {
      width: 100%;
      max-width: 1064px;
      object-fit: cover;
    }
  `
})
export class PageNotFoundComponent {

}
