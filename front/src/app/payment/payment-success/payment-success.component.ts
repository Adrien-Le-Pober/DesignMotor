import { Component } from '@angular/core';
import { CartService } from '../../cart/cart.service';

@Component({
  selector: 'app-payment-success',
  standalone: true,
  imports: [],
  template: `
    <div class="container d-flex flex-column align-items-center">
      <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-check2-circle my-5 text-success" viewBox="0 0 16 16">
          <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/>
          <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>
      </svg>
      <p>Votre paiement à bien été validé.</p>
      <p class="text-center">Un email vient de vous être envoyé avec la facture.</p>
      <p class="text-center">Vous pouvez consultez l'historique de vos commandes depuis la rubrique 'mon compte'.</p>
      <br>
      <br>
      <p>Merci pour votre confiance, à bientôt !</p>
    </div>
  `,
  styles: `
    svg {
      width: 256px;
      height: 256px;
    }

    @media screen and (max-width: 776px) {
      svg {
        width: 128px;
        height: 128px;
      }
    }
  `
})
export class PaymentSuccessComponent {

  constructor(
    private cartService: CartService,
  ) {}

  ngOnInit(): void {
    this.cartService.clearCart();
  }
}
